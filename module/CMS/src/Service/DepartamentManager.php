<?php
namespace CMS\Service;

use CMS\V1\Entity\Departament;
use Exception;

/**
 * This service is responsible for adding/editing departaments.
 */
class DepartamentManager
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
     * This method adds a new departament.
     */
    public function addDepartament($data)
    {

        $conn = $this->entityManager->getConnection();

        try {

            $conn->beginTransaction();

            // Do not allow several users with the same name.
            if($this->checkDepartamentExists($data['name'])) {
                throw new Exception("Um setor com o nome {$data['name']} já existe.");
            }

            // Create new Departament entity.
            $departament = new Departament();
            $departament->setName($data['name']);
            $departament->setStatus($data['status']);

            // Add the entity to the entity manager.
            $this->entityManager->persist($departament);
            // Apply changes to database.
            $this->entityManager->flush();

            $conn->commit();
            return $departament;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * This method updates data of an existing departament.
     */
    public function updateDepartament($departament, $data)
    {
        $conn = $this->entityManager->getConnection();

        try {

            $conn->beginTransaction();

            // Do not allow to change departament name if another departament with such departament already exits.
            if($departament->getName()!=$data['name'] && $this->checkDepartamentExists($data['name'])) {
                throw new Exception("Um outro Setor de nome {$data['name']} já existe.");
            }

            $departament->setName($data['name']);
            $departament->setStatus($data['status']);

            // Apply changes to database.
            $this->entityManager->flush();
            $conn->commit();
            return $departament;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }
    }

    /**
     * This method updates partial data of an existing departament.
     */
    public function patchDepartament($departament, $data)
    {
        $conn = $this->entityManager->getConnection();

        try {

            $conn->beginTransaction();

            if(isset($data['name'])){

                if($departament->getName()!=$data['name'] && $this->checkDepartamentExists($data['name'])) {
                    throw new Exception("Um outro Setor de nome {$data['name']} já existe.");
                }
                $departament->setName($data['name']);
            }

            if(isset($data['status'])){

                if($data['status'] == 'true')
                    $data['status'] = 1;
                else
                    $data['status'] = 0;

                $departament->setStatus($data['status']);
            }

            // Apply changes to database.
            $this->entityManager->flush();
            $conn->commit();
            return $departament;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }


    }
    /**
     * This method remove data of an existing Departament.
     */
    public function removeDepartament($data)
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
     * Checks whether an active Departament with given name already exists in the database.
     */
    public function checkDepartamentExists($name)
    {
        $departament = $this->entityManager->getRepository(Departament::class)->findOneByName($name);
        return $departament !== null;
    }

}

