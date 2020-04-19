<?php
namespace Quiz\Service\Factory;

use Interop\Container\ContainerInterface;
use Quiz\Service\ProdutoManager;

/**
 * This is the factory class for ProdutoManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class ProdutoManagerFactory
{
    /**
     * This method creates the ProdutoManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        return new ProdutoManager($entityManager);
    }
}
