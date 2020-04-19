<?php
namespace CMS\Service;

use CMS\V1\Entity\User;
use Auth\V1\Entity\Role;
use CMS\V1\Entity\Job;
use CMS\V1\Entity\WorkGroup;
use CMS\V1\Entity\Departament;
use CMS\V1\Entity\Organization;
use Zend\Crypt\Password\Bcrypt;
use Zend\Math\Rand;
use Exception;
use DateTime;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class UserManager
{
    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Role manager.
     * @var CMS\Service\RoleManager
     */
    private $roleManager;

    /**
     * Permission manager.
     * @var CMS\Service\PermissionManager
     */
    private $permissionManager;

    /**
     * WorkGroup manager.
     * @var CMS\Service\WorkGroupManager
     */
    private $workGroupManager;

    /**
     * Departament manager.
     * @var CMS\Service\DepartamentManager
     */
    private $departamentManager;

    /**
     * Organization manager.
     * @var CMS\Service\OrganizationManager
     */
    private $organizationManager;

    private $windelMail;

    /**
     * Constructs the service.
     */
    public function __construct($entityManager, $roleManager, $permissionManager,
                                $workGroupManager, $departamentManager, $organizationManager,
                                $windelMail)
    {
        $this->entityManager = $entityManager;
        $this->roleManager = $roleManager;
        $this->permissionManager = $permissionManager;
        $this->workGroupManager = $workGroupManager;
        $this->departamentManager = $departamentManager;
        $this->organizationManager = $organizationManager;
        $this->windelMail = $windelMail;

    }

    /**
     * This method adds a new user.
     */
    public function addUser($data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();
            // Do not allow several users with the same email address.
            if($this->checkUserExists($data['email'])) {
                throw new \Exception("Usuário com o endereço de e-mail {$data['email']} já existe.");
            }

            $job = $this->entityManager->getRepository(Job::class)->find($data['job']);

            if ($job==null) {
                throw new \Exception('Not found job by ID');
            }

            // Create new User entity.
            $user = new User();
            $user->setEmail($data['email']);
            $user->setFirstName($data['first_name']);
            $user->setLastName($data['last_name']);
            $user->setUserIdCrm($data['user_id_crm']);
            $user->setIdJob($job);
            $user->setDateCreated(new DateTime('now'));

            // Encrypt password and store the password in encrypted state.
            $bcrypt = new Bcrypt();
            $passwordHash = $bcrypt->create($data['password']);
            $user->setPassword($passwordHash);
            $user->setStatus($data['status']);

            // Assign roles to user.
            $this->assignRoles($user, $data['roles']);
            // Assign work_group to user.
            $this->assignWorkGroup($user, $data['work_groups']);
            // Assign work_group to user.
            $this->assignDepartament($user, $data['departaments']);
            // Assign work_group to user.
            $this->assignOrganization($user, $data['organizations']);
            // Add the entity to the entity manager.
            $this->entityManager->persist($user);
            // Apply changes to database.
            $this->entityManager->flush();

            $conn->commit();
            return $user;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * This method updates data of an existing user.
     */
    public function updateUser($user, $data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();
            // Do not allow to change user email if another user with such email already exits.
            if($user->getEmail()!=$data['email'] && $this->checkUserExists($data['email'])) {
                throw new \Exception("Usuário com o endereço de e-mail {$data['email']} já existe.");
            }

            $job = $this->entityManager->getRepository(Job::class)->find($data['job']);

            if ($job==null) {
                throw new \Exception('Not found job by ID');
            }

            $user->setEmail($data['email']);
            $user->setFirstName($data['first_name']);
            $user->setLastName($data['last_name']);
            $user->setUserIdCrm($data['user_id_crm']);
            $user->setStatus($data['status']);
            $user->setIdJob($job);

            // Assign roles to user.
            $this->assignRoles($user, $data['roles']);
            // Assign work_group to user.
            $this->assignWorkGroup($user, $data['work_groups']);
            // Assign work_group to user.
            $this->assignDepartament($user, $data['departaments']);
            // Assign work_group to user.
            $this->assignOrganization($user, $data['organizations']);
            // Apply changes to database.
            $this->entityManager->flush();

            $conn->commit();
            return $user;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * This method updates partial data of an existing user.
     */
    public function patchUser($user, $data)
    {

        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            if(isset($data['name'])){
                if($user->getEmail()!=$data['email'] && $this->checkUserExists($data['email'])) {
                    throw new \Exception("Usuário com o endereço de e-mail {$data['email']} já existe.");
                }

                $user->setEmail($data['email']);
            }

            if(isset($data['first_name']))
                $user->setFirstName($data['first_name']);

            if(isset($data['last_name']))
                $user->setLastName($data['last_name']);

            if(isset($data['status'])){

                if($data['status'] == 'true')
                    $data['status'] = 1;
                else
                    $data['status'] = 0;

                $user->setStatus($data['status']);
            }

            if(isset($data['job'])){
                $job = $this->entityManager->getRepository(Job::class)->find($data['job']);

                if ($job==null) {
                    throw new \Exception('Not found job by ID');
                }

                $user->setIdJob($job);
            }

            if(isset($data['roles']))
                $this->assignRoles($user, $data['roles']);
            if(isset($data['work_groups']))
                $this->assignWorkGroup($user, $data['work_groups']);
            if(isset($data['departaments']))
                $this->assignDepartament($user, $data['departaments']);
            if($data['organizations'])
                $this->assignOrganization($user, $data['organizations']);

            // Apply changes to database.
            $this->entityManager->flush();
            $conn->commit();
            return $user;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * This method remove data of an existing user.
     */
    public function removeUser($data)
    {

        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            $this->entityManager->remove($data);
            $this->entityManager->flush();

            $conn->commit();

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }
    }

    /**
     * A helper method which assigns new roles to the user.
     */
    private function assignRoles($user, $roleIds)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            // Remove old user role(s).
            $user->getRoles()->clear();

            // Assign new role(s).
            foreach ($roleIds as $roleId) {
                $role = $this->entityManager->getRepository(Role::class)
                        ->find($roleId);
                if ($role==null) {
                    throw new \Exception('Not found role by ID');
                }

                $user->addRole($role);
            }

            $conn->commit();

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * A helper method which assigns new work_group's to the user.
     */
    private function assignWorkGroup($user, $workGroupIds)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();
            // Remove old user role(s).
            $user->getWorkGroup()->clear();

            // Assign new role(s).
            foreach ($workGroupIds as $groupId) {

                $group = $this->entityManager->getRepository(WorkGroup::class)
                        ->find($groupId);

                if ($group==null) {
                    throw new \Exception('Not found work_group by ID');
                }

                $user->addWorkGroup($group);
            }
            $conn->commit();
        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * A helper method which assigns new departaments to the user.
     */
    private function assignDepartament($user, $departamentsId)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();
            // Remove old user role(s).
            $user->getDepartament()->clear();

            // Assign new role(s).
            foreach ($departamentsId as $departamentId) {

                $departament = $this->entityManager->getRepository(Departament::class)
                        ->find($departamentId);

                if ($departament==null) {
                    throw new \Exception('Not found departament by ID');
                }

                $user->addDepartament($departament);
            }

            $conn->commit();

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * A helper method which assigns new organizations to the user.
     */
    private function assignOrganization($user, $organizationsId)
    {
        $conn = $this->entityManager->getConnection();

        try {

            $conn->beginTransaction();
            // Remove old user role(s).
            $user->getOrganization()->clear();

            // Assign new role(s).
            foreach ($organizationsId as $organizationId) {

                $organization = $this->entityManager->getRepository(Organization::class)
                        ->find($organizationId);

                if ($organization==null) {
                    throw new \Exception('Not found organization by ID');
                }

                $user->addOrganization($organization);
            }
            $conn->commit();

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * This method checks if at least one user presents, and if not, creates
     * 'Admin' user with email 'admin@example.com' and password 'Secur1ty'.
     */
    public function createAdminUserIfNotExists()
    {
        $conn = $this->entityManager->getConnection();

        try {

            $conn->beginTransaction();

            $user = $this->entityManager->getRepository(User::class)->findOneBy([]);
            if ($user==null) {
                $user = new User();
                $user->setEmail('root@windel.com.br');
                $user->setFirstName('admin');
                $bcrypt = new Bcrypt();
                $passwordHash = $bcrypt->create('r00tl33s');
                $user->setPassword($passwordHash);
                $user->setStatus(User::STATUS_ACTIVE);
                $user->setDateCreated(date('Y-m-d H:i:s'));

                $this->entityManager->persist($user);
                $this->entityManager->flush();
            }

            $conn->commit();

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * Checks whether an active user with given email address already exists in the database.
     */
    public function checkUserExists($email)
    {

        $user = $this->entityManager->getRepository(User::class)->findOneByEmail($email);
        return $user !== null;
    }

    /**
     * Checks that the given password is correct.
     */
    public function validatePassword($user, $password)
    {
        $bcrypt = new Bcrypt();
        $passwordHash = $user->getPassword();

        if ($bcrypt->verify($password, $passwordHash)) {
            return true;
        }

        return false;
    }

    /**
     * Generates a password reset token for the user. This token is then stored in database and
     * sent to the user's E-mail address. When the user clicks the link in E-mail message, he is
     * directed to the Set Password page.
     */
    public function generatePasswordResetToken($user)
    {
        // Generate a token.
        $token = Rand::getString(32, '0123456789abcdefghijklmnopqrstuvwxyz', true);
        $user->setPasswordResetToken($token);

        $currentDate = date('Y-m-d H:i:s');
        $user->setPasswordResetTokenCreationDate($currentDate);

        $this->entityManager->flush();

        $subject = 'Recuperação de Senha';
        $httpHost = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost';
        $passwordResetUrl = $httpHost.\Base\Module::ROOT_PATH.'/usuarios/set-password?token='.$token;

        $body = "Por favor, clique no link abaixo para alterar a senha de acesso:\r\n<br>";
        $body .= "<a href='".$passwordResetUrl."'> Clique aqui </a>\r\n<br>";
        $body .= "Se você não solicitou a mudança de senha, ignore essa mensagem.\r\n<br>";

        // Send email to user.
        $options['header'] = [
            'nome' => $user->getFirstName(),
            // 'texto-auxiliar' => $passwordResetUrl
        ];
        $options['content'] = [
            'recovery_link' => $passwordResetUrl,
        ];

        $this->windelMail->sendMail($user, $subject, "recovery_mail", $options, $body);

    }

    /**
     * Checks whether the given password reset token is a valid one.
     */
    public function validatePasswordResetToken($passwordResetToken)
    {
        $user = $this->entityManager->getRepository(User::class)
                ->findOneByPasswordResetToken($passwordResetToken);

        if($user==null) {
            return false;
        }

        $tokenCreationDate = $user->getPasswordResetTokenCreationDate();
        $tokenCreationDate = strtotime($tokenCreationDate);

        $currentDate = strtotime('now');

        if ($currentDate - $tokenCreationDate > 24*60*60) {
            return false; // expired
        }

        return true;
    }

    /**
     * This method sets new password by password reset token.
     */
    public function setNewPasswordByToken($passwordResetToken, $newPassword)
    {
        $conn = $this->entityManager->getConnection();

        try {

            $conn->beginTransaction();

            if (!$this->validatePasswordResetToken($passwordResetToken)) {
               return false;
            }

            $user = $this->entityManager->getRepository(User::class)
                    ->findOneByPasswordResetToken($passwordResetToken);

            if ($user==null) {
                return false;
            }

            // Set new password for user
            $bcrypt = new Bcrypt();
            $passwordHash = $bcrypt->create($newPassword);
            $user->setPassword($passwordHash);

            // Remove password reset token
            $user->setPasswordResetToken(null);
            $user->setPasswordResetTokenCreationDate(null);

            $this->entityManager->flush();

            $conn->commit();
            return true;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * This method is used to change the password for the given user. To change the password,
     * one must know the old password.
     */
    public function changePassword($user, $data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            $oldPassword = $data['old_password'];

            // Check that old password is correct
            if (!$this->validatePassword($user, $oldPassword)) {
                return false;
            }

            $newPassword = $data['new_password'];

            // Check password length
            if (strlen($newPassword)<6 || strlen($newPassword)>64) {
                return false;
            }

            // Set new password for user
            $bcrypt = new Bcrypt();
            $passwordHash = $bcrypt->create($newPassword);
            $user->setPassword($passwordHash);

            // Apply changes
            $this->entityManager->flush();

            $conn->commit();
            return true;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }
}

