<?php
namespace Implantation\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Implantation\Controller\ClientSchedulingController;
use Implantation\Service\DeploymentScheduleManager;


/**
 * This is the factory for ClientSchedulingController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class ClientSchedulingControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $deploymentScheduleManager = $container->get(DeploymentScheduleManager::class);
        $authService = $container->get(\Zend\Authentication\AuthenticationService::class);

        // Instantiate the controller and inject dependencies
        return new ClientSchedulingController($entityManager, $authService, $deploymentScheduleManager);
    }
}