<?php
namespace CMS\Service;

use CMS\V1\Entity\MailTemplate;
use Exception;

/**
 * This service is responsible for adding/editing mailTemplates.
 */
class MailTemplateManager
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
     * This method adds a new mail_template.
     */
    public function addMailTemplate($data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            // Do not allow several users with the same name.
            if($this->checkMailTemplateExists($data['name'])) {
                throw new \Exception("Template de E-mail com o nome {$data['name']} já existe.");
            }

            // Create new Job entity.
            $mailTemplate = new MailTemplate();
            $mailTemplate->setName($data['name']);
            $mailTemplate->setHeader($data['header']);
            $mailTemplate->setContent($data['content']);
            $mailTemplate->setFooter($data['footer']);
            $mailTemplate->setImage(substr($data['image']['tmp_name'], 14));
            $mailTemplate->setIdentifier($data['identifier']);
            $mailTemplate->setStatus($data['status']);

            // Add the entity to the entity manager.
            $this->entityManager->persist($mailTemplate);

            // Apply changes to database.
            $this->entityManager->flush();
            $conn->commit();
            return $mailTemplate;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * This method updates data of an existing mail_template.
     */
    public function updateMailTemplate($mailTemplate, $data)
    {

        $conn = $this->entityManager->getConnection();

        try {

            $conn->beginTransaction();
            // Do not allow to change job name if another job with such job already exits.
            if($mailTemplate->getName()!=$data['name'] && $this->checkJobExists($data['name'])) {

                throw new \Exception("Template de E-mail com o nome " . $data['name'] . " já existe.");
            }

            $image = $data['image']['name'] == '' ? $data['image'][0] : substr($data['image']['tmp_name'], 14);
            $mailTemplate->setName($data['name']);
            $mailTemplate->setHeader($data['header']);
            $mailTemplate->setContent($data['content']);
            $mailTemplate->setFooter($data['footer']);
            $mailTemplate->setImage($image);
            $mailTemplate->setIdentifier($data['identifier']);
            $mailTemplate->setStatus($data['status']);

            // Apply changes to database.
            $this->entityManager->flush();
            $conn->commit();
            return $mailTemplate;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * This method updates partial data of an existing mailTemplate.
     */
    public function patchMailTemplate($mailTemplate, $data)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();
            if(isset($data['name'])){
                if($mailTemplate->getName() != $data['name'] && $this->checkOrganizationExists($data['name'])) {
                    throw new \Exception("Outra Unidade com o nome ".$data['name']." já existe.");
                }
                $mailTemplate->setName($data['name']);
            }

            if(isset($data['identifier']))
                $mailTemplate->setIdentifier($data['identifier']);

            if(isset($data['status'])){

                if($data['status'] == 'true')
                    $data['status'] = 1;
                else
                    $data['status'] = 0;

                $mailTemplate->setStatus($data['status']);
            }

            // Apply changes to database.
            $this->entityManager->flush();
            $conn->commit();
            return $mailTemplate;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * This method remove data of an existing mail_template.
     */
    public function removeMailTemplate($data)
    {

        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            if ($data->getImage()) {
                $this->removeImage($data->getImage());
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
     * Checks whether an active mail_template with given name already exists in the database.
     */
    public function checkMailTemplateExists($name)
    {

        $mailTemplate = $this->entityManager->getRepository(MailTemplate::class)->findOneByName($name);
        return $mailTemplate !== null;
    }

    public function removeImage($image, $path)
    {

        try {
            $path = \Base\Module::UPLOAD_DIR;
            if(unlink($path.$image))
                return true;
            else
                throw new Exception("Erro ao excluir imagem, tente novamente.", 1);

        } catch (Exception $e) {
            return $e->getMessage();
        }

    }
}

