<?php
namespace Implantation\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Implantation\Controller\DeploymentScheduleController;
use Implantation\Service\DeploymentScheduleManager;
use Quiz\Service\QuestionFieldFilledValueManager;


/**
 * This is the factory for IndexController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class DeploymentScheduleControllerFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

        $entityManager                   = $container->get('doctrine.entitymanager.orm_default');
        $deploymentScheduleManager       = $container->get(DeploymentScheduleManager::class);
        $questionFieldFilledValueManager = $container->get(QuestionFieldFilledValueManager::class);
        $authService                     = $container->get(\Zend\Authentication\AuthenticationService::class);

        // Instantiate the controller and inject dependencies
        return new DeploymentScheduleController($entityManager,
                                                $deploymentScheduleManager,
                                                $questionFieldFilledValueManager,
                                                $authService);
    }
}