<?php
namespace Quiz\Service\Factory;

use Interop\Container\ContainerInterface;
use Quiz\Service\QuestionFieldFilledValueManager;

/**
 * This is the factory class for QuestionFieldFilledValueManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class QuestionFieldFilledValueManagerFactory
{
    /**
     * This method creates the QuestionFieldFilledValueManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        return new QuestionFieldFilledValueManager($entityManager);
    }
}
