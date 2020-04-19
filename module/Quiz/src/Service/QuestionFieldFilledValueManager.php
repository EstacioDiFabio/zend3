<?php

namespace Quiz\Service;

use Implantation\V1\Entity\DeploymentSchedule;
use Quiz\V1\Entity\Question;
use Quiz\V1\Entity\QuestionField;
use Quiz\V1\Entity\QuestionFieldFilledValue;
use Quiz\V1\Entity\QuestionForm;
use Exception;


use Zend\Db\Sql\Sql;
use Zend\Db\Adapter\Adapter;

/**
 * This service is responsible for adding/editing question-.
 */
class QuestionFieldFilledValueManager
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
     * This method adds a new question awnser.
     */
    public function addQuestionFieldFilledValue($data)
    {

        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            if (is_array($data['value'])) {

                foreach ($data['value'] as $key => $value) {

                    $qffv = new QuestionFieldFilledValue();
                    $qffv->setIdField($data['id_field'][$key]);
                    $qffv->setIdQuestion($data['id_question']);
                    $qffv->setidDeploymentSchedule($data['scheduling']);
                    $qffv->setValue($value);
                    $qffv->setValueText($data['value_text']);

                    // Add the entity to the entity manager.
                    $this->entityManager->persist($qffv);
                    // Apply changes to database.
                    $this->entityManager->flush();
                }

            } else {

                $qffv = new QuestionFieldFilledValue();
                $qffv->setIdField($data['id_field']);
                $qffv->setIdQuestion($data['id_question']);
                $qffv->setidDeploymentSchedule($data['scheduling']);
                $qffv->setValue($data['value']);
                $qffv->setValueText($data['value_text']);

                // Add the entity to the entity manager.
                $this->entityManager->persist($qffv);
                // Apply changes to database.
                $this->entityManager->flush();
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
    public function removeQuestionFieldFilledValue($data)
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

    public function getAwnseredForm($id)
    {
        $dbAdapter = new Adapter([
                    'driver'   => 'Pdo_Mysql',
                    'database' => \Base\Module::DATABASE,
                    'username' => \Base\Module::USERNAME,
                    'password' => \Base\Module::PASSWORD,
                    'driver_options' => array(
                        \PDO::ATTR_EMULATE_PREPARES => true
                    )
                ]);

        $sql = new Sql($dbAdapter);
        $select = $sql->select()
                      ->from('formulario_respostas')
                      ->where(['schedule' => $id]);

        $statement = $sql->prepareStatementForSqlObject($select);
        return $statement->execute();
    }

}

