<?php

namespace Quiz\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Quiz\Controller\QuestionFormController;
use Quiz\Service\QuestionFormManager;

/**
 * This is the factory for QuestionFormController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class QuestionFormControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $questionFormManager = $container->get(QuestionFormManager::class);
        // Instantiate the controller and inject dependencies
        return new QuestionFormController($entityManager, $questionFormManager);
    }
}