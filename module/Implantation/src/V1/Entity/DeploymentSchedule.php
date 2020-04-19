<?php

namespace Implantation\V1\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * DeploymentSchedule
 *
 * @ORM\Table(name="deployment_schedule")
 * @ORM\Entity
 */
class DeploymentSchedule
{
    // Departament status constants.
    const STATUS_ACTIVE       = 1; // Active Departament.
    const STATUS_RETIRED      = 0; // Retired Departament.

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_client", type="integer", nullable=false)
     */
    private $idClient;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_attendance_crm", type="integer", nullable=true)
     */
    private $idAttendanceCrm;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_last_ds", type="integer", nullable=true)
     */
    private $idLastDs;

    /**
     * @var string
     *
     * @ORM\Column(name="client_name", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $clientName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="time", nullable=false)
     */
    private $time;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_end", type="time", nullable=false)
     */
    private $timeEnd;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status = '1';

    /**
     * @var text
     *
     * @ORM\Column(name="obs", type="text", nullable=true)
     */
    private $obs;

    /**
     * @var datetime
     *
     * @ORM\Column(name="created", type="text", nullable=true)
     */
    private $created;

    /**
     * @var datetime
     *
     * @ORM\Column(name="started", type="text", nullable=true)
     */
    private $started;

    /**
     * @var datetime
     *
     * @ORM\Column(name="updated", type="text", nullable=true)
     */
    private $updated;

    /**
     * @var datetime
     *
     * @ORM\Column(name="finished", type="text", nullable=true)
     */
    private $finished;

     /**
     * Returns possible statuses as array.
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => 'Agendado',
            self::STATUS_RETIRED => 'Finalizado'
        ];
    }

    /**
     * Returns departament status as string.
     * @return string
     */
    public function getStatusAsString()
    {
        $list = self::getStatusList();
        if (isset($list[$this->status]))
            return $list[$this->status];

        return 'Desconhecido';
    }

     /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set idClient
     *
     * @param int $idClient
     *
     * @return DeploymentSchedule
     */
    public function setIdClient($idClient)
    {
        $this->idClient = $idClient;

        return $this;
    }

    /**
     * Get idClient
     *
     * @return string
     */
    public function getIdClient()
    {
        return $this->idClient;
    }

    /**
     * Set idAttendanceCrm
     *
     * @param id $idAttendanceCrm
     *
     * @return Departament
     */
    public function setIdAttendanceCrm($idAttendanceCrm)
    {
        $this->idAttendanceCrm = $idAttendanceCrm;

        return $this;
    }

    /**
     * Get idAttendanceCrm
     *
     * @return integer
     */
    public function getIdAttendanceCrm()
    {
        return $this->idAttendanceCrm;
    }

    /**
     * Set idLastDs
     *
     * @param id $idLastDs
     *
     * @return Departament
     */
    public function setIdLastDs($idLastDs)
    {
        $this->idLastDs = $idLastDs;

        return $this;
    }

    /**
     * Get idLastDs
     *
     * @return integer
     */
    public function getIdLastDs()
    {
        return $this->idLastDs;
    }

    /**
     * Set clientName
     *
     * @param id $clientName
     *
     * @return Departament
     */
    public function setClientName($clientName)
    {
        $this->clientName = $clientName;

        return $this;
    }

    /**
     * Get clientName
     *
     * @return integer
     */
    public function getClientName()
    {
        return $this->clientName;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return DeploymentSchedule
     */
    public function setDate($date)
    {
        $this->date = new Datetime($date);
        return $this;
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     *
     * @return DeploymentSchedule
     */
    public function setTime($time)
    {
        $this->time = new DateTime($time);

        return $this;
    }

    /**
     * Get time
     *
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set time_end
     *
     * @param \DateTime $time_end
     *
     * @return DeploymentSchedule
     */
    public function setTimeEnd($timeEnd)
    {
        $this->timeEnd = new DateTime($timeEnd);

        return $this;
    }

    /**
     * Get time_end
     *
     * @return string
     */
    public function getTimeEnd()
    {
        return $this->timeEnd;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Departament
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set obs
     *
     * @param boolean $obs
     *
     * @return Departament
     */
    public function setObs($obs)
    {
        $this->obs = $obs;

        return $this;
    }

    /**
     * Get obs
     *
     * @return text
     */
    public function getObs()
    {
        return $this->obs;
    }

    /**
     * Get created
     *
     * @return datetime
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return datetime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Get started
     *
     * @return datetime
     */
    public function setStarted($started)
    {
        $this->started = $started;

        return $this;
    }

    /**
     * Get started
     *
     * @return datetime
     */
    public function getStarted()
    {
        return $this->started;
    }

    /**
     * Get updated
     *
     * @return text
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set updated
     *
     * @param boolean $updated
     *
     * @return Departament
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get finished
     *
     * @return text
     */
    public function getFinished()
    {
        return $this->finished;
    }

    /**
     * Set finished
     *
     * @param boolean $finished
     *
     * @return Departament
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;

        return $this;
    }
}

