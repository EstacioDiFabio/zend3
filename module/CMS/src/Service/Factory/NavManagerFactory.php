<?php
namespace CMS\Service\Factory;

use Interop\Container\ContainerInterface;
use CMS\Service\NavManager;

/**
 * This is the factory class for NavManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class NavManagerFactory
{
    /**
     * This method creates the NavManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $authService = $container->get(\Zend\Authentication\AuthenticationService::class);
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        $viewHelperManager = $container->get('ViewHelperManager');
        $urlHelper = $viewHelperManager->get('url');

        return new NavManager($authService, $urlHelper, $entityManager);
    }
}
