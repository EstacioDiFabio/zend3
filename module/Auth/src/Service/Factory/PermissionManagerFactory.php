<?php
namespace Auth\Service\Factory;

use Interop\Container\ContainerInterface;
use Auth\Service\PermissionManager;
use Auth\Service\RbacManager;

/**
 * This is the factory class for PermissionManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class PermissionManagerFactory
{
    /**
     * This method creates the UserManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $rbacManager = $container->get(RbacManager::class);

        return new PermissionManager($entityManager, $rbacManager);
    }
}
