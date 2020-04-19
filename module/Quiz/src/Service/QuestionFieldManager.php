<?php

namespace Quiz\Service;

use Quiz\V1\Entity\QuestionField;
use Quiz\V1\Entity\Question;
use Quiz\V1\Entity\QuestionFieldFilledValue;

use Exception;

/**
 * This service is responsible for adding/editing question.
 */
class QuestionFieldManager
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
    public function addQuestionField($data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            // if($this->checkQuestionFormFieldExists($data['id_client'], $date)) {
            //     $message = "Já existe um questionário para " . $data['id_client'] . " nesse dia ". $date->format('d/m/Y');
            //     throw new \Exception($message);
            // }

            $question = $this->entityManager->getRepository(Question::class)
                                            ->find($data['id_question']);

            // Create new QuestionField entity.
            $questionField = new QuestionField();
            $questionField->setIdQuestion($question);
            $questionField->setLabel($data['label']);
            $questionField->setIdentifier($data['identifier']);
            $questionField->setValueField($data['value_field']);
            $questionField->setType($data['type']);
            $questionField->setSequence($data['sequence']);

            if($data['depends_field']) {

                $depends = $this->entityManager->getRepository(QuestionField::class)
                                                    ->find($data['depends_field']);

                $questionField->setDependsField($depends->getId());
            }

            // Add the entity to the entity manager.
            $this->entityManager->persist($questionField);
            // Apply changes to database.
            $this->entityManager->flush();

            $conn->commit();
            return $questionField;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * This method updates data of an existing question_field.
     */
    public function updateQuestionField($questionField, $data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            $question = $this->entityManager->getRepository(Question::class)
                                            ->find($data['id_question']);

            $questionField->setIdQuestion($question);
            $questionField->setLabel($data['label']);
            $questionField->setIdentifier($data['identifier']);
            $questionField->setValueField($data['value_field']);
            $questionField->setType($data['type']);
            $questionField->setSequence($data['sequence']);

            if($data['depends_field']) {

                $depends = $this->entityManager->getRepository(QuestionField::class)
                                                    ->find($data['depends_field']);

                $questionField->setDependsField($depends->getId());
            }

            // Apply changes to database.
            $this->entityManager->flush();

            $conn->commit();
            return true;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * This method remove data of an existing QuestionField.
     */
    public function removeQuestionField($data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();
            $resource = $this->entityManager->getRepository(QuestionFieldFilledValue::class)
                                            ->findBy(['idQuestionField' => $data->getId()]);

            if($resource !== null){
                if(is_array($resource)){
                    foreach ($resource as $r) {
                        $this->entityManager->remove($r);
                    }
                } else {
                    $this->entityManager->remove($resource);
                }
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
     * Checks whether an active QuestionField with given name already exists in the database.
     */
    public function checkQuestionFieldExists($client_id, $date)
    {

        $questionField = $this->entityManager->getRepository(QuestionField::class)
                                             ->findBy(['idClient' => $client_id, 'date' => $date],
                                                      ['id'=>'ASC']);

        return count($questionField) > 0;
    }

}

