<?php
namespace Base\Service;

use CMS\V1\Entity\Activity;
use Exception;
use DateTime;

/**
 * This service is responsible for adding/editing deployment-schedules.
 */
class ActivityManager
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
    public function addActity($data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();
            $date = new DateTime();
             // Create new DeploymentSchedule entity.
            $act = new Activity();
            $act->setIdUser($data['id_user']);
            $act->setMethod($data['method']);
            $act->setController($data['controller']);
            $act->setAction($data['action']);
            $act->setUrlParameter($data['url_parameter']);
            $act->setDateCreated($date);

            if($data['parameters'] != null && !empty($data['parameters']))
                $act->setParameters(serialize($data['parameters']));

            // Add the entity to the entity manager.
            $this->entityManager->persist($act);
            // Apply changes to database.
            $this->entityManager->flush();
            $conn->commit();
            return $act;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

}

