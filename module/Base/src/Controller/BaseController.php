<?php
namespace Base\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\EventManager\EventManagerInterface;
use Base\Event\EventBase;
use Exception;

class BaseController extends AbstractActionController
{

    public function setEventManager(EventManagerInterface $events)
    {

        parent::setEventManager($events);

        $controller = $this;

        $events->attach('dispatch', function ($e) use ($controller) {

            $request = $e->getRequest();
            $method  = $request->getMethod();
            $controllerName = $controller->params()->fromRoute()['controller'];
            $controllerName = substr($controllerName, strrpos($controllerName, "\\")+1 );
            $action = $controller->params()->fromRoute()['action'];
            $urlParam = "";

            if ($controller->params()->fromRoute('id', false)) {
                $urlParam = $controller->params()->fromRoute('id', false);
            }
            if($this->currentUser()){
                $idUser = $this->currentUser()->getId();
            } else {
                $idUser = $this->get_client_ip();
            }
            $parameter = null;

            if (count($this->params()->fromQuery()) > 0) {
                $parameter = serialize($this->params()->fromQuery());
            } else if ($action != 'login' && $method == 'POST') {
                $parameter = serialize($controller->params()->fromPost());
            }

            $parameters = [
                'id_user' => $idUser,
                'method' => $method,
                'controller' => $controllerName,
                'action' => $action,
                'url_parameter' => $urlParam,
                'parameters' => $parameter,
            ];

            $event = new EventBase(__METHOD__, null, ['data_base' => $parameters]);
            $this->getEventManager()->trigger(EventBase::EVENT_ACTIVITY_LOG, $this, $event);

        }, 100);
    }

    private function get_client_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
}