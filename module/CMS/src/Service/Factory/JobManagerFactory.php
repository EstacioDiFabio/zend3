<?php
namespace CMS\Service\Factory;

use Interop\Container\ContainerInterface;
use CMS\Service\JobManager;

/**
 * This is the factory class for JobManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class JobManagerFactory
{
    /**
     * This method creates the JobManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        return new JobManager($entityManager);
    }
}
