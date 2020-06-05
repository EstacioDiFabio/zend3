<?php

namespace Quiz\Controller;

use CMS\Controller\CMSController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Quiz\V1\Entity\Produto;
use Quiz\Form\ProdutoForm;

/**
 * This controller is responsible for Produto management (adding, editing, viewing and delete products ).
 */
class ProdutoController extends CMSController
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Produto manager.
     * @var CMS\Service\ProdutoManager
     */
    private $produtoManager;

    /**
     * Array used to creating dinamic filters
     */
    private $searchArray = [
        'nome'               => 'Nome',
        'status'             => 'Status'
    ];

    /**
     * Constructor.
     */
    public function __construct($entityManager, $produtoManager)
    {
        $this->entityManager = $entityManager;
        $this->produtoManager  = $produtoManager;
    }

    /**
     * This is the default "index" action of the controller. It displays the
     * list of Produtos.
     */
    public function indexAction()
    {
        $forms = $this->entityManager->getRepository(Produto::class)
                                     ->findBy([], ['id'=>'ASC']);

        return new ViewModel([
            'forms'     => $forms,
            'search'    => $this->searchArray,
            'operators' => $this->searchMethods]);
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
            $finder = $this->csecFilter()->performWhereString($search, $alias);

        } else {
            $finder = $alias.".id > 0";
        }

        $forms = $qb->select($alias)
                    ->from(Produto::class, $alias)
                    ->where($finder)
                    ->getQuery();

        $returnArr = [];

        if(count($forms->getResult()) == 0) {
            $returnArr['data'] = [];
        } else {
            foreach ($forms->getResult() as $key => $form) {

                $returnArr[$key] = [

                    '0' => $this->csecHtml()->getLink('produto', $form->getId(), $form->getName(), 'Visualizar'),
                    '1' => $form->getStatusToggle(),
                    '2' => $this->csecHtml()->getActionButton('produto', $form->getId()),
                ];

            }
        }

        return new JsonModel(['data' => $returnArr]);
    }

    /**
     * This action displays a page allowing to add a new Produto.
     */
    public function addAction()
    {
        // Create departament form
        $form = new ProdutoForm('create', $this->entityManager);

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
                $produto = $this->produtoManager->addProduto($data);

                if(is_string($produto)){
                    $this->flashMessenger()->addErrorMessage($produto);
                } else {
                    $this->flashMessenger()->addSuccessMessage("Produto criado com sucesso!");
                    // Redirect to "view" page
                    return $this->redirect()->toRoute('produto',
                            ['action'=>'view', 'id' => $produto->getId()]);

                }
            }
        }

        return new ViewModel(['form' => $form]);
    }

    /**
     * The "view" action displays a page allowing to view Produto's details.
     */
    public function viewAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);

        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Find a quiz with such ID.
        $produto = $this->entityManager->getRepository(Produto::class)
                                       ->find($id);

        if ($produto == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return new ViewModel(['produto' => $produto]);
    }

    /**
     * The "edit" action displays a page allowing to edit Produto.
     */
    public function editAction()
    {

        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $produto = $this->entityManager->getRepository(Produto::class)
                                       ->find($id);

        if ($produto == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Create Produto form
        $form = new ProdutoForm('update', $this->entityManager, $produto);

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
                // Update the Produto.
                $produtoS = $this->produtoManager->updateProduto($produto, $data);

                if(is_string($produtoS)){
                    $this->flashMessenger()->addErrorMessage($produtoS);
                } else {
                    $this->flashMessenger()->addSuccessMessage("Produto alterado com sucesso!");
                    // Redirect to "view" page
                    return $this->redirect()->toRoute('produto',
                            ['action'=>'view', 'id' => $produto->getId()]);

                }

            }
        } else {
            $form->setData([
                    'nome'=> $produto->getNome(),
                    'status'=> $produto->getStatus()
                ]);
        }

        return new ViewModel(['produto' => $produto,
                              'form' => $form]);
    }

    /**
     * The "remove" action exclude a item from database.
     */
    public function removeAction()
    {

        $id = $this->params()->fromPost('id');

        $produto = $this->entityManager->getRepository(Produto::class)
                                       ->findOneById($id);

        if ($produto == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $produto = $this->produtoManager->removeProduto($produto);

        if (is_string($produto)) {
            $this->flashMessenger()->addErrorMessage($produto);
        } else {
            $this->flashMessenger()->addSuccessMessage("Produto removida com sucesso!");
            // Redirect the quiz to "index" page.
            return $this->redirect()->toRoute('produto', ['action'=>'index']);
        }

    }

    /**
     * The ToggleActive action change status more quickly.
     */
    public function toggleActiveAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();

            $produto = $this->entityManager->getRepository(Produto::class)->find($data['id']);

            if ($produto == null) {
                $this->getResponse()->setStatusCode(404);
                return;
            }

            $produto = $this->produtoManager->patchProduto($produto, $data);
            if(is_string($produto)){
                $this->flashMessenger()->addErrorMessage($produto);
            } else {
                $this->flashMessenger()->addSuccessMessage("Produto alterado com sucesso!");
            }

            return true;
        }
    }

}