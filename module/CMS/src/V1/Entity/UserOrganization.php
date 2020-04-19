<?php

namespace CMS\V1\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserOrganization
 *
 * @ORM\Table(name="user_organization", indexes={@ORM\Index(name="fk_user_organization_id_user_user_id_idx", columns={"id_user"}), @ORM\Index(name="fk_user_organization_Id_organization_organization_id_idx", columns={"id_organizationcol"})})
 * @ORM\Entity
 */
class UserOrganization
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
     * @var \CMS\V1\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="CMS\V1\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_organizationcol", referencedColumnName="id", nullable=true)
     * })
     */
    private $idOrganizationcol;

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
     * Set idOrganizationcol
     *
     * @param \CMS\V1\Entity\Organization $idOrganizationcol
     *
     * @return UserOrganization
     */
    public function setIdOrganizationcol(\CMS\V1\Entity\Organization $idOrganizationcol = null)
    {
        $this->idOrganizationcol = $idOrganizationcol;

        return $this;
    }

    /**
     * Get idOrganizationcol
     *
     * @return \CMS\V1\Entity\Organization
     */
    public function getIdOrganizationcol()
    {
        return $this->idOrganizationcol;
    }

    /**
     * Set idUser
     *
     * @param \CMS\V1\Entity\User $idUser
     *
     * @return UserOrganization
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

