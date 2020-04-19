<?php
namespace Base\Event;

use Zend\EventManager\Event;

class EventBase extends Event
{
    const EVENT_ACTIVITY_LOG = 'activity_log';
    const EVENT_EXCEPTION_LOG = 'exception_log';
}