<?php
namespace CMS\Service;

use CMS\V1\Entity\OrganizationOfficeHour;
use Exception;

/**
 * This service is responsible for adding/editing organizations.
 */
class OrganizationOfficeHourManager
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
     * This method adds a new organization.
     */
    public function addOrganizationHour($data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            // Create new OrganizationOfficeHour entity.
            $organization = new OrganizationOfficeHour();
            $organization->setIdOrganization((int)$data['id_organization']);
            $organization->setDay($data['day']);
            $organization->setMorningStartTime($data['morning_start_time']);
            $organization->setMorningClosingTime($data['morning_closing_time']);
            $organization->setAfternoonStartTime($data['afternoon_start_time']);
            $organization->setAfternoonClosingTime($data['afternoon_closing_time']);
            $organization->setStatusHour((int)$data['status_hour']);

            // Add the entity to the entity manager.
            $this->entityManager->persist($organization);

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
    public function removeOrganizationHour($data)
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

}

