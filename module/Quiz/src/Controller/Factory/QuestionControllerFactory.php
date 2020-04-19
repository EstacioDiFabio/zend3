<?php

namespace Quiz\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Quiz\Controller\QuestionController;
use Quiz\Service\QuestionManager;
use Quiz\Service\QuestionFieldManager;

/**
 * This is the factory for QuestionController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class QuestionControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $questionManager = $container->get(QuestionManager::class);
        $questionFieldManager = $container->get(QuestionFieldManager::class);

        // Instantiate the controller and inject dependencies
        return new QuestionController($entityManager, $questionManager, $questionFieldManager);
    }
}