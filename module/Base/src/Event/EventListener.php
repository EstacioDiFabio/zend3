<?php

namespace Base\Event;

use Zend\EventManager\EventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\ListenerAggregateInterface;
use Base\Event\EventBase;


class EventListener implements ListenerAggregateInterface
{
    private $listeners = [];

    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Departament manager.
     * @var Base\Service\ActivityManager
     */
    private $activityManager;

    public function __construct($activityManager)
    {
        $this->activityManager = $activityManager;
    }

    public function attach(EventManagerInterface $events, $priority = 200)
    {

        $sharedEvents = $events->getSharedManager();

        if (!empty($this->getCMSControllers())) {

            foreach ($this->getCMSControllers() as $controller) {

                $this->listeners[] = $sharedEvents->attach('CMS\Controller\\'.$controller,
                                               EventBase::EVENT_ACTIVITY_LOG,
                                               [$this, 'onActivityLog'],
                                               $priority);

                $this->listeners[] = $sharedEvents->attach('CMS\Controller\\'.$controller,
                                               EventBase::EVENT_EXCEPTION_LOG,
                                               [$this, 'onExceptionLog'],
                                               $priority);

            }
        }

    }

    public function detach(EventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $listener) {

            $events->detach($listener);
            unset($this->listeners[$index]);
        }
    }

    public function onActivityLog(EventInterface $event)
    {
        $data = $event->getParam('params');
        $data = $data['data_base'];

        $this->activityManager->addActity($data);
    }

    public function onExceptionLog(EventInterface $event)
    {
        $data = $event->getParam('params');
        $data = $data['data_base'];

        echo "<pre>"; die(var_dump('exception', $data));
        $this->activityManager->addActity($data);
    }

    public function getAuthControllers()
    {

        $controllers = dir("./module/Auth/src/Controller");
        $control = array();

        while (false !== ($entry = $controllers->read())) {

            if ($entry !== "Factory" && $entry !== '.' && $entry !== '..') { 
                array_push($control, substr($entry, 0, -4));
            }
        }

        return $control;
    }

    public function getCMSControllers()
    {
        $controllers = dir("./module/CMS/src/Controller");
        $control = array();

        while (false !== ($entry = $controllers->read())) {

            if ($entry !== "Factory" && $entry !== "Plugin" && $entry !== "CMSController.php" && $entry !== '.' && $entry !== '..') {
                array_push($control, substr($entry, 0, -4));
            }
        }

        return $control;
    }

}