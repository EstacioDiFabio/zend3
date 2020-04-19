<?php
namespace CMS\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use CMS\Controller\OrganizationController;
use CMS\Service\OrganizationManager;
use CMS\Service\OrganizationOfficeHourManager;

/**
 * This is the factory for OrganizationController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class OrganizationControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $organizationManager = $container->get(OrganizationManager::class);
        $organizationOfficeHourManager = $container->get(OrganizationOfficeHourManager::class);

        // Instantiate the controller and inject dependencies
        return new OrganizationController($entityManager, $organizationManager, $organizationOfficeHourManager);
    }
}