<?php

namespace CMS\V1\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
/**
 * Activity
 *
 * @ORM\Table(name="activity")})
 * @ORM\Entity
 */
class Activity
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id_activity", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idActivity;

    /**
     * @var string
     *
     * @ORM\Column(name="id_user", type="integer", precision=0, scale=0, nullable=false, unique=false)
     */
    private $idUser;

    /**
     * @var boolean
     *
     * @ORM\Column(name="method", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     */
    private $method;

    /**
     * @var string
     * @ORM\Column(name="controller", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     */
    private $controller;

    /**
     * @var string
     * @ORM\Column(name="action", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     */
    private $action;

    /**
     * @var integer
     * @ORM\Column(name="url_parameter", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $urlParameter;

    /**
     * @var text
     * @ORM\Column(name="parameters", type="text", precision=0, scale=0, nullable=true, unique=false)
     */
    private $parameters;

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
    public function getIdActivity()
    {
        return $this->idActivity;
    }

    /**
     * Set idUser
     *
     * @param integer $idUser
     *
     * @return Activity
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
     * Set method
     *
     * @param string $method
     *
     * @return Activity
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set controller
     *
     * @param string $controller
     *
     * @return Activity
     */
    public function setController($controller = null)
    {

        $this->controller = $controller;

        return $this;
    }

    /**
     * Get controller
     *
     * @return string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set action
     *
     * @param string $action
     *
     * @return Activity
     */
    public function setAction($action = null)
    {

        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set urlParameter
     *
     * @param integer $urlParameter
     *
     * @return Activity
     */
    public function setUrlParameter($urlParameter = null)
    {

        $this->urlParameter = $urlParameter;

        return $this;
    }

    /**
     * Get idParameter
     *
     * @return integer
     */
    public function getUrlParameter()
    {
        return $this->urlParameter;
    }

    /**
     * Set parameters
     *
     * @param text $parameters
     *
     * @return Activity
     */
    public function setParameters($parameters = null)
    {

        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Get parameters
     *
     * @return text
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Set dateCreated
     *
     * @param DateTime $dateCreated
     *
     * @return Activity
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