<?php
declare(strict_types=1);
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
use Zend\Validator\AbstractValidator;
use Zend\I18n\Translator\TranslatorInterface;
use Zend\Mvc\I18n\Translator;
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

        // Define locale
        \Locale::setDefault('pt_BR');
        if (file_exists('./vendor/zendframework/zend-i18n-resources/languages/pt_BR/Zend_Validate.php')) {
            $translator = $e->getApplication()->getServiceManager()->get(TranslatorInterface::class);
            //Define o local onde se encontra o arquivo de tradução de mensagens
            $translator->addTranslationFile('phparray',
                './vendor/zendframework/zend-i18n-resources/languages/pt_BR/Zend_Validate.php');

            //Define o local (você também pode definir diretamente no método acima
            $translator->setLocale('pt_BR');
            //Define a tradução padrão do Validator
            AbstractValidator::setDefaultTranslator(new Translator($translator));
        }

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
