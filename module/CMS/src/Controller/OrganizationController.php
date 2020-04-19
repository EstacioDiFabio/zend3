<?php
namespace CMS\Controller;

use CMS\Controller\CMSController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use CMS\V1\Entity\Organization;
use CMS\V1\Entity\OrganizationOfficeHour;
use CMS\Form\OrganizationForm;

/**
 * This controller is responsible for organization management (adding, editing, viewing and delete organizations ).
 */
class OrganizationController extends CMSController
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Organization manager.
     * @var CMS\Service\OrganizationManager
     */
    private $organizationManager;

    /**
     * OrganizationOfficeHour manager.
     * @var CMS\Service\OrganizationOfficeHourManager
     */
    private $organizationOfficeHourManager;

    /**
     * Array used to creating dinamic filters
     */
    private $searchArray = [
        'name' => 'Nome',
        'status' => 'Status'
    ];

    private $day = [
        0 => 'sunday',
        1 => 'monday',
        2 => 'tuesday',
        3 => 'wednesday',
        4 => 'thursday',
        5 => 'friday',
        6 => 'saturday'
    ];

    private $dia = [
        0 => 'domingo',
        1 => 'segunda',
        2 => 'terca',
        3 => 'quarta',
        4 => 'quinta',
        5 => 'sexta',
        6 => 'sabado'
    ];
    /**
     * Constructor.
     */
    public function __construct($entityManager, $organizationManager, $organizationOfficeHourManager)
    {
        $this->entityManager = $entityManager;
        $this->organizationManager = $organizationManager;

    }

    /**
     * This is the default "index" action of the controller. It displays the
     * list of organizations.
     */
    public function indexAction()
    {
        $organizations = $this->entityManager->getRepository(Organization::class)->findBy([], ['id'=>'ASC']);

        return new ViewModel([
            'organizations' => $organizations,
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
        $alias = "o";

        if ($this->getRequest()->isGet()) {

            $search = $this->params()->fromQuery();
            $finder = $this->windelFilter()->performWhereString($search, $alias);

        } else {
            $finder = $alias.".id > 0";
        }

        $organizations = $qb->select($alias)
                            ->from(Organization::class, $alias)
                            ->where($finder)
                            ->getQuery();

        $returnArr = array();

        if(count($organizations->getResult()) == 0) {
            $returnArr['data'] = [];
        } else {

            foreach ($organizations->getResult() as $key => $organization) {

                $returnArr[$key] = [

                    '0' => $this->windelHtml()->getLink('unidades', $organization->getId(), $organization->getName(), 'Visualizar'),
                    '1' => $organization->getStatusToggle(),
                    '2' => $this->windelHtml()->getActionButton('unidades', $organization->getId()),
                ];

            }
        }

        return new JsonModel(['data' => $returnArr]);
    }

    /**
     * This action displays a page allowing to add a new organization.
     */
    public function addAction()
    {
        // Create organization form
        $form = new OrganizationForm('create', $this->entityManager);

        // Check if organization has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            if(!isset($data['status'])){
                $data['status'] = 0;
            }

            $form->setData($data);
            $organization = $this->organizationManager->addOrganization($data);

            if(is_string($organization)){
                $this->flashMessenger()->addErrorMessage($organization);
            } else {

                $this->flashMessenger()->addSuccessMessage("Unidade criada com sucesso!");
                // Redirect to "view" page
                return $this->redirect()->toRoute('organizations',
                        ['action'=>'view', 'id'=>$organization->getId()]);
            }
        }

        return new ViewModel([
                'form' => $form,
            ]);
    }

    /**
     * The "view" action displays a page allowing to view organization's details.
     */
    public function viewAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Find a user with such ID.
        $organization = $this->entityManager->getRepository(Organization::class)
                             ->find($id);

        $qb = $this->entityManager->createQueryBuilder();
        $alias = 'ofh';
        $finder = $alias.'.idOrganization = '.$id;
        $organizationh = $qb->select($alias)
                            ->from(OrganizationOfficeHour::class, $alias)
                            ->where($finder)
                            ->getQuery();

        if ($organization == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }
        if(count($organizationh->getResult()) != 0) {
            $hour = $organizationh->getResult();
        } else {
            $hour = "";
        }

        return new ViewModel([
            'organization' => $organization,
            'hour' => $hour

        ]);
    }

    /**
     * The "edit" action displays a page allowing to edit organization.
     */
    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $qb = $this->entityManager->createQueryBuilder();
        $organization = $this->entityManager->getRepository(Organization::class)
                                            ->find($id);

        if ($organization == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Create organization form
        $form = new OrganizationForm('update', $this->entityManager, $organization);

        $alias = 'ofh';
        $finder = $alias.'.idOrganization = '.$id;

        $organizationh = $qb->select($alias)
                            ->from(OrganizationOfficeHour::class, $alias)
                            ->where($finder)
                            ->getQuery();

        $dataForm =  ['name'=>$organization->getName(),
                      'status'=>$organization->getStatus()];

        if(count($organizationh->getResult()) != 0) {

            foreach ($organizationh->getResult() as $key => $org) {

                $hour[$this->translateWeekName($org->getDay())]['status_hour'] =
                     $org->getStatusHour();

                $hour[$this->translateWeekName($org->getDay())]['morning_start_time'] =
                     $org->getMorningStartTime()->format('H:i');

                $hour[$this->translateWeekName($org->getDay())]['morning_closing_time'] =
                     $org->getMorningClosingTime()->format('H:i');

                $hour[$this->translateWeekName($org->getDay())]['afternoon_start_time'] =
                     $org->getAfternoonStartTime()->format('H:i');

                $hour[$this->translateWeekName($org->getDay())]['afternoon_closing_time'] =
                     $org->getAfternoonClosingTime()->format('H:i');

            }

        }

        // Check if organization has submitted the form
        if ($this->getRequest()->isPost()) {

            // Fill in the form with POST data
            $data = $this->params()->fromPost();
            if(!isset($data['status'])){
                $data['status'] = 0;
            }
            $form->setData($data);

            $organization = $this->organizationManager->updateOrganization($organization, $data);

            if(is_string($organization)){
                $this->flashMessenger()->addErrorMessage($organization);
            } else {

                $this->flashMessenger()->addSuccessMessage("Unidade atualizada com sucesso!");
                // Redirect to "view" page
                return $this->redirect()->toRoute('organizations',
                        ['action'=>'view', 'id'=>$organization->getId()]);
            }

        } else {

            $form->setData($dataForm);
        }

        return new ViewModel([
            'organization' => $organization,
            'form' => $form,
            'hour' => $hour
        ]);
    }

    /**
     * The "remove" action exclude a item from database.
     */
    public function removeAction()
    {
        $id = $this->params()->fromPost('id');

        $organization = $this->entityManager->getRepository(Organization::class)
                    ->findOneById($id);

        if ($organization == null) {
          $this->getResponse()->setStatusCode(404);
          return;
        }

        $organization = $this->organizationManager->removeOrganization($organization);

        if(is_string($organization)){
            $this->flashMessenger()->addErrorMessage($organization);
        } else {

            $this->flashMessenger()->addSuccessMessage("Unidade removido com sucesso!");
            // Redirect the organization to "index" page.
            return $this->redirect()->toRoute('organizations', ['action'=>'index']);
        }
    }

    /**
     * The ToggleActive action change status more quickly.
     */
    public function toggleActiveAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();

            $organization = $this->entityManager->getRepository(Organization::class)->find($data['id']);

            if ($organization == null) {
                $this->getResponse()->setStatusCode(404);
                return;
            }

            $organization = $this->organizationManager->patchOrganization($organization, $data);

            if(is_string($organization)){
                $this->flashMessenger()->addErrorMessage($organization);
            } else {
                $this->flashMessenger()->addSuccessMessage("Unidade alterada com sucesso!");
            }

            return true;
        }
    }

    public function translateWeekName($name)
    {
        switch ($name) {

            case 'domingo':
                return $this->day[0]; break;
            case 'segunda':
                return $this->day[1]; break;
            case 'terca':
                return $this->day[2]; break;
            case 'quarta':
                return $this->day[3]; break;
            case 'quinta':
                return $this->day[4]; break;
            case 'sexta':
                return $this->day[5]; break;
            case 'sabado':
                return $this->day[6]; break;

            default:
                return 'indefinido'; break;
        }
    }
}