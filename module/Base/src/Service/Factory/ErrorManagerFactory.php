<?php
namespace Base\Service\Factory;

use Interop\Container\ContainerInterface;
use Base\Service\ErrorManager;

/**
 * This is the factory class for DeploymentScheduleManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class ErrorManagerFactory
{
    /**
     * This method creates the DeploymentScheduleManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_log');
        return new ErrorManager($entityManager);
    }
}
