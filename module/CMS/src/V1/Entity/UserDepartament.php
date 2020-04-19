<?php

namespace CMS\V1\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserDepartament
 *
 * @ORM\Table(name="user_departament", indexes={@ORM\Index(name="fk_id_departament_departament_id_idx", columns={"id_departament"}), @ORM\Index(name="fk_id_user_departament_user_id_idx", columns={"id_user"})})
 * @ORM\Entity
 */
class UserDepartament
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
     * @var \CMS\V1\Entity\Departament
     *
     * @ORM\ManyToOne(targetEntity="CMS\V1\Entity\Departament")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_departament", referencedColumnName="id", nullable=true)
     * })
     */
    private $idDepartament;

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
     * Set idDepartament
     *
     * @param \CMS\V1\Entity\Departament $idDepartament
     *
     * @return UserDepartament
     */
    public function setIdDepartament(\CMS\V1\Entity\Departament $idDepartament = null)
    {
        $this->idDepartament = $idDepartament;

        return $this;
    }

    /**
     * Get idDepartament
     *
     * @return \CMS\V1\Entity\Departament
     */
    public function getIdDepartament()
    {
        return $this->idDepartament;
    }

    /**
     * Set idUser
     *
     * @param \CMS\V1\Entity\User $idUser
     *
     * @return UserDepartament
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

