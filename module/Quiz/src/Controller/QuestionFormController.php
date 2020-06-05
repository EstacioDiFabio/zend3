<?php

namespace Quiz\Controller;

use CMS\Controller\CMSController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Quiz\V1\Entity\QuestionForm;
use Quiz\Form\QuestionFormForm;

/**
 * This controller is responsible for QuestionForm management (adding, editing, viewing and delete departaments ).
 */
class QuestionFormController extends CMSController
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * QuestionForm manager.
     * @var CMS\Service\QuestionFormManager
     */
    private $questionFormManager;

    /**
     * Array used to creating dinamic filters
     */
    private $searchArray = [
        'name'               => 'Nome',
        'status'             => 'Status'
    ];

    /**
     * Constructor.
     */
    public function __construct($entityManager, $questionFormManager)
    {
        $this->entityManager = $entityManager;
        $this->questionFormManager  = $questionFormManager;
    }

    /**
     * This is the default "index" action of the controller. It displays the
     * list of QuestionForms.
     */
    public function indexAction()
    {
        $forms = $this->entityManager->getRepository(QuestionForm::class)
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
        $alias = "qf";

        if ($this->getRequest()->isGet()) {

            $search = $this->params()->fromQuery();
            $finder = $this->csecFilter()->performWhereString($search, $alias);

        } else {
            $finder = $alias.".id > 0";
        }

        $forms = $qb->select($alias)
                    ->from(QuestionForm::class, $alias)
                    ->where($finder)
                    ->getQuery();

        $returnArr = [];

        if(count($forms->getResult()) == 0) {
            $returnArr['data'] = [];
        } else {
            foreach ($forms->getResult() as $key => $form) {

                $returnArr[$key] = [

                    '0' => $this->csecHtml()->getLink('formulario', $form->getId(), $form->getName(), 'Visualizar'),
                    '1' => $form->getStatusToggle(),
                    '2' => $this->csecHtml()->getActionButton('formulario', $form->getId()),
                ];

            }
        }

        return new JsonModel(['data' => $returnArr]);
    }

    /**
     * This action displays a page allowing to add a new QuestionForm.
     */
    public function addAction()
    {
        // Create departament form
        $form = new QuestionFormForm('create', $this->entityManager);

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
                $data['name'] = $this->csecFilter()->slugfy($data['name']);
                $data['local'] = $this->csecFilter()->slugfy($data['local']);
                // Add departament.
                $questionForm = $this->questionFormManager->addQuestionForm($data);

                if(is_string($questionForm)){
                    $this->flashMessenger()->addErrorMessage($questionForm);
                } else {
                    $this->flashMessenger()->addSuccessMessage("Formulário criado com sucesso!");
                    // Redirect to "view" page
                    return $this->redirect()->toRoute('quizForm',
                            ['action'=>'view', 'id' => $questionForm->getId()]);

                }
            }
        }

        return new ViewModel(['form' => $form]);
    }

    /**
     * The "view" action displays a page allowing to view QuestionForm's details.
     */
    public function viewAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);

        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Find a quiz with such ID.
        $questionForm = $this->entityManager->getRepository(QuestionForm::class)
                                            ->find($id);

        if ($questionForm == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return new ViewModel(['questionForm' => $questionForm]);
    }

    /**
     * The "edit" action displays a page allowing to edit QuestionForm.
     */
    public function editAction()
    {

        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $questionForm = $this->entityManager->getRepository(QuestionForm::class)
                                            ->find($id);

        if ($questionForm == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Create questionForm form
        $form = new QuestionFormForm('update', $this->entityManager, $questionForm);

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
                $data['name'] = $this->csecFilter()->slugfy($data['name']);
                $data['local'] = $this->csecFilter()->slugfy($data['local']);

                // Update the questionForm.
                $questionFormS = $this->questionFormManager->updateQuestionForm($questionForm, $data);

                if(is_string($questionFormS)){
                    $this->flashMessenger()->addErrorMessage($questionFormS);
                } else {
                    $this->flashMessenger()->addSuccessMessage("Formulário alterado com sucesso!");
                    // Redirect to "view" page
                    return $this->redirect()->toRoute('quizForm',
                            ['action'=>'view', 'id' => $questionForm->getId()]);

                }
            }
        } else {

            $form->setData([
                'name'     => $questionForm->getName(),
                'local'    => $questionForm->getLocal(),
                'sequence' => $questionForm->getSequence(),
                'status'   => $questionForm->getStatus()
            ]);
        }

        return new ViewModel(['questionForm' => $questionForm,
                              'form' => $form]);
    }

    /**
     * The "remove" action exclude a item from database.
     */
    public function removeAction()
    {

        $id = $this->params()->fromPost('id');

        $questionForm = $this->entityManager->getRepository(QuestionForm::class)
                                            ->findOneById($id);

        if ($questionForm == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $questionForm = $this->questionFormManager->removeQuestionForm($questionForm);

        if (is_string($questionForm)) {
            $this->flashMessenger()->addErrorMessage($questionForm);
        } else {
            $this->flashMessenger()->addSuccessMessage("Formulário removida com sucesso!");
            // Redirect the quiz to "index" page.
            return $this->redirect()->toRoute('quizForm', ['action'=>'index']);
        }

    }

    /**
     * The ToggleActive action change status more quickly.
     */
    public function toggleActiveAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();

            $questionForm = $this->entityManager->getRepository(QuestionForm::class)->find($data['id']);

            if ($questionForm == null) {
                $this->getResponse()->setStatusCode(404);
                return;
            }

            $questionForm = $this->questionFormManager->patchQuestionForm($questionForm, $data);
            if(is_string($questionForm)){
                $this->flashMessenger()->addErrorMessage($questionForm);
            } else {
                $this->flashMessenger()->addSuccessMessage("Formulário alterado com sucesso!");
            }

            return true;
        }
    }

}