<?php
namespace Base\Service\Factory;

use Interop\Container\ContainerInterface;
use Base\Service\ActivityManager;

/**
 * This is the factory class for ActivityManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class ActivityManagerFactory
{
    /**
     * This method creates the ActivityManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_log');
        return new ActivityManager($entityManager);
    }
}
