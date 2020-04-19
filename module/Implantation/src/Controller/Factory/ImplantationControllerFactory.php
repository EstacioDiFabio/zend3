<?php
namespace Implantation\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Implantation\Controller\ImplantationController;

/**
 * This is the factory for CMSController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class CMSControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        // Instantiate the controller and inject dependencies
        return new ImplantationController($entityManager);
    }
}