<?php
namespace CMS\Service;

use CMS\V1\Entity\WorkGroup;
use Exception;

/**
 * This service is responsible for adding/editing groups.
 */
class WorkGroupManager
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
     * This method adds a new group.
     */
    public function addWorkGroup($data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            // Do not allow several users with the same name.
            if($this->checkWorkGroupExists($data['name'])) {
                throw new \Exception("Grupo com o nome {$data['name']} já existe.");
            }

            // Create new WorkGroup entity.
            $workGroup = new WorkGroup();
            $workGroup->setName($data['name']);
            $workGroup->setStatus($data['status']);

            // Add the entity to the entity manager.
            $this->entityManager->persist($workGroup);

            // Apply changes to database.
            $this->entityManager->flush();
            $conn->commit();
            return $workGroup;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * This method updates data of an existing workGroup.
     */
    public function updateWorkGroup($workGroup, $data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            // Do not allow to change group name if another group with such group already exits.
            if($workGroup->getName()!=$data['name'] && $this->checkWorkGroupExists($data['name'])) {

                throw new \Exception("Outro Cargo com o nome {$data['name']} já existe.");
            }

            $workGroup->setName($data['name']);
            $workGroup->setStatus($data['status']);

            // Apply changes to database.
            $this->entityManager->flush();
            $conn->commit();
            return $workGroup;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();

        }

    }

    /**
     * This method updates partial data of an existing workGroup.
     */
    public function patchWorkGroup($workGroup, $data)
    {

        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            if (isset($data['name'])) {
                if($workGroup->getName()!=$data['name'] && $this->checkJobExists($data['name'])) {
                    throw new \Exception("Outro Cargo com o nome {$data['name']} já existe.");
                }

                $workGroup->setName($data['name']);
            }

            if (isset($data['status'])) {

                if($data['status'] == 'true')
                    $data['status'] = 1;
                else
                    $data['status'] = 0;

                $workGroup->setStatus($data['status']);
            }

            // Apply changes to database.
            $this->entityManager->flush();
            $conn->commit();
            return $workGroup;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * This method remove data of an existing workGroup.
     */
    public function removeWorkGroup($data)
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
     * Checks whether an active group with given name already exists in the database.
     */
    public function checkWorkGroupExists($name)
    {

        $workGroup = $this->entityManager->getRepository(WorkGroup::class)->findOneByName($name);
        return $workGroup !== null;
    }

}

