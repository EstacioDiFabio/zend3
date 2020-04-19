<?php
namespace CMS\Service;

use Base\Service\BaseManager;
use CMS\V1\Entity\Job;
use Exception;
/**
 * This service is responsible for adding/editing jobs.
 */
class JobManager extends BaseManager
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
     * This method adds a new job.
     */
    public function addJob($data)
    {
        $conn = $this->entityManager->getConnection();

        try {

            $conn->beginTransaction();

            // Do not allow several users with the same name.
            if($this->checkJobExists($data['name'])) {
                throw new \Exception("Cargo com o nome {$data['name']} já existe.");
            }

            // Create new Job entity.
            $job = new Job();
            $job->setName($data['name']);
            $job->setIdTopJob((int)$data['id_top_job']);
            $job->setStatus($data['status']);
            // Add the entity to the entity manager.
            $this->entityManager->persist($job);

            // Apply changes to database.
            $this->entityManager->flush();
            $conn->commit();
            return $job;

        } catch (Exception $e) {
            $conn->rollBack();

            $params = [
                'id_user'      => $this->get_client_ip(),
                'type'         => 'error',
                'event'        => __FUNCTION__,
                'url'          => '',
                'file'         => __METHOD__,
                'line'         => __LINE__,
                'error_type'   => 'Exception',
                'trace'        => $e->getMessage(),
                'request_data' => $_REQUEST
            ];

            $this->setEventError($params);

            return $e->getMessage();
        }

    }

    /**
     * This method updates data of an existing job.
     */
    public function updateJob($job, $data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();
            // Do not allow to change job name if another job with such job already exits.
            if($job->getName()!=$data['name'] && $this->checkJobExists($data['name'])) {

                throw new \Exception("Outro Cargo com o nome {$data['name']} já existe.");
            }

            $job->setName($data['name']);
            $job->setIdTopJob((int)$data['id_top_job']);
            $job->setStatus($data['status']);

            // Apply changes to database.
            $this->entityManager->flush();
            $conn->commit();
            return $job;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * This method updates partial data of an existing job.
     */
    public function patchJob($job, $data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();
            if(isset($data['name'])){
                if($job->getName()!=$data['name'] && $this->checkJobExists($data['name'])) {
                    throw new \Exception("Outro Cargo com o nome {$data['name']} já existe.");
                }

                $job->setName($data['name']);
            }

            if(isset($data['id_top_job'])){
                $job->setIdTopJob((int)$data['id_top_job']);
            }

            if(isset($data['status'])){

                if($data['status'] == 'true')
                    $data['status'] = 1;
                else
                    $data['status'] = 0;

                $job->setStatus($data['status']);
            }

            // Apply changes to database.
            $this->entityManager->flush();
            $conn->commit();
            return $job;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * This method remove data of an existing job.
     */
    public function removeJob($data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            $this->entityManager->remove($data);
            $this->entityManager->flush();

            $conn->commit();

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }
    }

    /**
     * Checks whether an active job with given name already exists in the database.
     */
    public function checkJobExists($name)
    {

        $job = $this->entityManager->getRepository(Job::class)->findOneByName($name);
        return $job !== null;
    }

}

