<?php

namespace CMS\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\Mail\Transport\Sendmail as SendmailTransport;
use CMS\Service\WindelMail;
use CMS\V1\Entity\MailTemplate;


/**
 * This is the factory class for WorkGroupManager service. The purpose of the factory
 * is to instantiate the service and pass it dependencies (inject dependencies).
 */
class WindelMailFactory
{
    /**
     * This method creates the WorkGroupManager service and returns its instance.
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {

        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $transport   = new SendmailTransport();

        return new WindelMail($entityManager, $transport, 1);
    }
}