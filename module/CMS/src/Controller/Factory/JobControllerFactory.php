<?php
namespace CMS\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use CMS\Controller\JobController;
use CMS\Service\JobManager;

/**
 * This is the factory for UserController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class JobControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $jobManager = $container->get(JobManager::class);

        // Instantiate the controller and inject dependencies
        return new JobController($entityManager, $jobManager);
    }
}