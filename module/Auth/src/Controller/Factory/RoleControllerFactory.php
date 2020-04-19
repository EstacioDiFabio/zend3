<?php
namespace Auth\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Auth\Controller\RoleController;
use Auth\Service\RoleManager;

/**
 * This is the factory for RoleController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class RoleControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $roleManager = $container->get(RoleManager::class);

        // Instantiate the controller and inject dependencies
        return new RoleController($entityManager, $roleManager);
    }
}

