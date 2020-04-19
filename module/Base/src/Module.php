<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Base;

use Zend\EventManager\EventInterface;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\ModuleRouteListener;
use Base\Event\EventListener;

class Module
{

    const VERSION  = '1.0';

    const DATABASE = 'zendel';
    const USERNAME = 'root';
    const PASSWORD = 'Root@less123';

    const LOG_DATABASE = 'zendel_log';
    const LOG_USERNAME = 'root';
    const LOG_PASSWORD = 'Root@less123';

    const UPLOAD_DIR = './data/upload/';
    const ROOT_PATH  = '/zendel/master/public';

    const API_VERBOSE   = 'http://';
    const API_DOMAIN    = 'localhost/';
    const API_ROOT_PATH = 'windel-api/master/public/';

    const ENV     = 'development';
    const ENV_KEY = 'd2luZGVsOlcxbmQzbEB0ZXN0ZXM=';
    const CLIENTE_ENV = 'clientes/master/';

    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    /**
     * This method is called once the MVC bootstrapping is complete and allows
     * to register event listeners.
     */
    public function onBootstrap(EventInterface $e)
    {
        $application = $e->getApplication();
        $serviceManager = $application->getServiceManager();
        $eventManager = $application->getEventManager();

        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $hello = $serviceManager->get(EventListener::class);
        $hello->attach($eventManager);
    }

}
