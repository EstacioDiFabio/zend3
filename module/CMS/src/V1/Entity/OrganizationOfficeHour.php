<?php

namespace CMS\V1\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
/**
 * OrganizationOfficeHour
 *
 * @ORM\Table(name="organization_office_hour")
 * @ORM\Entity
 */
class OrganizationOfficeHour
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="id_organization", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $idOrganization;

    /**
     * @var string|null
     *
     * @ORM\Column(name="day", type="string", length=0, precision=0, scale=0, nullable=true, unique=false)
     */
    private $day;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="morning_start_time", type="time", precision=0, scale=0, nullable=true, unique=false)
     */
    private $morningStartTime;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="morning_closing_time", type="time", precision=0, scale=0, nullable=true, unique=false)
     */
    private $morningClosingTime;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="afternoon_start_time", type="time", precision=0, scale=0, nullable=true, unique=false)
     */
    private $afternoonStartTime;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="afternoon_closing_time", type="time", precision=0, scale=0, nullable=true, unique=false)
     */
    private $afternoonClosingTime;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status_hour", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $statusHour;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idOrganization.
     *
     * @param int $idOrganization
     *
     * @return OrganizationOfficeHour
     */
    public function setIdOrganization($idOrganization)
    {
        $this->idOrganization = $idOrganization;

        return $this;
    }

    /**
     * Get idOrganization.
     *
     * @return int
     */
    public function getIdOrganization()
    {
        return $this->idOrganization;
    }

    /**
     * Set day.
     *
     * @param string|null $day
     *
     * @return OrganizationOfficeHour
     */
    public function setDay($day = null)
    {
        $this->day = $day;

        return $this;
    }

    /**
     * Get day.
     *
     * @return string|null
     */
    public function getDay()
    {
        return $this->day;
    }

    /**
     * Set morningStartTime.
     *
     * @param \DateTime|null $morningStartTime
     *
     * @return OrganizationOfficeHour
     */
    public function setMorningStartTime($morningStartTime = null)
    {
        $this->morningStartTime = new DateTime($morningStartTime);

        return $this;
    }

    /**
     * Get morningStartTime.
     *
     * @return \DateTime|null
     */
    public function getMorningStartTime()
    {
        return $this->morningStartTime;
    }

    /**
     * Set morningClosingTime.
     *
     * @param \DateTime|null $morningClosingTime
     *
     * @return OrganizationOfficeHour
     */
    public function setMorningClosingTime($morningClosingTime = null)
    {
        $this->morningClosingTime = new DateTime($morningClosingTime);

        return $this;
    }

    /**
     * Get morningClosingTime.
     *
     * @return \DateTime|null
     */
    public function getMorningClosingTime()
    {
        return $this->morningClosingTime;
    }

    /**
     * Set afternoonStartTime.
     *
     * @param \DateTime|null $afternoonStartTime
     *
     * @return OrganizationOfficeHour
     */
    public function setAfternoonStartTime($afternoonStartTime = null)
    {
        $this->afternoonStartTime = new DateTime($afternoonStartTime);

        return $this;
    }

    /**
     * Get afternoonStartTime.
     *
     * @return \DateTime|null
     */
    public function getAfternoonStartTime()
    {
        return $this->afternoonStartTime;
    }

    /**
     * Set afternoonClosingTime.
     *
     * @param \DateTime|null $afternoonClosingTime
     *
     * @return OrganizationOfficeHour
     */
    public function setAfternoonClosingTime($afternoonClosingTime = null)
    {
        $this->afternoonClosingTime = new DateTime($afternoonClosingTime);

        return $this;
    }

    /**
     * Get afternoonClosingTime.
     *
     * @return \DateTime|null
     */
    public function getAfternoonClosingTime()
    {
        return $this->afternoonClosingTime;
    }

    /**
     * Set status_hour
     *
     * @param boolean $statusHour
     *
     * @return Departament
     */
    public function setStatusHour($statusHour)
    {
        $this->statusHour = $statusHour;

        return $this;
    }

    /**
     * Get status_hour
     *
     * @return boolean
     */
    public function getStatusHour()
    {
        return $this->statusHour;
    }
}
