<?php

namespace Base\Service;


/**
 * This service is responsible for adding/editing deployment-schedules.
 */
class BaseManager
{
    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $errorManager;

    /**
     * Constructs the service.
     */
    public function __construct($entityManager, $errorManager)
    {
        $this->entityManager = $entityManager;
        $this->errorManager  = $errorManager;
    }

    public function setEventError($params)
    {
        $params = [
            'id_user'      => $params['id_user'],
            'type'         => $params['type'],
            'event'        => $params['event'],
            'url'          => $params['url'],
            'file'         => $params['file'],
            'line'         => $params['line'],
            'error_type'   => $params['error_type'],
            'trace'        => $params['trace'],
            'request_data' => serialize($params['request_data']),
        ];

        $this->errorManager->addError($params);
    }

    public function get_client_ip()
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

