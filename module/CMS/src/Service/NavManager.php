<?php
namespace CMS\Service;

use Auth\V1\Entity\Permission;
use CMS\V1\Entity\User;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;

/**
 * This service is responsible for determining which items should be in the main menu.
 * The items may be different depending on whether the user is authenticated or not.
 */
class NavManager
{
    /**
     * Auth service.
     * @var Zend\Authentication\Authentication
     */
    private $authService;

    /**
     * Url view helper.
     * @var Zend\View\Helper\Url
     */
    private $urlHelper;

    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Constructs the service.
     */
    public function __construct($authService, $urlHelper, $entityManager)
    {
        $this->authService = $authService;
        $this->urlHelper = $urlHelper;
        $this->entityManager = $entityManager;
    }

    /**
     * This method returns menu items depending on whether user has logged in or not.
     */
    public function getMenuItems()
    {
        $url = $this->urlHelper;
        $items = [];

        // Display "Login" menu item for not authorized user only. On the other hand,
        // display "Admin" and "Logout" menu items only for authorized users.
        if (!$this->authService->hasIdentity()) {

            $items[] = [
                'id' => 'login',
                'label' => 'ENTRAR',
                'link'  => $url('login'),
                'float' => 'right'
            ];

        } else {

            if($this->checkPermission('basic.manage')){

                $items[] = [
                    'id' => 'management',
                    'label' => 'GESTÃO',
                    'dropdown' => [
                        [
                            'id' => 'users',
                            'label' => '<i class="fas fa-user"></i> USUÁRIOS',
                            'link' => $url('users')
                        ],
                        [
                            'id' => 'departament',
                            'label' => '<i class="fas fa-chalkboard-teacher"></i> SETORES',
                            'link' => $url('departaments')
                        ],
                    ]
                ];
            }

            if ($this->checkPermission('role.manage') ||
                $this->checkPermission('permission.manage') ||
                $this->checkPermission('mail-template.manage')
            ) {

                $items[1] = [
                    'id' => 'admin',
                    'label' => 'ADMINISTRAR',
                    'dropdown' => []
                ];

                if ($this->checkPermission('role.manage')) {

                    $array = [
                            'id' => 'roles',
                            'label' => '<i class="fas fa-eye"></i> FUNÇÕES',
                            'link' => $url('roles')
                        ];

                    array_push($items[1]['dropdown'], $array);
                }


                if ($this->checkPermission('permission.manage')) {
                    $array = [
                        'id' => 'permissions',
                        'label' => '<i class="fas fa-shield-alt"></i> PERMISSÕES',
                        'link' => $url('permissions')
                    ];

                    array_push($items[1]['dropdown'], $array);
                }

                if ($this->checkPermission('mail_template.manage')) {

                    $array = [
                        'id' => 'mail-templates',
                        'label' => '<i class="fas fa-envelope-open"></i> TEMPLATES DE E-MAIL',
                        'link' => $url('mailTemplates')
                    ];

                    array_push($items[1]['dropdown'], $array);
                }

            }

            $items[] = [
                'id' => 'logout',
                'label' => strtoupper($this->authService->getIdentity()),
                'float' => 'right',
                'dropdown' => [
                    [
                        'id' => 'logout',
                        'label' => '<i class="fas fa-sign-out-alt"></i> SAIR',
                        'link' => $url('logout')
                    ],
                ]
            ];
        }

        return $items;
    }

    public function checkPermission($permission)
    {

        $currentUser = $this->entityManager->getRepository(User::class)
                                           ->findOneByEmail($this->authService->getIdentity());

        if ($currentUser->getEmail()) {

            if($currentUser->getRolesAsString() == "Administrador"){
                return true;
            }

            $role = $this->entityManager->getRepository(Permission::class)
                                                  ->findOneByName($permission);

            if (isset($role) && $role->getId() !== null) {

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


