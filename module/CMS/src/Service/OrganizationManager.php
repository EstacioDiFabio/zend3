<?php
namespace CMS\Service;

use CMS\V1\Entity\Organization;
use CMS\V1\Entity\OrganizationOfficeHour;
use Exception;

/**
 * This service is responsible for adding/editing organizations.
 */
class OrganizationManager
{
    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;
    private $organizationOfficeHourManager;

    private $day = [
        0 => 'sunday',
        1 => 'monday',
        2 => 'tuesday',
        3 => 'wednesday',
        4 => 'thursday',
        5 => 'friday',
        6 => 'saturday'
    ];
    private $dia = [
        0 => 'domingo',
        1 => 'segunda',
        2 => 'terca',
        3 => 'quarta',
        4 => 'quinta',
        5 => 'sexta',
        6 => 'sabado'
    ];
    /**
     * Constructs the service.
     */
    public function __construct($entityManager, $organizationOfficeHourManager)
    {
        $this->entityManager = $entityManager;
        $this->organizationOfficeHourManager = $organizationOfficeHourManager;
    }

    /**
     * This method adds a new organization.
     */
    public function addOrganization($data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            // Do not allow several users with the same name.
            if($this->checkOrganizationExists($data['name'])) {
                throw new \Exception("Unidade com o nome {$data['name']} já existe.");
            }

            // Create new Organization entity.
            $organization = new Organization();
            $organization->setName($data['name']);
            $organization->setStatus($data['status']);

            // Add the entity to the entity manager.
            $this->entityManager->persist($organization);

            // Apply changes to database.
            $this->entityManager->flush();

            for ($i = 0; $i <= 6; $i++) {

                if(isset($data[$this->day[$i]]['status_hour'])){

                    $saveData['id_organization'] = $organization->getId();
                    $saveData['day'] = $this->dia[$i];
                    $saveData['morning_start_time'] = $data[$this->day[$i]]['morning_start_time'];
                    $saveData['morning_closing_time'] = $data[$this->day[$i]]['morning_closing_time'];
                    $saveData['afternoon_start_time'] = $data[$this->day[$i]]['afternoon_start_time'];
                    $saveData['afternoon_closing_time'] = $data[$this->day[$i]]['afternoon_closing_time'];
                    $saveData['status_hour'] = $data[$this->day[$i]]['status_hour'];

                    $this->organizationOfficeHourManager->addOrganizationHour($saveData);
                }
            }

            $conn->commit();
            return $organization;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * This method updates data of an existing organization.
     */
    public function updateOrganization($organization, $data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();
            // Do not allow to change organization name if another organization with such organization already exits.
            if($organization->getName()!=$data['name'] && $this->checkOrganizationExists($data['name'])) {
                throw new \Exception("Outra Unidade com o nome {$data['name']} já existe.");
            }

            $organization->setName($data['name']);
            $organization->setStatus($data['status']);

            // Apply changes to database.
            $this->entityManager->flush();

            $qb = $this->entityManager->createQueryBuilder();
            $alias = 'ofh';
            $finder = $alias.'.idOrganization ='.$organization->getId();
            $hours = $qb->select($alias)
                        ->from(OrganizationOfficeHour::class, $alias)
                        ->where($finder)
                        ->getQuery();

            foreach($hours->getResult() as $result){
                $this->organizationOfficeHourManager->removeOrganizationHour($result);
            }

            for ($i = 0; $i <= 6; $i++) {

                if (isset($data[$this->day[$i]]['status_hour'])) {

                    $saveData['id_organization'] = $organization->getId();
                    $saveData['day'] = $this->dia[$i];
                    $saveData['morning_start_time'] = $data[$this->day[$i]]['morning_start_time'];
                    $saveData['morning_closing_time'] = $data[$this->day[$i]]['morning_closing_time'];
                    $saveData['afternoon_start_time'] = $data[$this->day[$i]]['afternoon_start_time'];
                    $saveData['afternoon_closing_time'] = $data[$this->day[$i]]['afternoon_closing_time'];
                    $saveData['status_hour'] = $data[$this->day[$i]]['status_hour'];

                    $this->organizationOfficeHourManager->addOrganizationHour($saveData);
                }
            }

            $conn->commit();
            return $organization;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

        return true;
    }

    /**
     * This method updates partial data of an existing organization.
     */
    public function patchOrganization($organization, $data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            if (isset($data['name'])) {
                if($organization->getName() != $data['name'] && $this->checkOrganizationExists($data['name'])) {
                    throw new \Exception("Outra Unidade com o nome {$data['name']} já existe.");
                }
                $organization->setName($data['name']);
            }

            if(isset($data['status'])){

                if($data['status'] == 'true')
                    $data['status'] = 1;
                else
                    $data['status'] = 0;

                $organization->setStatus($data['status']);
            }

            // Apply changes to database.
            $this->entityManager->flush();
            $conn->commit();
            return $organization;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * This method remove data of an existing organization.
     */
    public function removeOrganization($data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            $qb = $this->entityManager->createQueryBuilder();
            $alias = 'ofh';
            $finder = $alias.'.idOrganization ='.$data->getId();
            $hours = $qb->select($alias)
                        ->from(OrganizationOfficeHour::class, $alias)
                        ->where($finder)
                        ->getQuery();

            foreach($hours->getResult() as $result){
                $this->organizationOfficeHourManager->removeOrganizationHour($result);
            }

            $this->entityManager->remove($data);
            $this->entityManager->flush();

            $conn->commit();

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }
    }

    /**
     * Checks whether an active organization with given name already exists in the database.
     */
    public function checkOrganizationExists($name)
    {
        $organization = $this->entityManager->getRepository(Organization::class)->findOneByName($name);
        return $organization !== null;
    }

}

