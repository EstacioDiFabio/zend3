<?php
namespace Base\Controller\Plugin\Factory;

use Interop\Container\ContainerInterface;
use Base\Controller\Plugin\WindelFilterPlugin;

class WindelFilterPluginFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $authService = $container->get(\Zend\Authentication\AuthenticationService::class);

        return new WindelFilterPlugin($entityManager, $authService);
    }
}


