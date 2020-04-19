<?php
namespace CMS\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use CMS\Controller\WorkGroupController;
use CMS\Service\WorkGroupManager;

/**
 * This is the factory for WorkGroupController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class WorkGroupControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $workGroupManager = $container->get(WorkGroupManager::class);

        // Instantiate the controller and inject dependencies
        return new WorkGroupController($entityManager, $workGroupManager);
    }
}