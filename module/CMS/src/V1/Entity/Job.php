<?php

namespace CMS\V1\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Job
 *
 * @ORM\Table(name="job", indexes={@ORM\Index(name="fk_job_id_top_job_job_id_idx", columns={"id_top_job"})})
 * @ORM\Entity
 */
class Job
{

    // Job status constants.
    const STATUS_ACTIVE       = 1; // Active job.
    const STATUS_RETIRED      = 0; // Retired job.

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $status;

    /**
     * @var integer
     * @ORM\Column(name="id_top_job", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $idTopJob;


    /**
     * Returns possible statuses as array.
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => 'Ativo',
            self::STATUS_RETIRED => 'Inativo'
        ];
    }

    /**
     * Returns job status as string.
     * @return string
     */
    public function getStatusAsString()
    {
        $list = self::getStatusList();
        if (isset($list[$this->status]))
            return $list[$this->status];

        return 'Desconhecido';
    }

    public function getStatusToggle()
    {
        $id = $this->getId();
        $status = $this->getStatusAsString();
        $status_id = $this->getStatus();

        $toggle = "<div class='status_string_".$id."'>";
            $toggle .= $status;
        $toggle .= "</div>";

        $toggle .= "<div class='material-switch'>";
            $toggle .= "<input class='status-index'
                               type='checkbox'
                               name='status'
                               value='".$status_id."'
                               data-status='".$status."'
                               data-id='".$id."'
                               id='status_".$id."'>";
        $toggle .= "<label for='status_".$id."'></label>";
        $toggle .= "</div>";

        return $toggle;
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
     * Set name
     *
     * @param string $name
     *
     * @return Job
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Job
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
     * Set idTopJob
     *
     * @param integer $idTopJob
     *
     * @return Job
     */
    public function setIdTopJob($idTopJob = null)
    {

        $this->idTopJob = $idTopJob;

        return $this;
    }

    /**
     * Get idTopJob
     *
     * @return integer
     */
    public function getIdTopJob()
    {
        return $this->idTopJob;
    }
}