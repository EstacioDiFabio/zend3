<?php
namespace Auth\Service;

use Auth\V1\Entity\Permission;
use Exception;

/**
 * This service is responsible for adding/editing permissions.
 */
class PermissionManager
{
    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * RBAC manager.
     * @var User\Service\RbacManager
     */
    private $rbacManager;

    /**
     * Constructs the service.
     */
    public function __construct($entityManager, $rbacManager)
    {
        $this->entityManager = $entityManager;
        $this->rbacManager = $rbacManager;
    }

    /**
     * Adds a new permission.
     * @param array $data
     */
    public function addPermission($data)
    {

        $conn = $this->entityManager->getConnection();

        try {

            $conn->beginTransaction();

            $existingPermission = $this->entityManager->getRepository(Permission::class)
                ->findOneByName($data['name']);
            if ($existingPermission!=null) {
                throw new \Exception('Permission with such name already exists');
            }

            $permission = new Permission();
            $permission->setName($data['name']);
            $permission->setDescription($data['description']);
            $permission->setDateCreated(date('Y-m-d H:i:s'));

            $this->entityManager->persist($permission);

            $this->entityManager->flush();

            // Reload RBAC container.
            $this->rbacManager->init(true);
            $conn->commit();

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * Updates an existing permission.
     * @param Permission $permission
     * @param array $data
     */
    public function updatePermission($permission, $data)
    {
        $conn = $this->entityManager->getConnection();

        try {

            $conn->beginTransaction();

            $existingPermission = $this->entityManager->getRepository(Permission::class)
                ->findOneByName($data['name']);
            if ($existingPermission!=null && $existingPermission!=$permission) {
                throw new \Exception('Another permission with such name already exists');
            }

            $permission->setName($data['name']);
            $permission->setDescription($data['description']);

            $this->entityManager->flush();

            // Reload RBAC container.
            $this->rbacManager->init(true);
            $conn->commit();

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * Deletes the given permission.
     */
    public function deletePermission($permission)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            $qb = $this->entityManager->createQueryBuilder();
            $alias = 'role';
            $finder = $alias.'.idOrganization ='.$data->getId();
            $hours = $qb->select($alias)
                        ->from(OrganizationOfficeHour::class, $alias)
                        ->where($finder)
                        ->getQuery();

            foreach($hours->getResult() as $result){
                $this->organizationOfficeHourManager->removeOrganizationHour($result);
            }

            $this->entityManager->remove($data);
            $this->entityManager->flush();

            // Reload RBAC container.
            $this->rbacManager->init(true);
            $conn->commit();

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * This method creates the default set of permissions if no permissions exist at all.
     */
    public function createDefaultPermissionsIfNotExist()
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            $permission = $this->entityManager->getRepository(Permission::class)
                ->findOneBy([]);
            if ($permission!=null)
                return; // Some permissions already exist; do nothing.

            $defaultPermissions = [
                'user.manage' => 'Manage users',
                'permission.manage' => 'Manage permissions',
                'role.manage' => 'Manage roles',
                'profile.any.view' => 'View anyone\'s profile',
                'profile.own.view' => 'View own profile',
            ];

            foreach ($defaultPermissions as $name=>$description) {
                $permission = new Permission();
                $permission->setName($name);
                $permission->setDescription($description);
                $permission->setDateCreated(date('Y-m-d H:i:s'));

                $this->entityManager->persist($permission);
            }

            $this->entityManager->flush();

            // Reload RBAC container.
            $this->rbacManager->init(true);
            $conn->commit();

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }
}

