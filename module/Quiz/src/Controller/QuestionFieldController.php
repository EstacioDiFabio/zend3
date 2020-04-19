<?php

namespace Quiz\Controller;

use CMS\Controller\CMSController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Quiz\V1\Entity\QuestionField;
use Quiz\V1\Entity\QuestionFieldDefaultValue;
use Quiz\V1\Entity\Question;
use Quiz\Form\QuestionFieldForm;

/**
 * This controller is responsible for QuestionField management (adding, editing, viewing and delete question ).
 */
class QuestionFieldController extends CMSController
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * QuestionField manager.
     * @var CMS\Service\QuestionFieldManager
     */
    private $questionFieldManager;

    /**
     * Array used to creating dinamic filters
     */
    private $searchArray = [
        'id_question'  => 'Pergunta',
        'label'        => 'Label',
        'sequence'     => 'Sequência'
    ];

    /**
     * Constructor.
     */
    public function __construct($entityManager, $questionFieldManager)
    {
        $this->entityManager = $entityManager;
        $this->questionFieldManager  = $questionFieldManager;
    }

    /**
     * This is the default "index" action of the controller. It displays the
     * list of QuestionFields.
     */
    public function indexAction()
    {
        $fields = $this->entityManager->getRepository(QuestionField::class)
                                      ->findBy([], ['id'=>'ASC']);

        return new ViewModel([
            'fields' => $fields,
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
        $alias = "q";

        if ($this->getRequest()->isGet()) {

            $search = $this->params()->fromQuery();
            $finder = $this->windelFilter()->performWhereString($search, $alias);

        } else {
            $finder = $alias.".id > 0";
        }

        $fields = $qb->select($alias)
                             ->from(QuestionField::class, $alias)
                             ->where($finder)
                             ->getQuery();

        $returnArr = [];

        if(count($fields->getResult()) == 0) {
            $returnArr['data'] = [];
        } else {
            foreach ($fields->getResult() as $key => $field) {

                $returnArr[$key] = [

                    '0' => $this->windelHtml()->getLink('campos',
                                                        $field->getId(),
                                                        $field->getQuestionAsString(),
                                                        'Visualizar'),
                    '1' => $field->getLabel(),
                    '2' => $field->getTypeAsString(),
                    '3' => $field->getSequence(),
                    '4' => $this->windelHtml()->getActionButton('campos', $field->getId()),
                ];

            }
        }

        return new JsonModel(['data' => $returnArr]);
    }

    /**
     * This action displays a page allowing to add a new QuestionField.
     */
    public function addAction()
    {
        // Create departament form
        $form = new QuestionFieldForm('create', $this->entityManager);

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
                $data['identifier'] = $this->windelFilter()->slugfy($data['identifier']);
                // Add departament.
                $questionFields = $this->questionFieldManager->addQuestionField($data);

                if(is_string($questionFields)){
                    $this->flashMessenger()->addErrorMessage($questionFields);
                } else {
                    $this->flashMessenger()->addSuccessMessage("Campo criado com sucesso!");
                    // Redirect to "view" page
                    return $this->redirect()->toRoute('quizField',
                            ['action'=>'view', 'id' => $questionFields->getId()]);

                }
            }
        }

        return new ViewModel([
                'form' => $form,
                'fields' => count($fields)
            ]);
    }

    /**
     * The "view" action displays a page allowing to view QuestionField's details.
     */
    public function viewAction()
    {

        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Find a quiz with such ID.
        $questionField = $this->entityManager->getRepository(QuestionField::class)->find($id);

        if ($questionField == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $input = $this->windelInput()->createInput($questionField);

        return new ViewModel([
            'questionField' => $questionField,
            'input' => $input
        ]);
    }

    /**
     * The "edit" action displays a page allowing to edit QuestionField.
     */
    public function editAction()
    {

        $id = (int)$this->params()->fromRoute('id', -1);

        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $questionField = $this->entityManager->getRepository(QuestionField::class)
                                             ->find($id);

        if ($questionField == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Create questionField form
        $form = new QuestionFieldForm('update', $this->entityManager, $questionField);

        $form->get('depends_field')
             ->setAttributes([
                'value' => $questionField->getDependsField(),
             ]);

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

                $data['identifier'] = $this->windelFilter()->slugfy($data['identifier']);

                // Update the questionField.
                $questionFields = $this->questionFieldManager->updateQuestionField($questionField,
                                                                                   $data);

                if(is_string($questionFields)){
                    $this->flashMessenger()->addErrorMessage($questionFields);
                } else {
                    $this->flashMessenger()->addSuccessMessage("Questionário alterado com sucesso!");
                    // Redirect to "view" page
                    return $this->redirect()->toRoute('quizField',
                            ['action'=>'view', 'id' => $questionField->getId()]);

                }

            }
        } else {

            $form->setData(array(
                'id_question'=>$questionField->getIdQuestion()->getId(),
                'label'=>$questionField->getLabel(),
                'identifier'=>$questionField->getIdentifier(),
                'type'=>$questionField->getType(),
                'sequence'=>$questionField->getSequence(),
                'depends_field'=>$questionField->getDependsField(),
            ));
        }

        return new ViewModel(array(
            'questionField' => $questionField,
            'form' => $form,
            'fields' => count($fields)
        ));
    }

    /**
     * The "remove" action exclude a item from database.
     */
    public function removeAction()
    {
        // $id = $this->params()->fromRoute('id');
        $id = $this->params()->fromPost('id');

        $questionField = $this->entityManager->getRepository(QuestionField::class)
                                            ->findOneById($id);

        if ($questionField == null) {
          $this->getResponse()->setStatusCode(404);
          return;
        }

        $questionFields = $this->questionFieldManager->removeQuestionField($questionField);

        if(is_string($questionFields)){
            $this->flashMessenger()->addErrorMessage($questionFields);
        } else {
            $this->flashMessenger()->addSuccessMessage("Campo removido com sucesso!");
            // Redirect the questionField to "index" page.
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

            $questionField = $this->entityManager->getRepository(QuestionField::class)->find($data['id']);

            if ($questionField == null) {
                $this->getResponse()->setStatusCode(404);
                return;
            }

            $questionField = $this->questionFieldManager->patchQuestionField($questionField, $data);
            if(is_string($questionField)){
                $this->flashMessenger()->addErrorMessage($questionField);
            } else {
                $this->flashMessenger()->addSuccessMessage("Questionário alterado com sucesso!");
            }

            return true;
        }
    }

}