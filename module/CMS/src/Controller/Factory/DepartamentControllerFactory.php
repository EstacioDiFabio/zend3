<?php
namespace CMS\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use CMS\Controller\DepartamentController;
use CMS\Service\DepartamentManager;

/**
 * This is the factory for DepartamentController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class DepartamentControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $departamentManager = $container->get(DepartamentManager::class);

        // Instantiate the controller and inject dependencies
        return new DepartamentController($entityManager, $departamentManager);
    }
}