<?php

namespace Quiz\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Quiz\Controller\QuestionFieldController;
use Quiz\Service\QuestionFieldManager;

/**
 * This is the factory for QuestionFieldController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class QuestionFieldControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $questionFieldManager = $container->get(QuestionFieldManager::class);

        // Instantiate the controller and inject dependencies
        return new QuestionFieldController($entityManager, $questionFieldManager);
    }
}