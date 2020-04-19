<?php
namespace Auth\Controller;

use CMS\Controller\CMSController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Auth\V1\Entity\Role;
use Auth\V1\Entity\Permission;
use Auth\Form\RoleForm;
use Auth\Form\RolePermissionsForm;

/**
 * This controller is responsible for role management (adding, editing,
 * viewing, deleting).
 */
class RoleController extends CMSController
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Role manager.
     * @var Auth\Service\RoleManager
     */
    private $roleManager;

    /**
     * Array used to creating dinamic filters
     */
    private $searchArray = [
        'name' => 'Nome',
    ];

    /**
     * Constructor.
     */
    public function __construct($entityManager, $roleManager)
    {
        $this->entityManager = $entityManager;
        $this->roleManager = $roleManager;
    }

    /**
     * This is the default "index" action of the controller. It displays the
     * list of roles.
     */
    public function indexAction()
    {
        $roles = $this->entityManager->getRepository(Role::class)
                ->findBy([], ['id'=>'ASC']);

        return new ViewModel([
            'roles' => $roles,
            'search' => $this->searchArray,
            'operators' => $this->searchMethods
        ]);
    }

    /**
     * The "Search" action is used to filter data in the search.
     */
    public function searchAction()
    {

        $qb = $this->entityManager->createQueryBuilder();
        $alias = "r";

        if ($this->getRequest()->isGet()) {

            $search = $this->params()->fromQuery();
            $finder = $this->windelFilter()->performWhereString($search, $alias);

        } else {
            $finder = $alias.".id > 0";
        }

        $roles = $qb->select($alias)
                   ->from(Role::class, $alias)
                   ->where($finder)
                   ->getQuery();

        $returnArr = array();

        if(count($roles->getResult()) == 0) {
            $returnArr['data'] = [];
        } else {
            foreach ($roles->getResult() as $key => $role) {

                $returnArr[$key] = [

                    '0' => $this->windelHtml()->getLink('funcoes', $role->getId(), $role->getName(), 'Visualizar'),
                    '1' => $role->getDescription(),
                    '2' => $this->windelHtml()->getActionButton('funcoes', $role->getId()),
                ];

            }
        }

        return new JsonModel(['data' => $returnArr]);
    }

    /**
     * This action displays a page allowing to add a new role.
     */
    public function addAction()
    {
        // Create form
        $form = new RoleForm('create', $this->entityManager);

        $roleList = [];
        $roles = $this->entityManager->getRepository(Role::class)
                ->findBy([], ['name'=>'ASC']);
        foreach ($roles as $role) {
            $roleList[$role->getId()] = $role->getName();
        }
        $form->get('inherit_roles[]')->setValueOptions($roleList);

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                // Add role.
                $role = $this->roleManager->addRole($data);

                if(is_string($role)){
                    $this->flashMessenger()->addErrorMessage($role);
                } else {

                    $this->flashMessenger()->addSuccessMessage("Função criada com sucesso!");
                    // Redirect to "index" page
                    return $this->redirect()->toRoute('roles', ['action'=>'index']);
                }

            }
        }

        return new ViewModel([
                'form' => $form
            ]);
    }

    /**
     * The "view" action displays a page allowing to view role's details.
     */
    public function viewAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Find a role with such ID.
        $role = $this->entityManager->getRepository(Role::class)
                ->find($id);

        if ($role == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $allPermissions = $this->entityManager->getRepository(Permission::class)
                ->findBy([], ['name'=>'ASC']);

        $effectivePermissions = $this->roleManager->getEffectivePermissions($role);

        return new ViewModel([
            'role' => $role,
            'allPermissions' => $allPermissions,
            'effectivePermissions' => $effectivePermissions
        ]);
    }

    /**
     * This action displays a page allowing to edit an existing role.
     */
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $role = $this->entityManager->getRepository(Role::class)
                ->find($id);

        if ($role == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Create form
        $form = new RoleForm('update', $this->entityManager, $role);

        $roleList = [];
        $roles = $this->entityManager->getRepository(Role::class)
                ->findBy([], ['name'=>'ASC']);
        foreach ($roles as $role2) {
            $roleList[$role2->getId()] = $role2->getName();
        }
        $form->get('inherit_roles[]')->setValueOptions($roleList);

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                // Update permission.
                $role = $this->roleManager->updateRole($role, $data);

                if(is_string($role)){
                    $this->flashMessenger()->addErrorMessage($role);
                } else {

                    $this->flashMessenger()->addSuccessMessage("Função alterada com sucesso!");
                    // Redirect to "index" page
                    return $this->redirect()->toRoute('roles', ['action'=>'index']);
                }

            }
        } else {
            $form->setData(array(
                    'name'=>$role->getName(),
                    'description'=>$role->getDescription()
                ));
        }

        return new ViewModel([
                'form' => $form,
                'role' => $role
            ]);
    }

    /**
     * The "editPermissions" action allows to edit permissions assigned to the given role.
     */
    public function editPermissionsAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $role = $this->entityManager->getRepository(Role::class)
                ->find($id);

        if ($role == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $allPermissions = $this->entityManager->getRepository(Permission::class)
                ->findBy([], ['name'=>'ASC']);

        $effectivePermissions = $this->roleManager->getEffectivePermissions($role);

        // Create form
        $form = new RolePermissionsForm($this->entityManager);
        foreach ($allPermissions as $permission) {
            $label = $permission->getName();
            $isDisabled = false;
            if (isset($effectivePermissions[$permission->getName()]) && $effectivePermissions[$permission->getName()]=='inherited') {
                $label .= ' (inherited)';
                $isDisabled = true;
            }
            $form->addPermissionField($permission->getName(), $label, $isDisabled);
        }

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                // Update permissions.
                $roled = $this->roleManager->updateRolePermissions($role, $data);

                if(is_string($roled)){
                    $this->flashMessenger()->addErrorMessage($roled);
                } else {

                    $this->flashMessenger()->addSuccessMessage("Permissões atualizadas para a função!");
                    // Redirect to "index" page
                    return $this->redirect()->toRoute('roles', ['action'=>'view', 'id'=>$role->getId()]);
                }

            }
        } else {

            $data = [];
            foreach ($effectivePermissions as $name=>$inherited) {
                $data['permissions'][$name] = 1;
            }

            $form->setData($data);
        }

        $errors = $form->getMessages();

        return new ViewModel([
                'form' => $form,
                'role' => $role,
                'allPermissions' => $allPermissions,
                'effectivePermissions' => $effectivePermissions
            ]);
    }

    /**
     * This action deletes a permission.
     */
    public function deleteAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $role = $this->entityManager->getRepository(Role::class)
                ->find($id);

        if ($role == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Delete role.
        $role = $this->roleManager->deleteRole($role);

        if(is_string($role)){
            $this->flashMessenger()->addErrorMessage($role);
        } else {

            $this->flashMessenger()->addSuccessMessage("Função removida com sucesso!");
            // Redirect to "index" page
            return $this->redirect()->toRoute('roles', ['action'=>'index']);
        }

    }
}




