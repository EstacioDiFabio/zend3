<?php
namespace CMS\Controller;

use CMS\Controller\CMSController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use CMS\V1\Entity\Departament;
use CMS\Form\DepartamentForm;

/**
 * This controller is responsible for departament management (adding, editing, viewing and delete departaments ).
 */
class DepartamentController extends CMSController
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Departament manager.
     * @var CMS\Service\DepartamentManager
     */
    private $departamentManager;

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
    public function __construct($entityManager, $departamentManager)
    {
        $this->entityManager = $entityManager;
        $this->departamentManager = $departamentManager;
    }

    /**
     * This is the default "index" action of the controller. It displays the
     * list of departaments.
     */
    public function indexAction()
    {
        $departaments = $this->entityManager->getRepository(Departament::class)->findBy([], ['id'=>'ASC']);
        return new ViewModel([
            'departaments' => $departaments,
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
        $alias = "d";

        if ($this->getRequest()->isGet()) {

            $search = $this->params()->fromQuery();
            $finder = $this->csecFilter()->performWhereString($search, $alias);

        } else {
            $finder = $alias.".id > 0";
        }

        $departaments = $qb->select($alias)
                           ->from(Departament::class, $alias)
                           ->where($finder)
                           ->getQuery();

        $returnArr = array();

        if(count($departaments->getResult()) == 0) {
            $returnArr['data'] = [];
        } else {
            foreach ($departaments->getResult() as $key => $departament) {

                $returnArr[$key] = [

                    '0' => $this->csecHtml()->getLink('setores', $departament->getId(), $departament->getName(), 'Visualizar'),
                    '1' => $departament->getStatusToggle(),
                    '2' => $this->csecHtml()->getActionButton('setores', $departament->getId()),
                ];

            }
        }

        return new JsonModel(['data' => $returnArr]);
    }

    /**
     * This action displays a page allowing to add a new departament.
     */
    public function addAction()
    {
        // Create departament form
        $form = new DepartamentForm('create', $this->entityManager);

        // Check if departament has submitted the form
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

                // Add departament.
                $departament = $this->departamentManager->addDepartament($data);

                if(is_string($departament)){
                    $this->flashMessenger()->addErrorMessage($departament);
                } else {
                    $this->flashMessenger()->addSuccessMessage("Setor criado com sucesso!");
                    // Redirect to "view" page
                    return $this->redirect()->toRoute('departaments',
                            ['action'=>'view', 'id' => $departament->getId()]);

                }
            }
        }

        return new ViewModel([
                'form' => $form,
            ]);
    }

    /**
     * The "view" action displays a page allowing to view departament's details.
     */
    public function viewAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Find a departament with such ID.
        $departament = $this->entityManager->getRepository(Departament::class)->find($id);

        if ($departament == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return new ViewModel([
            'departament' => $departament
        ]);
    }

    /**
     * The "edit" action displays a page allowing to edit departament.
     */
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $departament = $this->entityManager->getRepository(Departament::class)->find($id);

        if ($departament == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Create user form
        $form = new DepartamentForm('update', $this->entityManager, $departament);

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

                // Update the departament.
                $departament = $this->departamentManager->updateDepartament($departament, $data);

                if(is_string($departament)){
                    $this->flashMessenger()->addErrorMessage($departament);
                } else {
                    $this->flashMessenger()->addSuccessMessage("Setor alterado com sucesso!");
                    // Redirect to "view" page
                    return $this->redirect()->toRoute('departaments',
                            ['action'=>'view', 'id' => $departament->getId()]);

                }

            }
        } else {
            $form->setData(array(
                    'name'=>$departament->getName(),
                    'status'=>$departament->getStatus(),
                ));
        }

        return new ViewModel(array(
            'departament' => $departament,
            'form' => $form
        ));
    }

    /**
     * The "remove" action exclude a item from database.
     */
    public function removeAction()
    {

        $id = $this->params()->fromPost('id');

        $departament = $this->entityManager->getRepository(Departament::class)
                    ->findOneById($id);

        if ($departament == null) {
          $this->getResponse()->setStatusCode(404);
          return;
        }

        $departament = $this->departamentManager->removeDepartament($departament);

        if(is_string($departament)){
            $this->flashMessenger()->addErrorMessage($departament);
        } else {
            $this->flashMessenger()->addSuccessMessage("Setor removido com sucesso!");
            // Redirect the job to "index" page.
            return $this->redirect()->toRoute('departaments', ['action'=>'index']);
        }

    }

    /**
     * The ToggleActive action change status more quickly.
     */
    public function toggleActiveAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();

            $departament = $this->entityManager->getRepository(Departament::class)->find($data['id']);

            if ($departament == null) {
                $this->getResponse()->setStatusCode(404);
                return;
            }

            $departament = $this->departamentManager->patchDepartament($departament, $data);
            if(is_string($departament)){
                $this->flashMessenger()->addErrorMessage($departament);
            } else {
                $this->flashMessenger()->addSuccessMessage("Setor alterado com sucesso!");
            }

            return true;
        }
    }

}