<?php
namespace CMS\Service\Factory;

use Interop\Container\ContainerInterface;
use CMS\Service\UserManager;
use Auth\Service\RoleManager;
use Auth\Service\PermissionManager;
use CMS\Service\WorkGroupManager;
use CMS\Service\DepartamentManager;
use CMS\Service\OrganizationManager;
use CMS\Service\WindelMail;

/**
 * This is the factory class for UserManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class UserManagerFactory
{
    /**
     * This method creates the UserManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $roleManager = $container->get(RoleManager::class);
        $permissionManager = $container->get(PermissionManager::class);
        $workGroupManager = $container->get(WorkGroupManager::class);
        $departamentManager = $container->get(DepartamentManager::class);
        $organizationManager = $container->get(OrganizationManager::class);
        $windelMail = $container->get(WindelMail::class);

        return new UserManager($entityManager, $roleManager, $permissionManager,
                               $workGroupManager, $departamentManager, $organizationManager,
                               $windelMail);
    }
}