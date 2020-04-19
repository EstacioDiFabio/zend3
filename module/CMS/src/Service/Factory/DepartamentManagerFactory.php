<?php
namespace CMS\Service\Factory;

use Interop\Container\ContainerInterface;
use CMS\Service\DepartamentManager;

/**
 * This is the factory class for DepartamentManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class DepartamentManagerFactory
{
    /**
     * This method creates the DepartamentManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        return new DepartamentManager($entityManager);
    }
}
