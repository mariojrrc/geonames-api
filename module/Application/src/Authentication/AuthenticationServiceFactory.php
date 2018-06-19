<?php
/**
 * @see \ZF\MvcAuth\Factory\AuthenticationServiceFactory
 */

namespace Application\Authentication;

use Interop\Container\ContainerInterface;
use Zend\Authentication\Storage\NonPersistent;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class AuthenticationServiceFactory implements FactoryInterface
{

    /**
     * Create and return an AuthenticationService instance.
     *
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param null|array $options
     * @return AuthenticationService
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $config = $container->get('config');
        if (!isset($config['backend']['api'])) {
            throw new \RuntimeException('Api configuration not found.');
        }

        $service = new AuthenticationService($container->get(NonPersistent::class));
        $service->setConfig($config['backend']['api']);
        return $service;
    }

    /**
     * Create and return an AuthenticationService instance (v2).
     *
     * Provided for backwards compatibility; proxies to __invoke().
     *
     * @param ServiceLocatorInterface $container
     * @return AuthenticationService
     */
    public function createService(ServiceLocatorInterface $container)
    {
        return $this($container, AuthenticationService::class);
    }
}
