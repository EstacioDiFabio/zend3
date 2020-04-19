<?php
namespace CMS\Controller;

use CMS\Controller\CMSController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use CMS\V1\Entity\MailTemplate;
use CMS\Form\MailTemplateForm;

/**
 * This controller is responsible for mail_template management (adding, editing, viewing and delete jobs ).
 */
class MailTemplateController extends CMSController
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * MailTemplate manager.
     * @var MailTemplate\Service\MailTemplateManager
     */
    private $mailTemplateManager;

    // The image manager.
    private $imageManager;

    /**
     * Array used to creating dinamic filters
     */
    private $searchArray = [
        'name' => 'Nome',
        'identifier' => 'Identificador',
        'status' => 'Status'
    ];

    /**
     * Constructor.
     */
    public function __construct($entityManager, $mailTemplateManager, $imageManager)
    {
        $this->entityManager = $entityManager;
        $this->mailTemplateManager = $mailTemplateManager;
        $this->imageManager = $imageManager;
    }

    /**
     * This is the default "index" action of the controller. It displays the
     * list of mailTemplates.
     */
    public function indexAction()
    {
        $mailTemplates = $this->entityManager->getRepository(MailTemplate::class)->findBy([],
                                                             ['id'=>'ASC']);

        return new ViewModel([
            'mailTemplates' => $mailTemplates,
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
        $alias = "mt";

        if ($this->getRequest()->isGet()) {

            $search = $this->params()->fromQuery();
            $finder = $this->windelFilter()->performWhereString($search, $alias);

        } else {
            $finder = $alias.".id > 0";
        }

        $mailTemplates = $qb->select($alias)
                   ->from(MailTemplate::class, $alias)
                   ->where($finder)
                   ->getQuery();

        $returnArr = array();

        if(count($mailTemplates->getResult()) == 0) {
            $returnArr['data'] = [];
        } else {

            foreach ($mailTemplates->getResult() as $key => $mailTemplate) {

                $returnArr[$key] = [

                    '0' => $this->windelHtml()->getLink('mail_template', $mailTemplate->getId(), $mailTemplate->getName(), 'Visualizar'),
                    '1' => $mailTemplate->getIdentifier(),
                    '2' => $mailTemplate->getStatusToggle(),
                    '3' => $this->windelHtml()->getActionButton('mail_template', $mailTemplate->getId()),
                ];

            }
        }

        return new JsonModel(['data' => $returnArr]);
    }

    /**
     * This action displays a page allowing to add a new mailTemplate.
     */
    public function addAction()
    {
        // Create mailTemplate form
        $form = new MailTemplateForm('create', $this->entityManager);

        // Check if mailTemplate has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            // $data = $this->params()->fromPost();

            // Fill in the form with POST And GET data
            $request = $this->getRequest();
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            if(!isset($data['status'])){
                $data['status'] = 0;
            }
            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                // Add job.
                $mailTemplate = $this->mailTemplateManager->addMailTemplate($data);

                if(is_string($mailTemplate)){
                    $this->flashMessenger()->addErrorMessage($mailTemplate);
                } else {

                    $this->flashMessenger()->addSuccessMessage("Template criado com sucesso!");
                    // Redirect to "view" page
                    return $this->redirect()->toRoute('mailTemplates',
                            ['action'=>'view', 'id'=>$mailTemplate->getId()]);
                }

            }
        }

        return new ViewModel([
                'form' => $form,
            ]);
    }

    /**
     * The "view" action displays a page allowing to view mailTemplate's details.
     */
    public function viewAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Find a user with such ID.
        $mailTemplate = $this->entityManager->getRepository(MailTemplate::class)->find($id);
        $file = null;

        if(!empty($mailTemplate->getImage()))
            $file = $this->imageManager->getSavedFiles($mailTemplate->getImage());

        if ($mailTemplate == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return new ViewModel([
            'mailTemplate' => $mailTemplate,
            'file' => $file,
        ]);
    }

    /**
     * The "edit" action displays a page allowing to edit mailTemplate.
     */
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $mailTemplate = $this->entityManager->getRepository(MailTemplate::class)->find($id);

        if ($mailTemplate == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Create user form
        $form = new MailTemplateForm('update', $this->entityManager, $mailTemplate);

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $request = $this->getRequest();
            $data = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            if(!isset($data['status'])){
                $data['status'] = 0;
            }
            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();
                // Update template user.
                $mailTemplate = $this->mailTemplateManager->updateMailTemplate($mailTemplate, $data);

                if(is_string($mailTemplate)){
                    $this->flashMessenger()->addErrorMessage($mailTemplate);
                } else {

                    $this->flashMessenger()->addSuccessMessage("Template alterado com sucesso!");
                    // Redirect to "view" page
                    return $this->redirect()->toRoute('mailTemplates',
                            ['action'=>'view', 'id'=>$mailTemplate->getId()]);
                }

            }
        } else {

            $form->setData(array(
                    'name'=>$mailTemplate->getName(),
                    'header'=> html_entity_decode($mailTemplate->getHeader(), ENT_COMPAT, 'UTF-8'),
                    'content'=> html_entity_decode($mailTemplate->getContent(), ENT_COMPAT, 'UTF-8'),
                    'footer'=> html_entity_decode($mailTemplate->getFooter(), ENT_COMPAT, 'UTF-8'),
                    'image'=>$mailTemplate->getImage(),
                    'identifier'=>$mailTemplate->getIdentifier(),
                    'status'=>$mailTemplate->getStatus(),
                ));
        }

        return new ViewModel(array(
            'mailTemplate' => $mailTemplate,
            'form' => $form
        ));
    }

    /**
     * The ToggleActive action change status more quickly.
     */
    public function toggleActiveAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();

            $mailTemplate = $this->entityManager->getRepository(MailTemplate::class)->find($data['id']);

            if ($mailTemplate == null) {
                $this->getResponse()->setStatusCode(404);
                return;
            }

            $mailTemplate = $this->mailTemplateManager->patchMailTemplate($mailTemplate, $data);

            if(is_string($mailTemplate)){
                $this->flashMessenger()->addErrorMessage($mailTemplate);
            } else {

                $this->flashMessenger()->addSuccessMessage("Template alterado com sucesso!");
            }

            return true;
        }
    }

     /**
     * This action remove a mailTemplate data.
     */

    /**
     * The "remove" action exclude a item from database.
     */
    public function removeAction()
    {
        // $id = $this->params()->fromRoute('id');
        $id = $this->params()->fromPost('id');

        $mailTemplate = $this->entityManager->getRepository(MailTemplate::class)
                    ->findOneById($id);

        if ($mailTemplate == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $mailTemplate = $this->mailTemplateManager->removeMailTemplate($mailTemplate);

        if(is_string($mailTemplate)){
            $this->flashMessenger()->addErrorMessage($mailTemplate);
        } else {

            $this->flashMessenger()->addSuccessMessage("Template removido com sucesso!");
            // Redirect the mail_template to "index" page.
            return $this->redirect()->toRoute('mailTemplates', ['action'=>'index']);
        }

    }

    /**
     * Retrive image from database and path
     */
    public function fileAction()
    {

        $fileName = $this->params()->fromQuery('name', '');
        $isThumbnail = (bool)$this->params()->fromQuery('thumbnail', false);
        $fileName = $this->imageManager->getFileFromPath($fileName, $isThumbnail);
        $fileInfo = $this->imageManager->getImageFileInfo($fileName);

        if ($fileInfo===false) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $headers->addHeaderLine("Content-type: " . $fileInfo['type']);
        $headers->addHeaderLine("Content-length: " . $fileInfo['size']);

        $fileContent = $this->imageManager->getImageFileContent($fileName);

        if($fileContent!==false) {
            $response->setContent($fileContent);

        } else {
            $this->getResponse()->setStatusCode(500);
            return;
        }

        if($isThumbnail)
            unlink($fileName);


        return $this->getResponse();
    }
}