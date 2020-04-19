<?php

namespace Quiz\Service;

use Quiz\V1\Entity\QuestionForm;
use Quiz\V1\Entity\Produto;
use Exception;

/**
 * This service is responsible for adding/editing question-.
 */
class QuestionFormManager
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
     * This method adds a new question_form.
     */
    public function addQuestionForm($data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            if($this->checkQuestionFormExists($data['name'])) {
                $message = "Já existe um formulario com esse nome ".$data['name'];
                throw new \Exception($message);
            }

            $produto = $this->entityManager->getRepository(Produto::class)->find($data['id_produto']);

            // Create new QuestionForm entity.
            $questionForm = new QuestionForm();
            $questionForm->setName($data['name']);
            $questionForm->setIdProduto($produto);
            $questionForm->setLocal($data['local']);
            $questionForm->setSequence((int)$data['sequence']);
            $questionForm->setStatus((string)$data['status']);

            // Add the entity to the entity manager.
            $this->entityManager->persist($questionForm);

            // Apply changes to database.
            $this->entityManager->flush();

            $conn->commit();
            return $questionForm;

        } catch (Exception $e) {
            $conn->rollBack();

            return $e->getMessage();
        }

    }

    /**
     * This method updates data of an existing question_form.
     */
    public function updateQuestionForm($questionForm, $data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();
            $produto = $this->entityManager->getRepository(Produto::class)->find($data['id_produto']);

            $questionForm->setName($data['name']);
            $questionForm->setIdProduto($produto);
            $questionForm->setLocal($data['local']);
            $questionForm->setSequence((int)$data['sequence']);
            $questionForm->setStatus((string)$data['status']);

            // Add the entity to the entity manager.
            $this->entityManager->persist($questionForm);
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
     * This method remove data of an existing Question.
     */
    public function removeQuestionForm($data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            $this->entityManager->remove($data);
            $this->entityManager->flush();

            $conn->commit();

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }
    /**
     * This method updates partial data of an existing QuestionForm.
     */
    public function patchQuestionForm($questionForm, $data)
    {
        $conn = $this->entityManager->getConnection();

        try {

            $conn->beginTransaction();

            if(isset($data['name'])){

                if($this->checkQuestionFormExists($data['name'])) {
                    $message = "Já existe um formulario com esse nome ".$data['name'];
                    throw new \Exception($message);
                }
                $questionForm->setName($data['name']);
            }

            if (isset($data['local']))
                $questionForm->setLocal($data['local']);

            if (isset($data['sequence']))
                $questionForm->setSequence($data['sequence']);

            if (isset($data['id_produto'])){
                $produto = $this->entityManager->getRepository(Produto::class)->find($data['id_produto']);
                $questionForm->setSequence($produto);
            }

            if (isset($data['status'])) {

                if($data['status'] == 'true')
                    $data['status'] = 1;
                else
                    $data['status'] = 0;

                $questionForm->setStatus($data['status']);
            }

            // Apply changes to database.
            $this->entityManager->flush();
            $conn->commit();
            return $questionForm;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }


    }
    /**
     * Checks whether an active QuestionForm with given name already exists in the database.
     */
    public function checkQuestionFormExists($name)
    {

        $questionForm = $this->entityManager->getRepository(QuestionForm::class)
                                            ->findBy(['name' => $name],
                                                     ['id'=>'ASC']);

        return count($questionForm) > 0;
    }

}

