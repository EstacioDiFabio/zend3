<?php

namespace CMS\V1\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
/**
 * Error
 *
 * @ORM\Table(name="error")})
 * @ORM\Entity
 */
class Error
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_error", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idError;

    /**
     * @var string
     *
     * @ORM\Column(name="id_user", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $idUser;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", length=11, precision=0, scale=0, nullable=true, unique=false)
     */
    private $type;

    /**
     * @var text
     * @ORM\Column(name="event", type="text", precision=0, scale=0, nullable=true, unique=false)
     */
    private $event;

    /**
     * @var string
     * @ORM\Column(name="url", type="string", length=2000, precision=0, scale=0, nullable=true, unique=false)
     */
    private $url;

    /**
     * @var string
     * @ORM\Column(name="file", type="string", length=2000, precision=0, scale=0, nullable=true, unique=false)
     */
    private $file;

    /**
     * @var integer
     * @ORM\Column(name="line", type="integer", length=11, precision=0, scale=0, nullable=true, unique=false)
     */
    private $line;

    /**
     * @var string
     * @ORM\Column(name="error_type", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $errorType;

    /**
     * @var string
     * @ORM\Column(name="trace", type="text", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $trace;

    /**
     * @var string
     * @ORM\Column(name="request_data", type="text", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $requestData;


    /**
     * @var datetime
     * @ORM\Column(name="date_created", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $dateCreated;

    /**
     * Get id
     *
     * @return integer
     */
    public function getIdError()
    {
        return $this->idError;
    }

    /**
     * Set idUser
     *
     * @param integer $idUser
     *
     * @return Error
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser
     *
     * @return integer
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Error
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set event
     *
     * @param string $event
     *
     * @return Error
     */
    public function setEvent($event = null)
    {

        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Error
     */
    public function setUrl($url = null)
    {

        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set file
     *
     * @param integer $file
     *
     * @return Error
     */
    public function setFile($file = null)
    {

        $this->file = $file;

        return $this;
    }

    /**
     * Get idParameter
     *
     * @return integer
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set errorType
     *
     * @param text $error_type
     *
     * @return Error
     */
    public function setErrorType($errorType = null)
    {

        $this->errorType = $errorType;

        return $this;
    }

    /**
     * Get errorType
     *
     * @return text
     */
    public function getErrorType()
    {
        return $this->errorType;
    }

    /**
     * Set trace
     *
     * @param text $trace
     *
     * @return Error
     */
    public function setTrace($trace = null)
    {

        $this->trace = $trace;

        return $this;
    }

    /**
     * Get trace
     *
     * @return text
     */
    public function getTrace()
    {
        return $this->trace;
    }

    /**
     * Set requestData
     *
     * @param text $requestData
     *
     * @return Error
     */
    public function setRequestData($requestData = null)
    {

        $this->requestData = $requestData;

        return $this;
    }

    /**
     * Get requestData
     *
     * @return text
     */
    public function getRequestData()
    {
        return $this->requestData;
    }

    /**
     * Set dateCreated
     *
     * @param DateTime $dateCreated
     *
     * @return Error
     */
    public function setDateCreated($dateCreated = null)
    {

        $this->dateCreated = new \DateTime('now');
        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }
}