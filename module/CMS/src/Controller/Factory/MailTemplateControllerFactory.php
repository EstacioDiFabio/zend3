<?php
namespace CMS\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use CMS\Controller\MailTemplateController;
use CMS\Service\MailTemplateManager;

use CMS\Service\ImageManager;

/**
 * This is the factory for MailTemplateController. Its purpose is to instantiate the
 * controller and inject dependencies into it.
 */
class MailTemplateControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $mailTemplateManager = $container->get(MailTemplateManager::class);
        $imageManager = $container->get(ImageManager::class);

        // Instantiate the controller and inject dependencies
        return new MailTemplateController($entityManager, $mailTemplateManager, $imageManager);
    }
}