<?php
namespace Auth\Controller;

use CMS\Controller\CMSController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Auth\V1\Entity\Permission;
use Auth\Form\PermissionForm;

/**
 * This controller is responsible for permission management (adding, editing,
 * viewing, deleting).
 */
class PermissionController extends CMSController
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Permission manager.
     * @var Auth\Service\PermissionManager
     */
    private $permissionManager;

    /**
     * Array used to creating dinamic filters
     */
    private $searchArray = [
        'name' => 'Nome',
    ];

    /**
     * Constructor.
     */
    public function __construct($entityManager, $permissionManager)
    {
        $this->entityManager = $entityManager;
        $this->permissionManager = $permissionManager;
    }

    /**
     * This is the default "index" action of the controller. It displays the
     * list of permission.
     */
    public function indexAction()
    {
        $permissions = $this->entityManager->getRepository(Permission::class)
                ->findBy([], ['name'=>'ASC']);

        return new ViewModel([
            'permissions' => $permissions,
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
        $alias = "p";

        if ($this->getRequest()->isGet()) {

            $search = $this->params()->fromQuery();
            $finder = $this->windelFilter()->performWhereString($search, $alias);

        } else {
            $finder = $alias.".id > 0";
        }

        $permissions = $qb->select($alias)
                   ->from(Permission::class, $alias)
                   ->where($finder)
                   ->getQuery();

        $returnArr = array();

        if(count($permissions->getResult()) == 0) {
            $returnArr['data'] = [];
        } else {

            foreach ($permissions->getResult() as $key => $permission) {

                $returnArr[$key] = [

                    '0' => $this->windelHtml()->getLink('permissions', $permission->getId(), $permission->getName(), 'Visualizar'),
                    '1' => $permission->getDescription(),
                    '2' => $this->windelHtml()->getActionButton('permissions', $permission->getId()),
                ];

            }
        }

        return new JsonModel(['data' => $returnArr]);
    }

    /**
     * This action displays a page allowing to add a new permission.
     */
    public function addAction()
    {
        // Create form
        $form = new PermissionForm('create', $this->entityManager);

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                // Add permission.
                $permission = $this->permissionManager->addPermission($data);

                if(is_string($permission)){
                    $this->flashMessenger()->addErrorMessage($permission);
                } else {

                    $this->flashMessenger()->addSuccessMessage("Permissão criada com sucesso!");
                   // Redirect to "index" page
                    return $this->redirect()->toRoute('permissions', ['action'=>'index']);
                }

            }
        }

        return new ViewModel([
                'form' => $form
            ]);
    }

    /**
     * The "view" action displays a page allowing to view permission's details.
     */
    public function viewAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Find a permission with such ID.
        $permission = $this->entityManager->getRepository(Permission::class)
                ->find($id);

        if ($permission == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return new ViewModel([
            'permission' => $permission
        ]);
    }

    /**
     * This action displays a page allowing to edit an existing permission.
     */
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $permission = $this->entityManager->getRepository(Permission::class)
                ->find($id);

        if ($permission == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Create form
        $form = new PermissionForm('update', $this->entityManager, $permission);

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
                $permission = $this->permissionManager->updatePermission($permission, $data);

                if(is_string($permission)){
                    $this->flashMessenger()->addErrorMessage($permission);
                } else {

                    $this->flashMessenger()->addSuccessMessage("Permissão alterada com sucesso!");
                    // Redirect to "index" page
                    return $this->redirect()->toRoute('permissions', ['action'=>'index']);
                }


            }
        } else {
            $form->setData(array(
                    'name'=>$permission->getName(),
                    'description'=>$permission->getDescription()
                ));
        }

        return new ViewModel([
                'form' => $form,
                'permission' => $permission
            ]);
    }

    /**
     * This action deletes a permission.
     */
    public function removeAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $permission = $this->entityManager->getRepository(Permission::class)
                ->find($id);

        if ($permission == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Delete permission.
        $permission = $this->permissionManager->deletePermission($permission);

        if(is_string($permission)){
            $this->flashMessenger()->addErrorMessage($permission);
        } else {

            $this->flashMessenger()->addSuccessMessage("Permissão removida com sucesso!");
            // Redirect to "index" page
            return $this->redirect()->toRoute('permissions', ['action'=>'index']);
        }

    }
}






