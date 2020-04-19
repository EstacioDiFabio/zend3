<?php

namespace Quiz\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Quiz\Controller\ProdutoController;
use Quiz\Service\ProdutoManager;

/**
 * This is the factory for ProdutoController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class ProdutoControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $produtoManager = $container->get(ProdutoManager::class);
        // Instantiate the controller and inject dependencies
        return new ProdutoController($entityManager, $produtoManager);
    }
}