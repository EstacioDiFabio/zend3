<?php
namespace CMS\Controller;

use CMS\Controller\CMSController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use CMS\V1\Entity\Job;
use CMS\Form\JobForm;


/**
 * This controller is responsible for job management (adding, editing, viewing and delete jobs ).
 */
class JobController extends CMSController
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Job manager.
     * @var Job\Service\JobManager
     */
    private $jobManager;

    /**
     * Array used to creating dinamic filters
     */
    private $searchArray = [
        'name' => 'Nome',
        'idTopJob' => 'Cargo Superior',
        'status' => 'Status'
    ];

    /**
     * Constructor.
     */
    public function __construct($entityManager, $jobManager)
    {
        $this->entityManager = $entityManager;
        $this->jobManager = $jobManager;
    }

    /**
     * This is the default "index" action of the controller. It displays the
     * list of jobs.
     */
    public function indexAction()
    {
        $jobs = $this->entityManager->getRepository(Job::class)->findBy([], ['id'=>'ASC']);

        return new ViewModel(
            ['jobs' => $jobs,
            'search' => $this->searchArray,
            'operators' => $this->searchMethods]);
    }

    /**
     * The "Search" action is used to filter data in the search.
     */
    public function searchAction()
    {

        $qb = $this->entityManager->createQueryBuilder();
        $alias = "j";

        if ($this->getRequest()->isGet()) {

            $search = $this->params()->fromQuery();
            $finder = $this->csecFilter()->performWhereString($search, $alias);

        } else {
            $finder = $alias.".id > 0";
        }

        $jobs = $qb->select($alias)
                   ->from(Job::class, $alias)
                   ->where($finder)
                   ->getQuery();

        $returnArr = array();

        if(count($jobs->getResult()) == 0) {
            $returnArr['data'] = [];
        } else {
            foreach ($jobs->getResult() as $key => $job) {

                $returnArr[$key] = [

                    '0' => $this->csecHtml()->getLink('cargos', $job->getId(), $job->getName(), 'Visualizar'),
                    '1' => $job->getIdTopJob(),
                    '2' => $job->getStatusToggle(),
                    '3' => $this->csecHtml()->getActionButton('cargos', $job->getId()),
                ];

            }
        }

        return new JsonModel(['data' => $returnArr]);
    }

    /**
     * This action displays a page allowing to add a new job.
     */
    public function addAction()
    {
        // Create job form
        $form = new JobForm('create', $this->entityManager);

        // Check if job has submitted the form
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

                // Add job.
                $job = $this->jobManager->addJob($data);

                if(is_string($job)){

                    $this->flashMessenger()->addErrorMessage($job);

                } else {
                    $this->flashMessenger()->addSuccessMessage("Cargo criado com sucesso!");
                    // Redirect to "view" page
                    return $this->redirect()->toRoute('jobs',
                            ['action'=>'view', 'id'=>$job->getId()]);
                }

            }
        }

        return new ViewModel([
                'form' => $form,
            ]);
    }

    /**
     * The "view" action displays a page allowing to view job's details.
     */
    public function viewAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }


        // Find a user with such ID.
        $job = $this->entityManager->getRepository(Job::class)->find($id);

        if ($job == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        return new ViewModel([
            'job' => $job
        ]);
    }

    /**
     * The "edit" action displays a page allowing to edit job.
     */
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $job = $this->entityManager->getRepository(Job::class)->find($id);

        if ($job == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Create user form
        $form = new JobForm('update', $this->entityManager, $job);

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

                // Update the user.
                $job = $this->jobManager->updateJob($job, $data);

                if(is_string($job)){
                    $this->flashMessenger()->addErrorMessage($job);
                } else {
                    $this->flashMessenger()->addSuccessMessage("Cargo alterado com sucesso!");
                    // Redirect to "view" page
                    return $this->redirect()->toRoute('jobs',
                            ['action'=>'view', 'id'=>$job->getId()]);
                }

            }
        } else {
            $form->setData(array(
                    'name'=>$job->getName(),
                    'id_top_job'=>$job->getIdTopJob(),
                    'status'=>$job->getStatus(),
                ));
        }

        return new ViewModel(array(
            'job' => $job,
            'form' => $form
        ));
    }

    /**
     * The "remove" action exclude a item from database.
     */
    public function removeAction()
    {
        // $id = $this->params()->fromRoute('id');
        $id = $this->params()->fromPost('id');

        $job = $this->entityManager->getRepository(Job::class)
                    ->findOneById($id);

        if ($job == null) {
          $this->getResponse()->setStatusCode(404);
          return;
        }

        $job = $this->jobManager->removeJob($job);

        if(is_string($job)){
            $this->flashMessenger()->addErrorMessage($job);
        } else {
            $this->flashMessenger()->addSuccessMessage("Cargo removido com sucesso!");
            // Redirect the job to "index" page.
            return $this->redirect()->toRoute('jobs', ['action'=>'index']);
        }

    }

    /**
     * The ToggleActive action change status more quickly.
     */
    public function toggleActiveAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();

            $job = $this->entityManager->getRepository(Job::class)->find($data['id']);

            if ($job == null) {
                $this->getResponse()->setStatusCode(404);
                return;
            }

            $job = $this->jobManager->patchJob($job, $data);

            if(is_string($job)){
                $this->flashMessenger()->addErrorMessage($job);
            } else {
                $this->flashMessenger()->addSuccessMessage("Cargo alterado com sucesso!");
            }

            return true;
        }
    }
}