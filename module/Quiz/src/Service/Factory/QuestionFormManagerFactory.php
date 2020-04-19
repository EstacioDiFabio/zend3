<?php
namespace Quiz\Service\Factory;

use Interop\Container\ContainerInterface;
use Quiz\Service\QuestionFormManager;

/**
 * This is the factory class for QuestionFormManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class QuestionFormManagerFactory
{
    /**
     * This method creates the QuestionFormManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        return new QuestionFormManager($entityManager);
    }
}
