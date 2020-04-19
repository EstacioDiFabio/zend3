<?php
namespace Implantation\Service\Factory;

use Interop\Container\ContainerInterface;
use Implantation\Service\DeploymentScheduleManager;
use CMS\Service\WindelMail;

/**
 * This is the factory class for DeploymentScheduleManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class DeploymentScheduleManagerFactory
{
    /**
     * This method creates the DeploymentScheduleManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $windelMail = $container->get(WindelMail::class);

        return new DeploymentScheduleManager($entityManager, $windelMail);
    }
}
