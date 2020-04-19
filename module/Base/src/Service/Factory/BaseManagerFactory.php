<?php
namespace Base\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Base\Service\BaseManager;
use Base\Service\ErrorManager;

class BaseManagerFactory implements FactoryInterface
{
    /**
     * This method creates the BaseManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_log');
        $errorManager = $container->get(ErrorManager::class);

        return new BaseManager($entityManager, $errorManager);
    }
}
