<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2014 Zend Technologies USA Inc. (http://www.zend.com)
 */

namespace Application;

use Application\Authentication\AuthenticationService;
use Zend\Console\Request as ConsoleRequest;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Router\Http\RouteMatch;
use ZF\ApiProblem\ApiProblem;
use ZF\ApiProblem\ApiProblemResponse;

class Module
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function onBootstrap(MvcEvent $e)
    {
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(MvcEvent::EVENT_ROUTE, function (MvcEvent $event) {

            $routeMatch = $event->getRouteMatch();

            // No route match, this is a 404
            if (!$routeMatch instanceof RouteMatch) {
                return null;
            }

            // Verifica se esta no admin
            if ($routeMatch->getMatchedRouteName() === 'zf-apigility/ui'
                || $routeMatch->getMatchedRouteName() === 'home' // precisa para ser redirecionado ao admin
                || $routeMatch->getParam('is_apigility_admin_api')
            ) {
                return null;
            }
            // Não verifica login se for um chamada no console
            //@todo é possível pedir o login? deveria?
            if ($event->getRequest() instanceof ConsoleRequest) {
                return;
            }

            // TODO remover isso para fazer a validacao
            return;

            /**
             * @var $authentication AuthenticationService
             */
            if ($event->getRequest()->getHeaders()->has('Authorization')) {
                $authentication = $event->getApplication()->getServiceManager()->get('authentication');
                $return = $authentication->setTokenFromAuthorizationHeader($event->getRequest()->getHeader('Authorization'));
                if ($return instanceof ApiProblem) {
                    return new ApiProblemResponse($return);
                }
                $token = $authentication->getIdentity()->getToken(); // pra não quebrar...
            }
            if (empty($token)) {
                return new ApiProblemResponse(new ApiProblem(403, 'Token obrigatório'));
            }
        }, -100);

        /** attach Front layout for 404 errors */
        $eventManager->attach(MvcEvent::EVENT_DISPATCH_ERROR, function (MvcEvent $event) {
            if (null === $event->getRouteMatch() && $event->getResponse()->getStatusCode() === 404) {
                $event->setResponse(new ApiProblemResponse(new ApiProblem('404', 'Endpoint not found.')));
                // Isso interfere no new relic?
                // $event->stopPropagation(true);
            }
        });
    }
}
