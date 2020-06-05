<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Implantation\Controller;

use Implantation\Controller\ImplantationController;
use Implantation\Form\DeploymentScheduleForm;
use Implantation\V1\Entity\ClientScheduling;
use CMS\V1\Entity\User;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Http\Request;
use Zend\Http\Client;
use Zend\Stdlib\Parameters;
use Zend\Json\Json;
use Datetime;
use Base\Event\EventBase;

class ClientSchedulingController extends ImplantationController
{

    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * clientScheduling manager.
     * @var Implantation\Service\ClientSchedulingManager
     */
    private $deploymentScheduleManager;

    /**
     * Auth service.
     * @var Zend\Authentication\Authentication
     */
    private $authService;

    /**
     * Array used to creating dinamic filters
     */
    private $currentUser;

    /**
     * Array used to creating dinamic filters
     */
    private $searchArray = [
        'idClient' => 'IdCliente',
        'date' => 'Data',
        'time' => 'Horário Agendado',
        'status' => 'Status'
    ];

    /**
     * Constructor.
     */
    public function __construct($entityManager, $authService, $deploymentScheduleManager)
    {
        $this->entityManager = $entityManager;
        $this->authService = $authService;
        $this->deploymentScheduleManager = $deploymentScheduleManager;

        $this->currentUser = $this->entityManager->getRepository(User::class)
                                           ->findOneByEmail($this->authService->getIdentity());

    }

    /**
     * This is the default "index" action of the controller. It displays the
     * list of completed process contracts.
     */
    public function indexAction()
    {
        $id_tecnico = $this->currentUser->getUserIdCrm();
        $datas = $this->csecAPI()->doGETRequest("cliente-implantacao",
                                                  $this->getRequest(),
                                                  0,
                                                  $id_tecnico);

        return new ViewModel([
            'datas' => $datas,
            'search' => $this->searchArray,
            'operators' => $this->searchMethods
        ]);
    }

    /**
     * The "Search" action is used to filter data in the search.
     */
    public function searchAction()
    {
        return true;
        if ($this->getRequest()->isGet()) {

            $search = $this->params()->fromQuery();
            $finder = $this->csecFilter()->performWhereString($search);
            $responseData = $this->getCompletedProcessAction($finder);

        }

        $retorno = [];

        if(count($responseData) == 0) {
            $retorno['data'] = [];
        } else {
            foreach ($responseData as $key => $data) {

                $retorno[$key] = [
                    '0' => $data['cotIdcontrato'],
                    '1' => $data['cliIdcliente'],
                ];

            }
        }

        return new JsonModel(['data' => $retorno]);
    }

    public function confirmAction()
    {

        $id = (int)$this->params()->fromRoute('id', -1);

        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $scheduling = $this->csecAPI()
                           ->doGETRequest("agendamento-implantacao",
                                          $this->getRequest(), 0, $id);

        if ($scheduling == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $horasDisponiveis = $this->csecAPI()
                                 ->doGETRequest("horas-disponiveis-implantacao",
                                                $this->getRequest(), 0, $scheduling[0]['atdIdcontrato']);

        $form = new DeploymentScheduleForm('client', $this->entityManager);

        // Fill in the form with POST data
        $data = $this->params()->fromPost();

        if (!empty($data)) {

            if (!isset($data['status']))
                $data['status'] = 1;

            $form->setData($data);

            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $dataForm = $form->getData();
                $dataForm['user'] = $this->currentUser->getId();
                $clienteNome  = $data['client_name'];
                $clienteEmail = $data['client_email'];

                if (isset($data['time_end_init']) && !isset($data['time_end'])) {
                    $dataForm['time_end'] = $data['time_end_init'];
                }

                unset($dataForm['submit']);
                unset($dataForm['client_email']);

                $dataForm['id_attendance_crm'] = $data['id_attendance_crm'];

                if(isset($data['date']) && !empty($data['date'])){
                    $dt = $data['date'];
                    $dataForm['date'] = $data['date'];
                } else if(isset($data['data']) && !empty($data['data'])) {
                    $dt = $data['data'];
                    $dataForm['date'] = $data['data'];
                }

                // Add DeploymentSchedule.
                $deploymentSchedule = $this->deploymentScheduleManager->addDeploymentSchedule($dataForm, $clienteNome, $clienteEmail);

                $date = explode('/', $dt);
                $dataSave['atdDtcadastro'] = $date[2]."-".$date[1]."-".$date[0];
                $dataSave['atdHorainicio'] = $data['time'].":00";

                if (isset($data['time_end_init'])) {
                    $dataSave['atdHorafinal']  = $data['time_end_init'].":00";
                }
                if (isset($data['time_end'])) {
                    $dataSave['atdHorafinal']  = $data['time_end'].":00";
                }

                $dataSave['atdSituacao'] = $data['status'];
                $idCRM = $data['id_attendance_crm'];

                $setNewDateCRM = $this->csecAPI()
                                       ->doPOSTRequest("agendamento-implantacao",
                                                       (object)$dataSave, $idCRM);

                // Redirect to "view" page
                return $this->redirect()->toRoute('deployment_schedule',
                        ['action'=>'view',
                         'id'=>$deploymentSchedule->getId()]);
            }
        }

        return new ViewModel([
            'scheduling' => $scheduling,
            'form' => $form,
            'horas_cliente' => $horasDisponiveis['saldo']/60
        ]);
    }

    public function getHoursByDateAction()
    {
        if ($this->getRequest()->isGet()) {

            $date = $this->params()->fromQuery();
            $date = $date['date'];
            $dt = new Datetime($date);
            $dtCadastro = $dt->format('Y-m-d');

            $unavailable = array();
            $queryUri = ['data' => $dtCadastro,
                         'idFuncionario' => $this->currentUser->getUserIdCrm()];

            $unavailableDatas = $this->csecAPI()->doGETRequest('atendimentos-do-dia',
                                                                 $this->getRequest(),
                                                                 0,
                                                                 $queryUri);

            foreach($unavailableDatas as $un){
                $data   = new Datetime($un['atdDtcadastro']['date']);
                $inicio = new Datetime($un['atdHorainicio']['date']);
                $fim    = new Datetime($un['atdHorafinal']['date']);
                $unavailable[] = [
                   'start' => $inicio->format('H:i'),
                   'end'   =>$fim->format('H:i')
                ];
            }

            return new JsonModel(['data' => $unavailable]);
        }
    }
}
