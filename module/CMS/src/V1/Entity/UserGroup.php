<?php

namespace CMS\V1\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserGroup
 *
 * @ORM\Table(name="user_group", indexes={@ORM\Index(name="fk_id_user_user_id_idx", columns={"id_user"}), @ORM\Index(name="fk_id_group_group_id_idx", columns={"id_group"})})
 * @ORM\Entity
 */
class UserGroup
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \CMS\V1\Entity\WorkGroup
     *
     * @ORM\ManyToOne(targetEntity="CMS\V1\Entity\WorkGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_group", referencedColumnName="id", nullable=true)
     * })
     */
    private $idGroup;

    /**
     * @var \CMS\V1\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="CMS\V1\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="id", nullable=true)
     * })
     */
    private $idUser;


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
     * Set idGroup
     *
     * @param \CMS\V1\Entity\WorkGroup $idGroup
     *
     * @return UserGroup
     */
    public function setIdGroup(\CMS\V1\Entity\WorkGroup $idGroup = null)
    {
        $this->idGroup = $idGroup;

        return $this;
    }

    /**
     * Get idGroup
     *
     * @return \CMS\V1\Entity\WorkGroup
     */
    public function getIdGroup()
    {
        return $this->idGroup;
    }

    /**
     * Set idUser
     *
     * @param \CMS\V1\Entity\User $idUser
     *
     * @return UserGroup
     */
    public function setIdUser(\CMS\V1\Entity\User $idUser = null)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser
     *
     * @return \CMS\V1\Entity\User
     */
    public function getIdUser()
    {
        return $this->idUser;
    }
}

