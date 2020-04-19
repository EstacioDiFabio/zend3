<?php
namespace Auth\Service;

use Zend\Permissions\Rbac\Rbac;
use CMS\V1\Entity\User;

/**
 * This service is used for invoking user-defined RBAC dynamic assertions.
 */
class RbacAssertionManager
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Auth service.
     * @var Zend\Authentication\AuthenticationService
     */
    private $authService;

    /**
     * Constructs the service.
     */
    public function __construct($entityManager, $authService)
    {
        $this->entityManager = $entityManager;
        $this->authService = $authService;
    }

    /**
     * This method is used for dynamic assertions.
     */
    public function assert(Rbac $rbac, $permission, $params)
    {
        $currentUser = $this->entityManager->getRepository(User::class)
                                           ->findOneByEmail($this->authService->getIdentity());

        if ($params['user']==$currentUser->getEmail()) {

            if ($currentUser->getRolesAsString() == "Administrador") {
                return true;
            }

            $role = $this->entityManager->getRepository(Permission::class)
                                                  ->findOneByName($permission);

            if ($role !== null) {

                $dbAdapter = new Adapter([
                    'driver'   => 'Mysqli',
                    'database' => \Base\Module::DATABASE,
                    'username' => \Base\Module::USERNAME,
                    'password' => \Base\Module::PASSWORD,
                ]);

                $sql = new Sql($dbAdapter);
                $select = $sql->select();

                $select->from('role');
                $select->join('user_role',       'user_role.role_id = role.id');
                $select->join('role_permission', 'role.id = role_permission.role_id');
                $select->join('permission',      'role_permission.permission_id = permission.id');

                $select->where(['permission.name' => $permission,
                                'user_role.user_id' => $currentUser->getId()]);

                $select->limit(1);

                $statement = $sql->prepareStatementForSqlObject($select);
                $result = $statement->execute();

                foreach ($result as $key => $value) {

                    if($value['id']){
                        return true;
                    }
                }

            } else {

                return false;
            }

        } else {

            return false;
        }
    }
}