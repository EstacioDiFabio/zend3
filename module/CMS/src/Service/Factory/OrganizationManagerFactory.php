<?php
namespace CMS\Service\Factory;

use Interop\Container\ContainerInterface;
use CMS\Service\OrganizationManager;
use CMS\Service\OrganizationOfficeHourManager;

/**
 * This is the factory class for OrganizationManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class OrganizationManagerFactory
{
    /**
     * This method creates the OrganizationManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $organizationOfficeHourManager = $container->get(OrganizationOfficeHourManager::class);

        return new OrganizationManager($entityManager, $organizationOfficeHourManager);
    }
}
