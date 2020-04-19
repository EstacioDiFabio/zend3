<?php

namespace Quiz\Service;

use Quiz\V1\Entity\Question;
use Quiz\V1\Entity\QuestionForm;
use Quiz\V1\Entity\QuestionField;
use Exception;

/**
 * This service is responsible for adding/editing question-.
 */
class QuestionManager
{
    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Constructs the service.
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * This method adds a new question.
     */
    public function addQuestion($data, $dataField)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();
            unset($dataField['label'][0]);
            unset($dataField['sequence'][0]);

            // Create new Question entity.
            $question = new Question();
            $question->setName($data['name']);
            $question->setType($data['type']);
            $question->setSequence((int)$data['sequence']);
            $question->setRequired((int)$data['required']);
            $question->setStatus((int)$data['status']);

            if(isset($data['id_question_form'])){
                $questionForm = $this->entityManager->getRepository(QuestionForm::class)
                                                    ->find($data['id_question_form']);

                $question->setIdQuestionForm($questionForm);
            }
            // Add the entity to the entity manager.
            $this->entityManager->persist($question);
            // Apply changes to database.
            $this->entityManager->flush();


            if(is_array($dataField)){
                for ($i=1; $i <= count($dataField['label']); $i++) {
                    if (!empty($dataField['label'][$i]) && !empty($dataField['sequence'][$i])) {
                        $questionField = new QuestionField();
                        $questionField->setIdQuestion($question);
                        $questionField->setLabel($dataField['label'][$i]);
                        $questionField->setSequence($dataField['sequence'][$i]);

                        // Add the entity to the entity manager.
                        $this->entityManager->persist($questionField);
                        // Apply changes to database.
                        $this->entityManager->flush();
                    }
                }
            }

            $conn->commit();
            return $question;

        } catch (Exception $e) {
            $conn->rollBack();

            return $e->getMessage();
        }

    }

    /**
     * This method updates data of an existing question.
     */
    public function updateQuestion($question, $data, $dataField)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            unset($dataField['label'][0]);
            unset($dataField['sequence'][0]);

            // Create new Question entity.
            $question->setName($data['name']);
            $question->setType($data['type']);
            $question->setSequence((int)$data['sequence']);
            $question->setRequired((int)$data['required']);
            $question->setStatus((int)$data['status']);

            if(isset($data['id_question_form'])){
                $questionForm = $this->entityManager->getRepository(QuestionForm::class)
                                                    ->find($data['id_question_form']);

                $question->setIdQuestionForm($questionForm);
            }
            // Add the entity to the entity manager.
            $this->entityManager->persist($question);
            // Apply changes to database.
            $this->entityManager->flush();

            if(is_array($dataField)){

                $questionField = $this->entityManager->getRepository(QuestionField::class)
                                                     ->findBy(['idQuestion' => $question->getId()]);
                if(!empty($questionField)){
                    foreach($questionField as $q){
                        $this->entityManager->remove($q);
                    }

                    for ($i=1; $i <= count($dataField['label']); $i++) {

                        $questionField = new QuestionField();
                        $questionField->setIdQuestion($question);
                        $questionField->setLabel($dataField['label'][$i]);
                        $questionField->setSequence($dataField['sequence'][$i]);

                        // Add the entity to the entity manager.
                        $this->entityManager->persist($questionField);
                        // Apply changes to database.
                        $this->entityManager->flush();
                    }
                }

            }

            $conn->commit();
            return true;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * This method remove data of an existing Question.
     */
    public function removeQuestion($data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();
            $questionField = $this->entityManager->getRepository(QuestionField::class)
                                                 ->findBy(['idQuestion' => $data->getId()]);

            foreach($questionField as $q){
                $this->entityManager->remove($q);
            }

            $this->entityManager->remove($data);
            $this->entityManager->flush();

            $conn->commit();

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * Checks whether an active Question with given name already exists in the database.
     */
    public function checkQuestionExists($client_id, $date)
    {

        $question = $this->entityManager->getRepository(Question::class)
                                                   ->findBy(['name' => $client_id, 'date' => $date],
                                                            ['id'=>'ASC']);

        return count($question) > 0;
    }

}

