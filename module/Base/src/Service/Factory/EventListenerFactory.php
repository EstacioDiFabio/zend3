<?php

namespace Base\Service\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Base\Event\EventListener;
use Base\Service\ActivityManager;

class EventListenerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $activityManager = $container->get(ActivityManager::class);
        return new EventListener($activityManager);
    }
}