<?php

namespace CMS\V1\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use DateTime;

/**
 * User
 *
 * @ORM\Table(name="user", indexes={@ORM\Index(name="fk_user_1_idx", columns={"id_job"})})
 * @ORM\Entity
 */
class User
{
     // User status constants.
    const STATUS_ACTIVE       = 1; // Active user.
    const STATUS_RETIRED      = 0; // Retired user.
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
     * @ORM\Column(name="first_name", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $password;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $dateCreated;

    /**
     * @var integer
     *
     * @ORM\Column(name="user_id_crm", type="integer", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $userIdCrm;

    /**
     * @ORM\Column(name="pwd_reset_token")
     */
    protected $passwordResetToken;

    /**
     * @ORM\Column(name="pwd_reset_token_creation_date")
     */
    protected $passwordResetTokenCreationDate;

    /**
     * @var \CMS\V1\Entity\Job
     *
     * @ORM\ManyToOne(targetEntity="CMS\V1\Entity\Job")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_job", referencedColumnName="id", nullable=true)
     * })
     */
    private $idJob;

    /**
     * @ORM\ManyToMany(targetEntity="Auth\V1\Entity\Role")
     * @ORM\JoinTable(name="user_role",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id")}
     *      )
     */
    private $roles;

    /**
     * @ORM\ManyToMany(targetEntity="CMS\V1\Entity\WorkGroup")
     * @ORM\JoinTable(name="user_group",
     *      joinColumns={@ORM\JoinColumn(name="id_user", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_group", referencedColumnName="id")}
     *      )
     */
    private $workGroup;

    /**
     * @ORM\ManyToMany(targetEntity="CMS\V1\Entity\Departament")
     * @ORM\JoinTable(name="user_departament",
     *      joinColumns={@ORM\JoinColumn(name="id_user", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_departament", referencedColumnName="id")}
     *      )
     */
    private $departament;

    /**
     * @ORM\ManyToMany(targetEntity="CMS\V1\Entity\Organization")
     * @ORM\JoinTable(name="user_organization",
     *      joinColumns={@ORM\JoinColumn(name="id_user", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="id_organization", referencedColumnName="id")}
     *      )
     */
    private $organization;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->roles     = new ArrayCollection();
        $this->workGroup = new ArrayCollection();
        $this->departament = new ArrayCollection();
        $this->organization = new ArrayCollection();
    }
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
     * Returns user status as string.
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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return User
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
     * Set dateCreated
     *
     * @param DateTime $dateCreated
     *
     * @return User
     */
    public function setDateCreated($dateCreated = null)
    {

        $this->dateCreated = new \DateTime('now');
        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set idJob
     *
     * @param \CMS\V1\Entity\Job $idJob
     *
     * @return User
     */
    public function setIdJob(\CMS\V1\Entity\Job $idJob = null)
    {
        $this->idJob = $idJob;

        return $this;
    }

    /**
     * Get idJob
     *
     * @return \CMS\V1\Entity\Job
     */
    public function getIdJob()
    {
        return $this->idJob;
    }

    /**
     * Returns the string of assigned role names.
     */
    public function getJobAsString()
    {
        return $this->getIdJob()->getName();
    }

    /**
     * Returns the array of roles assigned to this user.
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Returns the string of assigned role names.
     */
    public function getRolesAsString()
    {
        $roleList = '';

        $count = count($this->roles);

        $i = 0;
        if($this->roles){

            foreach ($this->roles as $role) {
                $roleList .= $role->getName();
                if ($i<$count-1)
                    $roleList .= ', ';
                $i++;
            }
        }
        return $roleList;
    }

    /**
     * Assigns a role to user.
     */
    public function addRole($role)
    {
        $this->roles->add($role);
    }

    /**
     * Returns the array of workGroups assigned to this user.
     * @return array
     */
    public function getWorkGroup()
    {
        return $this->workGroup;
    }

    /**
     * Returns the string of assigned workGroup names.
     */
    public function getGroupAsString()
    {
        $workGroupList = '';

        $count = count($this->workGroup);
        $i = 0;
        foreach ($this->workGroup as $group) {
            $workGroupList .= $group->getName();
            if ($i<$count-1)
                $workGroupList .= ', ';
            $i++;
        }

        return $workGroupList;
    }

    /**
     * Assigns a workGroup to user.
     */
    public function addWorkGroup($group)
    {
        $this->workGroup->add($group);
    }

    /**
     * Returns the array of departaments assigned to this user.
     * @return array
     */
    public function getDepartament()
    {
        return $this->departament;
    }

    /**
     * Returns the string of assigned departament names.
     */
    public function getDepartamentAsString()
    {
        $departamentList = '';

        $count = count($this->departament);
        $i = 0;
        foreach ($this->departament as $departament) {
            $departamentList .= $departament->getName();
            if ($i<$count-1)
                $departamentList .= ', ';
            $i++;
        }

        return $departamentList;
    }

    /**
     * Assigns a departament to user.
     */
    public function addDepartament($departament)
    {
        $this->departament->add($departament);
    }

    /**
     * Returns the array of organizations assigned to this user.
     * @return array
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Returns the string of assigned organization names.
     */
    public function getOrganizationAsString()
    {
        $organizationList = '';

        $count = count($this->organization);
        $i = 0;
        foreach ($this->organization as $organization) {
            $organizationList .= $organization->getName();
            if ($i<$count-1)
                $organizationList .= ', ';
            $i++;
        }

        return $organizationList;
    }

    /**
     * Assigns a organization to user.
     */
    public function addOrganization($organization)
    {
        $this->organization->add($organization);
    }

    /**
     * Returns password reset token.
     * @return string
     */
    public function getResetPasswordToken()
    {
        return $this->passwordResetToken;
    }

    /**
     * Sets password reset token.
     * @param string $token
     */
    public function setPasswordResetToken($token)
    {
        $this->passwordResetToken = $token;
    }

    /**
     * Returns password reset token's creation date.
     * @return string
     */
    public function getPasswordResetTokenCreationDate()
    {
        return $this->passwordResetTokenCreationDate;
    }

    /**
     * Sets password reset token's creation date.
     * @param string $date
     */
    public function setPasswordResetTokenCreationDate($date)
    {
        $this->passwordResetTokenCreationDate = $date;
    }

    /**
     * Set userIdCrm
     *
     * @param integer $userIdCrm
     *
     * @return User
     */
    public function setUserIdCrm($userIdCrm)
    {
        $this->userIdCrm = $userIdCrm;

        return $this;
    }

    /**
     * Get userIdCrm
     *
     * @return integer
     */
    public function getUserIdCrm()
    {
        return $this->userIdCrm;
    }

}

