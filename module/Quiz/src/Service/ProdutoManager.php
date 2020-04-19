<?php

namespace Quiz\Service;

use Quiz\V1\Entity\Produto;
use Exception;

/**
 * This service is responsible for adding/editing produto.
 */
class ProdutoManager
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
     * This method adds a new produto.
     */
    public function addProduto($data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            if($this->checkProdutoExists($data['nome'])) {
                $message = "Já existe um produto com esse nome ".$data['nome'];
                throw new \Exception($message);
            }

            // Create new Produto entity.
            $produto = new Produto();
            $produto->setNome($data['nome']);
            $produto->setStatus((string)$data['status']);

            // Add the entity to the entity manager.
            $this->entityManager->persist($produto);

            // Apply changes to database.
            $this->entityManager->flush();

            $conn->commit();
            return $produto;

        } catch (Exception $e) {
            $conn->rollBack();

            return $e->getMessage();
        }

    }

    /**
     * This method updates data of an existing produto.
     */
    public function updateProduto($produto, $data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            $produto->setNome($data['nome']);
            $produto->setStatus((string)$data['status']);

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
     * This method remove data of an existing Produto.
     */
    public function removeProduto($data)
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
     * This method updates partial data of an existing Produto.
     */
    public function patchProduto($produto, $data)
    {
        $conn = $this->entityManager->getConnection();

        try {

            $conn->beginTransaction();

            if(isset($data['nome'])){

                if($this->checkProdutoExists($data['nome'])) {
                    $message = "Já existe um produto com esse nome ".$data['nome'];
                    throw new \Exception($message);
                }
                $produto->setNome($data['nome']);
            }

            if (isset($data['status'])) {

                if($data['status'] == 'true')
                    $data['status'] = 1;
                else
                    $data['status'] = 0;

                $produto->setStatus($data['status']);
            }

            // Apply changes to database.
            $this->entityManager->flush();
            $conn->commit();
            return $produto;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }


    }
    /**
     * Checks whether an active Produto with given name already exists in the database.
     */
    public function checkProdutoExists($nome)
    {

        $produto = $this->entityManager->getRepository(Produto::class)
                                            ->findBy(['nome' => $nome],
                                                     ['id'=>'ASC']);

        return count($produto) > 0;
    }

}

