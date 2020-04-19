<?php
namespace CMS\Service\Factory;

use Interop\Container\ContainerInterface;
use CMS\Service\MailTemplateManager;

/**
 * This is the factory class for MailTemplateManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class MailTemplateManagerFactory
{
    /**
     * This method creates the MailTemplateManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $entityManager = $container->get('doctrine.entitymanager.orm_default');

        return new MailTemplateManager($entityManager);
    }
}
