<?php

namespace CMS\V1\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrganizationDepartament
 *
 * @ORM\Table(name="organization_departament", indexes={@ORM\Index(name="fk_organization_departament_id_organization_organization_idx", columns={"id_organization"}), @ORM\Index(name="fk_organization_departament_id_departament_departament_idx", columns={"id_departament"})})
 * @ORM\Entity
 */
class OrganizationDepartament
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
     * @var \CMS\V1\Entity\Organization
     *
     * @ORM\ManyToOne(targetEntity="CMS\V1\Entity\Organization")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_organization", referencedColumnName="id", nullable=true)
     * })
     */
    private $idOrganization;


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
     * @return OrganizationDepartament
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
     * Set idOrganization
     *
     * @param \CMS\V1\Entity\Organization $idOrganization
     *
     * @return OrganizationDepartament
     */
    public function setIdOrganization(\CMS\V1\Entity\Organization $idOrganization = null)
    {
        $this->idOrganization = $idOrganization;

        return $this;
    }

    /**
     * Get idOrganization
     *
     * @return \CMS\V1\Entity\Organization
     */
    public function getIdOrganization()
    {
        return $this->idOrganization;
    }
}

