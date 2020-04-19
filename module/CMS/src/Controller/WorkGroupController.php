<?php
namespace CMS\Controller;

use CMS\Controller\CMSController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use CMS\V1\Entity\WorkGroup;
use CMS\Form\WorkGroupForm;

/**
 * This controller is responsible for workGroup management (adding, editing, viewing and delete workGroups ).
 */
class WorkGroupController extends CMSController
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * WorkGroup manager.
     * @var CMS\Service\WorkGroupManager
     */
    private $workGroupManager;

    /**
     * Array used to creating dinamic filters
     */
    private $searchArray = [
        'name' => 'Nome',
        'status' => 'Status'
    ];

    /**
     * Constructor.
     */
    public function __construct($entityManager, $workGroupManager)
    {
        $this->entityManager = $entityManager;
        $this->workGroupManager = $workGroupManager;
    }

    /**
     * This is the default "index" action of the controller. It displays the
     * list of work_groups.
     */
    public function indexAction()
    {
        $workGroups = $this->entityManager->getRepository(WorkGroup::class)->findBy([], ['id'=>'ASC']);

        return new ViewModel([
            'workGroups' => $workGroups,
            'search' => $this->searchArray,
            'operators' => $this->searchMethods
        ]);
    }

    /**
     * The "Search" action is used to filter data in the search.
     */
    public function searchAction()
    {

        try {
            $qb = $this->entityManager->createQueryBuilder();
            $alias = "wg";

            if ($this->getRequest()->isGet()) {

                $search = $this->params()->fromQuery();
                $finder = $this->windelFilter()->performWhereString($search, $alias);

            } else {
                $finder = $alias.".id > 0";
            }

            if(!$finder)
                return false;

            $workGroups = $qb->select($alias)
                       ->from(WorkGroup::class, $alias)
                       ->where($finder)
                       ->getQuery();

            $returnArr = array();

            if(count($workGroups->getResult()) == 0) {
                $returnArr['data'] = [];
                throw new Exception("Error Processing Request", 1);

            } else {

                foreach ($workGroups->getResult() as $key => $workGroup) {

                    $returnArr[$key] = [

                        '0' => $this->windelHtml()->getLink('grupos', $workGroup->getId(), $workGroup->getName(), 'Visualizar'),
                        '1' => $workGroup->getStatusToggle(),
                        '2' => $this->windelHtml()->getActionButton('grupos', $workGroup->getId()),
                    ];
                }
            }

            return new JsonModel(['data' => $returnArr]);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }
    }

    /**
     * This action displays a page allowing to add a new work_group.
     */
    public function addAction()
    {
        // Create workGroup form
        $form = new WorkGroupForm('create', $this->entityManager);

        // Check if workGroup has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();
            if(!isset($data['status'])){
                $data['status'] = 0;
            }
            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();
                // Add group.
                $workGroup = $this->workGroupManager->addWorkGroup($data);

                if(is_string($workGroup)){
                    $this->flashMessenger()->addErrorMessage($workGroup);
                } else {

                    $this->flashMessenger()->addSuccessMessage("Grupo removido com sucesso!");
                    // Redirect to "view" page
                    return $this->redirect()->toRoute('groups',
                            ['action'=>'view', 'id'=>$workGroup->getId()]);
                }


            }
        }

        return new ViewModel([
                'form' => $form,
            ]);
    }

    /**
     * The "view" action displays a page allowing to view workGroup's details.
     */
    public function viewAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Find a user with such ID.
        $workGroup = $this->entityManager->getRepository(WorkGroup::class)->find($id);

        if ($workGroup == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return new ViewModel([
            'workGroup' => $workGroup
        ]);
    }

    /**
     * The "edit" action displays a page allowing to edit workGroup.
     */
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $workGroup = $this->entityManager->getRepository(WorkGroup::class)->find($id);

        if ($workGroup == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Create user form
        $form = new WorkGroupForm('update', $this->entityManager, $workGroup);

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();
            if(!isset($data['status'])){
                $data['status'] = 0;
            }
            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                // Update the user.
                $workGroup = $this->workGroupManager->updateWorkGroup($workGroup, $data);

                if(is_string($workGroup)){
                    $this->flashMessenger()->addErrorMessage($workGroup);
                } else {

                    $this->flashMessenger()->addSuccessMessage("Grupo alterado com sucesso!");
                    // Redirect to "view" page
                    return $this->redirect()->toRoute('groups',
                            ['action'=>'view', 'id'=>$workGroup->getId()]);
                }

            }
        } else {
            $form->setData(array(
                    'name'=>$workGroup->getName(),
                    'status'=>$workGroup->getStatus(),
                ));
        }

        return new ViewModel(array(
            'wgroup' => $workGroup,
            'form' => $form
        ));
    }

    /**
     * The "remove" action exclude a item from database.
     */
    public function removeAction()
    {
        // $id = $this->params()->fromRoute('id');
        $id = $this->params()->fromPost('id');

        $workGroup = $this->entityManager->getRepository(WorkGroup::class)
                    ->findOneById($id);

        if ($workGroup == null) {
          $this->getResponse()->setStatusCode(404);
          return;
        }

        $workGroup = $this->workGroupManager->removeWorkGroup($workGroup);

        if(is_string($workGroup)){
            $this->flashMessenger()->addErrorMessage($workGroup);
        } else {

            $this->flashMessenger()->addSuccessMessage("Grupo removido com sucesso!");
            // Redirect the group to "index" page.
            return $this->redirect()->toRoute('groups', ['action'=>'index']);
        }

    }

    /**
     * The ToggleActive action change status more quickly.
     */
    public function toggleActiveAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();

            $workGroup = $this->entityManager->getRepository(WorkGroup::class)->find($data['id']);

            if ($workGroup == null) {
                $this->getResponse()->setStatusCode(404);
                return;
            }

            $workGroup = $this->workGroupManager->patchWorkGroup($workGroup, $data);

            if(is_string($workGroup)){
                $this->flashMessenger()->addErrorMessage($workGroup);
            } else {
                $this->flashMessenger()->addSuccessMessage("Grupo alterado com sucesso!");
            }

            return true;
        }
    }

}