<?php
namespace Base\Service;

use CMS\V1\Entity\Error;
use Exception;

/**
 * This service is responsible for adding/editing deployment-schedules.
 */
class ErrorManager
{
    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Constructs the service.
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * This method adds a new deployment-_chedule.
     */
    public function addError($data)
    {

        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            // Create new DeploymentSchedule entity.
            $err = new Error();
            $err->setIdUser($data['id_user']);
            $err->setType($data['type']);
            $err->setEvent($data['event']);
            $err->setUrl($data['url']);
            $err->setFile($data['file']);
            $err->setErrorType($data['error_type']);
            $err->setTrace($data['trace']);
            $err->setRequestData($data['request_data']);

            // Add the entity to the entity manager.
            $this->entityManager->persist($err);
            // Apply changes to database.
            $this->entityManager->flush();
            $conn->commit();
            return $err;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }
    }

}

