<?php

namespace Quiz\Controller;

use CMS\Controller\CMSController;
use Quiz\V1\Entity\Question;
use Quiz\V1\Entity\QuestionForm;
use Quiz\V1\Entity\QuestionField;
use Quiz\Form\QuestionDataForm;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;

/**
 * This controller is responsible for Question management (adding, editing, viewing and delete questions ).
 */
class QuestionController extends CMSController
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * QuestionForm manager.
     * @var CMS\Service\QuestionManager
     */
    private $questionManager;

    /**
     * QuestionFieldsForm manager.
     * @var CMS\Service\questionFieldManager
     */
    private $questionFieldManager;

    /**
     * Array used to creating dinamic filters
     */
    private $searchArray = [
        'q.name'      => 'Nome',
        'qf.name'     => 'Formulário',
        'q.type'      => 'Tipo',
        'q.sequence'  => 'Ordem',
        'q.required'  => 'Obrigatório',
        'q.status'    => 'Status'
    ];

    /**
     * Constructor.
     */
    public function __construct($entityManager, $questionManager, $questionFieldManager)
    {
        $this->entityManager = $entityManager;
        $this->questionManager  = $questionManager;
        $this->questionFieldManager  = $questionFieldManager;
    }

    /**
     * This is the default "index" action of the controller. It displays the
     * list of Question.
     */
    public function indexAction()
    {
        $questionnaires = $this->entityManager->getRepository(Question::class)
                                              ->findBy([], ['id'=>'ASC']);

        return new ViewModel([
            'questionnaires' => $questionnaires,
            'search'         => $this->searchArray,
            'operators'      => $this->searchMethods]);
    }

    /**
     * The "Search" action is used to filter data in the search.
     */
    public function searchAction()
    {
        $qb = $this->entityManager->createQueryBuilder();

        if ($this->getRequest()->isGet()) {

            $search = $this->params()->fromQuery();
            $finder = $this->csecFilter()->performWhereString($search);

        } else {
            $finder = "q.id > 0";
        }

        $questionnaires = $qb->select('q')
                             ->from(Question::class, 'q')
                             ->join('q.idQuestionForm', 'qf', 'left')
                             ->where($finder)
                             ->getQuery();

        $returnArr = [];

        if(count($questionnaires->getResult()) == 0) {
            $returnArr['data'] = [];
        } else {
            foreach ($questionnaires->getResult() as $key => $quiz) {

                $returnArr[$key] = [

                    '0' => $quiz->getQuestionFormAsString(),
                    '1' => $this->csecHtml()->getLink('questionario', $quiz->getId(), $quiz->getName(), 'Visualizar'),
                    '2' => $quiz->getTypeAsString(),
                    '3' => $quiz->getSequence(),
                    '4' => $quiz->getRequiredAsString(),
                    '5' => $quiz->getStatusAsString(),
                    '6' => $this->csecHtml()->getActionButton('questionario', $quiz->getId()),
                ];

            }
        }

        return new JsonModel(['data' => $returnArr]);
    }

    /**
     * This action displays a page allowing to add a new Question.
     */
    public function addAction()
    {
        // Create departament form
        $form = new QuestionDataForm('create', $this->entityManager);

        // Check if departament has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();
            if(!isset($data['status'])){
                $data['status'] = 1;
            }

            $dataField = $data['question_field'];
            unset($data['question_field']);

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                // Add departament.
                $question = $this->questionManager->addQuestion($data, $dataField);

                if(is_string($question)){
                    $this->flashMessenger()->addErrorMessage($question);
                } else {
                    $this->flashMessenger()->addSuccessMessage("Questionário criado com sucesso!");
                    // Redirect to "view" page
                    return $this->redirect()->toRoute('quiz',
                            ['action'=>'view',
                             'id' => $question->getId()]);
                }
            }
        }

        return new ViewModel(['form' => $form,
                              'questionArray' => count($questionsDependency)]);
    }

    /**
     * The "view" action displays a page allowing to view Question's details.
     */
    public function viewAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);

        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $question = $this->entityManager->getRepository(Question::class)
                                        ->find($id);

        $questionFields = $this->entityManager->getRepository(QuestionField::class)
                                        ->findBy(['idQuestion' => $id ],
                                                 ['sequence' => 'ASC']);

        $inputs = array();
        $required = $question->getRequired()==1? 'required' : '';
        $name = $question->getName();

        $pergunta = "<div class='form-group  {$required}'>";
            $pergunta .= "<label class='control-label'>{$name}</label>";
            $pergunta .=  "<div id='input-questionario_{$id}'>";

        if($questionFields){
            foreach ($questionFields as $field) {
                $pergunta .= $this->csecInput()->createInput($question, $field);
            }
        } else {
            $pergunta .= $this->csecInput()->createInput($question);
        }

        $pergunta .= "</div> </div>";

        if ($question == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return new ViewModel(['question' => $question, 'input' => $pergunta]);
    }

    /**
     * The "edit" action displays a page allowing to edit Question.
     */
    public function editAction()
    {

        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $question = $this->entityManager->getRepository(Question::class)
                                        ->find($id);

        if ($question == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $questionFields = $this->entityManager->getRepository(QuestionField::class)
                                        ->findBy(['idQuestion' => $id ],
                                                 ['sequence' => 'ASC']);

        // Create question form
        $form = new QuestionDataForm('update', $this->entityManager, $question);

        if($question->getIdQuestionForm() != null){
            $form->get('id_question_form')
                 ->setAttributes([
                    'value' => $question->getIdQuestionForm()->getId(),
                 ]);
        }

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();
            if(!isset($data['status'])){
                $data['status'] = 0;
            }

            $dataField = $data['question_field'];
            unset($data['question_field']);

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                // Update the questionForm.
                $questionS = $this->questionManager->updateQuestion($question, $data, $dataField);

                if(is_string($questionS)){
                    $this->flashMessenger()->addErrorMessage($questionS);
                } else {
                    $this->flashMessenger()->addSuccessMessage("Questionário alterado com sucesso!");
                    // Redirect to "view" page
                    return $this->redirect()->toRoute('quiz',
                            ['action'=>'view', 'id' => $question->getId()]);
                }
            }

        } else {
            $form->setData(['name'     => $question->getName(),
                            'type'     => $question->getType(),
                            'sequence' => $question->getSequence(),
                            'required' => $question->getRequired(),
                            'status'   => $question->getStatus()]);
        }

        return new ViewModel(['question' => $question,
                              'form' => $form,
                              'fields' => $questionFields]);
    }

    /**
     * The "remove" action exclude a item from database.
     */
    public function removeAction()
    {
        return true;
        $id = (int)$this->params()->fromPost('id');
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $question = $this->entityManager->getRepository(Question::class)
                                        ->find((int)$id);

        if ($question == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $question = $this->questionManager->removeQuestion($question);

        if (is_string($question)) {
            $this->flashMessenger()->addErrorMessage($question);
        } else {
            $this->flashMessenger()->addSuccessMessage("Pergunta removida com sucesso!");
            // Redirect the quiz to "index" page.
            return $this->redirect()->toRoute('quiz', ['action'=>'index']);
        }
    }

    /**
     * The ToggleActive action change status more quickly.
     */
    public function toggleActiveAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();

            $question = $this->entityManager->getRepository(Question::class)->find($data['id']);

            if ($question == null) {
                $this->getResponse()->setStatusCode(404);
                return;
            }

            $question = $this->questionManager->patchQuestion($question, $data);
            if(is_string($question)){
                $this->flashMessenger()->addErrorMessage($question);
            } else {
                $this->flashMessenger()->addSuccessMessage("Questionário alterado com sucesso!");
            }

            return true;
        }
    }

}