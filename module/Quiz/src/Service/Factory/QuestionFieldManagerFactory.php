<?php
namespace Quiz\Service\Factory;

use Interop\Container\ContainerInterface;
use Quiz\Service\QuestionFieldManager;

/**
 * This is the factory class for QuestionFieldManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class QuestionFieldManagerFactory
{
    /**
     * This method creates the QuestionFieldManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        return new QuestionFieldManager($entityManager);
    }
}
