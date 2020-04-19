<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Implantation\Controller;

use Implantation\Controller\ImplantationController;
use Implantation\Form\DeploymentScheduleForm;
use Implantation\V1\Entity\DeploymentSchedule;
use CMS\V1\Entity\User;
use Quiz\V1\Entity\Question;
use Quiz\V1\Entity\QuestionForm;
use Quiz\V1\Entity\QuestionField;
use Quiz\V1\Entity\QuestionFieldFilledValue;

use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Datetime;

use Base\Event\EventBase;

class DeploymentScheduleController extends ImplantationController
{

    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * DeploymentSchedule manager.
     * @var Implantation\Service\DeploymentScheduleManager
     */
    private $deploymentScheduleManager;

    /**
     * QuestionFieldFilledValueManager manager.
     * @var Quiz\Service\QuestionFieldFilledValueManager
     */
    private $questionFilledValueManager;

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
        'timeEnd' => 'Horáio Finalizado',
        'status' => 'Status'
    ];

    /**
     * Constructor.
     */
    public function __construct($entityManager, $dsm, $qffvm, $authService)
    {
        $this->entityManager = $entityManager;
        $this->deploymentScheduleManager = $dsm;
        $this->QuestionFilledValueManager = $qffvm;
        $this->authService = $authService;

        $this->currentUser = $this->entityManager->getRepository(User::class)
                                           ->findOneByEmail($this->authService->getIdentity());
    }

    /**
     * This is the default "index" action of the controller. It displays the
     * list of deployment_schedules.
     */
    public function indexAction()
    {
        $deploymentSchedules = $this->entityManager->getRepository(DeploymentSchedule::class)
                                                   ->findBy([], ['id'=>'ASC']);

        return new ViewModel(
            ['deploymentSchedules' => $deploymentSchedules,
            'search' => $this->searchArray,
            'operators' => $this->searchMethods]);
    }

    /**
     * The "Search" action is used to filter data in the search.
     */
    public function searchAction()
    {

        $qb = $this->entityManager->createQueryBuilder();
        $alias = "ds";

        if ($this->getRequest()->isGet()) {

            $search = $this->params()->fromQuery();
            $finder = $this->windelFilter()->performWhereString($search, $alias);

        } else {
            $finder = $alias.".id > 0";
        }

        $deploymentSchedules = $qb->select($alias)
                   ->from(DeploymentSchedule::class, $alias)
                   ->where($finder)
                   ->getQuery();

        $returnArr = array();

        if(count($deploymentSchedules->getResult()) == 0) {
            $returnArr['data'] = [];
        } else {
            foreach ($deploymentSchedules->getResult() as $key => $deploymentSchedule) {

                $returnArr[$key] = [

                    '0' => $deploymentSchedule->getIdClient(),
                    '1' => $deploymentSchedule->getDate()->format('d/m/Y'),
                    '2' => $deploymentSchedule->getTime()->format('H:i'),
                    '3' => $deploymentSchedule->getTimeEnd() ? $deploymentSchedule->getTimeEnd()->format('H:i') : "",
                    '4' => $deploymentSchedule->getStatusAsString(),
                    '5' => $deploymentSchedule->getStatus() == 1 ? $this->windelHtml()->getActionButton('agendamento', $deploymentSchedule->getId()) : "",
                ];

            }
        }

        return new JsonModel(['data' => $returnArr]);
    }

    /**
     * This action displays a page allowing to add a new DeploymentSchedule.
     */
    public function addAction()
    {

        $id = (int)$this->params()->fromRoute('id', -1);

        // Create DeploymentSchedule form
        $form = new DeploymentScheduleForm('create', $this->entityManager);
        $cliNome = $this->windelAPI()->doGETRequest("nome-cliente", $this->getRequest(), 0, $id);

        // Check if DeploymentSchedule has submitted the form
        if ($this->getRequest()->isGet()) {
            // Fill in the form with POST data
            $data = $this->params()->fromPost();

            if(!isset($data['status'])){
                $data['atdSituacao'] = 'R';
            }
            $form->setData($data);
            // Validate form
            if($form->isValid()) {

                // Get filtered and validated data
                $data = $form->getData();

                // Add DeploymentSchedule in CRM API way.
                $data['user'] = $this->currentUser->getId();
                $data['crm_user'] = $this->currentUser->getUserIdCrm();
                $data['id_client'] = (int)$data['id_client'];
                $data['id_motivo'] = (int)431;
                $data['id_attendance_crm'] = $this->windelAPI()->doPOSTRequest("agendamento-implantacao", $data);
                unset($data['submit']);

                // Add DeploymentSchedule.
                $deploymentSchedule = $this->deploymentScheduleManager->addDeploymentSchedule($data);

                // Redirect to "view" page
                return $this->redirect()->toRoute('deployment_schedule',
                        ['action'=>'view',
                         'id'=>$deploymentSchedule->getId()]);

            }
        }

        return new ViewModel([
                'form'      => $form,
                'id_client' => $id,
                'cliNome'   => $cliNome
            ]);
    }

    /**
     * The "view" action displays a page allowing to view DeploymentSchedule's details.
     */
    public function viewAction()
    {
        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Find a user with such ID.

        $deploymentSchedule = $this->entityManager->getRepository(DeploymentSchedule::class)->find($id);

        if ($deploymentSchedule == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $result = $this->QuestionFilledValueManager->getAwnseredForm($id);

        $returnValue = [];
        foreach ($result as $key => $value) {
            $formulario = $this->windelFilter()->unSlugfy($value['formulario']);
            $returnValue[$formulario][$key] = $value;
        }

        return new ViewModel([
            'deploymentSchedule' => $deploymentSchedule,
            'result'             => $returnValue
        ]);
    }

    /**
     * The "edit" action displays a page allowing to include an complete process
     * of system implantation in determined client.
     */
    public function editAction()
    {

        $id = (int)$this->params()->fromRoute('id', -1);
        if ($id<1) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $deploymentSchedule = $this->entityManager
                                   ->getRepository(DeploymentSchedule::class)
                                   ->find($id);

        if ($deploymentSchedule == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        // Create user form
        $form = new DeploymentScheduleForm('update', $this->entityManager, $deploymentSchedule);

        // Check if user has submitted the form
        if ($this->getRequest()->isPost()) {

            $scheduling = $this->windelAPI()->doGETRequest("agendamento-implantacao",
                                                    $this->getRequest(),
                                                    0,
                                                    $deploymentSchedule->getIdAttendanceCrm());

            // PEGA OS DADOS DE ENVIO.
            $data = $this->params()->fromPost();
            $cliNome  = $scheduling[0]['cliNome'];
            $cliEmail = $scheduling[0]['cliEmail'];
            unset($data['submit']);
            $dados['atdSituacao'] = 0;

            $hoje = new DateTime('now');
            $started = new DateTime($deploymentSchedule->getStarted());
            $data['finished'] = $hoje->format('Y-m-d H:i:s');

            $diff = $started->diff($hoje);
            $tempoTotal = $diff->format('%H:%I:%S');
            $dados['atdTempoUtilizado'] = $tempoTotal;
            // FINALIZA O ATENDIMENTO NESSE AMBIENTE.
            $ds = $this->deploymentScheduleManager
                       ->updateDeploymentSchedule($deploymentSchedule, $data, $cliNome, $cliEmail);

            // FINALIZA O ATENDIMENTO NO CRM.
            $finalizado = $this->finalizarAgendamentoCRM($deploymentSchedule->getIdAttendanceCrm(), $dados);
            // ATUALIZAR HORAS USADAS.
            $horas = $this->atualizarHorasDisponiveis($scheduling[0], $tempoTotal);

            // REDIRECIONA PARA A VIEW DE RESPOSTAS.
            return $this->redirect()->toRoute('deployment_schedule',
                    ['action'=>'view',
                     'id'=>$deploymentSchedule->getId()]);

        } else {

            $data = [
                'id_client'=>$deploymentSchedule->getIdClient(),
                'date'=>$deploymentSchedule->getDate(),
                'time'=>$deploymentSchedule->getTime(),
                'status'=> 0
            ];

            $form->setData($data);
        }

        $scheduling = $this->windelAPI()->doGETRequest("agendamento-implantacao",
                                                       $this->getRequest(), 0,
                                                       $deploymentSchedule->getIdAttendanceCrm());

        $produtoCliente = $this->windelAPI()->doGETRequest('produto-cliente',
                                                           $this->getRequest(), 0,
                                                           $scheduling[0]['atdIdcliente']);

        $formsQ = $this->entityManager->getRepository(QuestionForm::class)
                                      ->findBy([
                                                 'local' => 'implantacao',
                                                 'idProduto' => [$produtoCliente['modIdmodulo'], 15]
                                               ],
                                               ['sequence' => 'ASC']);

        $questions = array();
        foreach ($formsQ as $formQ) {
            $q = $this->getQuestion($formQ->getName());
            # BUSCA POR ATENDIMENTO RELACIONADO PARA TRAZER PREENCHIDO.
            if($deploymentSchedule->getIdLastDs()){
                $id = $deploymentSchedule->getIdLastDs();
            }

            $questions[$formQ->getName()] = $this->getFormatedQuestions($q, $id);
        }

        $horasDisponiveis = $this->getHorasDisponiveis($scheduling[0]['atdIdcontrato'],
                                                       $scheduling[0]['atdTotalhoras']['date']);

        return new ViewModel(['deploymentSchedule' => $deploymentSchedule,
                              'form' => $form,
                              'timeEnd' => $deploymentSchedule->getTimeEnd(),
                              'formQuestion' => $formsQ,
                              'perguntas' => $questions,
                              'cliente_hour' => $horasDisponiveis/60]);
    }

    private function getHorasDisponiveis($idContrato, $totalHoras)
    {
        $horasDisponiveis = $this->windelAPI()
                                 ->doGETRequest("horas-disponiveis-implantacao",
                                                $this->getRequest(), 0, $idContrato);

        $totalHr = new DateTime($totalHoras);
        $hrs = $totalHr->format('H')*60;
        $min = $totalHr->format('i')*60;

        return $horasDisponiveis["saldo"] - $hrs - $min;
    }

    public function getQuestion($form)
    {
        $qb = $this->entityManager->createQueryBuilder();

        $alias = 'q';
        $select = ['q.id', 'qf.name AS formName', 'q.name','q.type', 'q.required'];
        $finder = "qf.name = '{$form}'";

        $question = $qb->select($select)
                       ->from(Question::class, $alias)
                       ->join($alias.".idQuestionForm", 'qf')
                       ->where($finder)
                       ->andWhere('qf.status = 1')
                       ->orderBy('q.sequence')
                       ->getQuery();

        if(count($question->getResult()) != 0) {
           return $question->getResult();
        }

        return false;
    }

    public function getAwnsers($idQuestion)
    {

        $qb = $this->entityManager->createQueryBuilder();

        $alias = 'qf';
        $select = ['qf.id', 'qf.label'];

        $finder = "qf.idQuestion = '{$idQuestion}'";

        $awnser = $qb->select($select)
                       ->from(QuestionField::class, $alias)
                       ->where($finder)
                       ->orderBy('qf.sequence')
                       ->getQuery();

        if(count($awnser->getResult()) != 0) {
           return $awnser->getResult();
        }
        return false;
    }

    public function getFormatedQuestions($perguntas, $idDeploymentSchedule)
    {
        $questions = array();

        foreach ($perguntas as $pergunta) {

            $idPergunta  = $pergunta['id'];
            $enunciado   = $pergunta['name'];
            $obrigatorio = $pergunta['required'];
            $tipo        = $pergunta['type'];
            $formName    = $pergunta['formName'];

            switch ($tipo) {
                case 1:
                    $tipo = 'number';
                    break;
                case 2:
                    $tipo = 'text';
                    break;
                case 3:
                    $tipo = 'textarea';
                    break;
            }

            $respostas = $this->getAwnsers($idPergunta);

            $question = "<div class='form-group {$obrigatorio}'>";

                if(!$respostas){
                    $question .= $this->windelInput()->createInput($pergunta, false, $idDeploymentSchedule, $enunciado);
                } else {
                    $question .= "<label class='control-label'>{$enunciado}</label><br>";
                    foreach($respostas as $resposta){
                        $question .= $this->windelInput()->createInput($pergunta, $resposta, $idDeploymentSchedule, false);
                    }
                }

            $question .= "</div>";

            $questions[$formName][] = $question;
        }

        return $questions;
    }

    /**
     * The "insertData" action add filled values from dinamic form.
     */
    public function insertDataAction()
    {
        $data = $this->params()->fromPost();
        $scheduling = $data['scheduling'];
        unset($data['scheduling']);
        foreach ($data as $formQuestion => $dt) {
            // CRIA UM DOCUMENTO PARA O CLIENTE ACEITAR
            $documento = $this->criarContratoCrm($formQuestion, $scheduling);

            foreach ($dt as $key => $dado) {
                $q   = $this->entityManager
                        ->getRepository(Question::class)
                        ->findOneBy(['id' => $key]);

                $idField = null;
                if($q->getType() == 5 || $q->getType() == 4){
                    $idField = $dado;
                }

                $saveData = [
                    'form_question' => $formQuestion,
                    'id_question' => $key,
                    'scheduling' => $scheduling,
                    'value' => $dado,
                    'id_field' => $idField
                ];

                $re   = $this->entityManager
                             ->getRepository(QuestionFieldFilledValue::class)
                             ->findBy(['idDeploymentSchedule' => $scheduling,
                                       'idQuestion' => $key]);

                if($re){
                    foreach($re as $r){
                        $this->QuestionFilledValueManager->removeQuestionFieldFilledValue($r);
                    }
                }

                if($this->QuestionFilledValueManager->addQuestionFieldFilledValue($saveData)){
                    $saved = true;
                } else {
                    $saved = false;
                }

            }
        }

        return $saved;
    }

    /**
     * REGISTRA UM CONTRATO NO CRM.
     */
    private function criarContratoCrm($tipoRoteiro, $implantacao)
    {

        $iz   = $this->entityManager->getRepository(DeploymentSchedule::class)
                                    ->findOneBy(['id' => $implantacao]);

        $atdCRM = $iz->getIdAttendanceCrm();
        // BUSCA O REGISTRO DE ATENDIMENTO.
        $scheduling = $this->windelAPI()->doGETRequest("agendamento-implantacao",
                                                       $this->getRequest(), 0,
                                                       $atdCRM);
        $cot = $scheduling[0]['atdIdcontrato'];
        // BUSCA O REGISTRO DO PEDIDO DE IMPLANTACAO.
        $pedImp = $this->windelAPI()->doGETRequest('contratos',
                                                   $this->getRequest(), 0,
                                                   $cot);

        if ($tipoRoteiro == 'termo_de_levantamento_de_dados') {
            $situacao = '13';
            $tipoRoteiro = null;
        } else {
            $situacao = '14';
        }

        $hoje = new DateTime('now');
        $params = array();
        $params['cotVlrmensal']     = $pedImp['cotVlrmensal'];
        $params['cotFuncionamento'] = $pedImp['cotFuncionamento'];
        $params['cotCliNome']       = $pedImp['cotCliNome'];
        $params['cotCliContato']    = !empty($pedImp['cotCliContato']) ? $pedImp['cotCliContato']:  ' - ';
        $params['cotCliBairro']     = $pedImp['cotCliBairro'];
        $params['cotCliCidade']     = $pedImp['cotCliCidade'];
        $params['cotCliFone1']      = $pedImp['cotCliFone1'];
        $params['cotCliIdmotivo']   = $pedImp['cotCliIdmotivo'];
        $params['cotIdrevenda']     = $pedImp['cotIdrevenda'];
        $params['cotCliEmail']      = $pedImp['cotCliEmail'];

        $params['cotStatus'] = 4;
        $params['cotIdcliente'] = $pedImp['cotIdcliente'];
        $params['cotDtcadastro'] = $hoje->format('Y-m-d');
        $params['cotHoracadastro'] = $hoje->format('H:i:s');
        $params['cotSituacao'] = $situacao;
        $params['cotIdproduto'] = $pedImp['cotIdproduto'];
        $params['cotCliCnpjcpf'] = $pedImp['cotCliCnpjcpf'];
        $params['cotObs'] = 'Documento criado automaticamente pelo sistema de implantação.';
        $params['cotImplantacaoAtendimento'] = $atdCRM;
        $params['cotImplantacaoTipoRoteiro'] = $tipoRoteiro;

        return $this->windelAPI()->doPOSTRequest('contratos', $params);
    }

    /**
     * The "insertReschedule" altera a data do agendamento atual.
     */
    public function insertRescheduleAction()
    {
        try {
            if ($this->getRequest()->isPost()) {

                $data = $this->params()->fromPost();

                $deploymentSchedule = $this->entityManager
                                           ->getRepository(DeploymentSchedule::class)
                                           ->find($data['id']);

                // BUSCA O AGENDAMENTO ATUAL NO CRM
                $atdCRM = $this->windelAPI()->doGETRequest("agendamento-implantacao",
                                                        $this->getRequest(), 0,
                                                        $deploymentSchedule->getIdAttendanceCrm());
                $atdCRM = $atdCRM[0];
                $cliNome = $atdCRM['cliNome'];
                $cliEmail = $atdCRM['cliEmail'];
                $dados['atdSituacao'] = 0;

                $hoje = new DateTime('now');
                $started = new DateTime($deploymentSchedule->getStarted());

                $diff = $started->diff($hoje);
                $tempoTotal = $diff->format('%H:%I:%S');
                $dados['atdTempoUtilizado'] = $tempoTotal;

                // 1 GERA UM NOVO REGISTRO DE ATENDIMENTO NO CRM
                $novo = $this->gerarNovoAtendimentoCRM($data, $atdCRM);
                // 2 FINALIZA O ATENDIMENTO NO CRM
                $finalizado = $this->finalizarAgendamentoCRM($deploymentSchedule->getIdAttendanceCrm(), $dados);
                // 3 CRIA UM NOVO REGISTRO DE ATENDIMENTO NESSE BANCO
                $agendado = $this->gerarNovoAtendimentoZend($deploymentSchedule, $novo, $data, $cliNome, $cliEmail);
                // 4 FINALIZA O ATENDIMENTO DO ZENDEL
                $data['updated'] = $hoje->format('Y-m-d H:i:s');
                $ds = $this->deploymentScheduleManager
                           ->patchDeploymentSchedule($deploymentSchedule, $data, $cliNome, $cliEmail, 0);

                // 5 ATUALIZAR HORAS USADAS.
                $horas = $this->atualizarHorasDisponiveis($atdCRM, $tempoTotal);

                return $this->redirect()->toRoute('deployment_schedule', ['action'=>'index']);
            }
        } catch (Exception $e) {
             echo $e->getMessage();
             die;
        }
    }

    private function gerarNovoAtendimentoCRM($dadosNovos, $atendimentoBase)
    {
        $data = $dadosNovos;

        $date = explode('/', $data['date']);
        $dataSave['atdIdrevenda']   = $atendimentoBase['atdIdrevenda'];
        $dataSave['atdIdcliente']   = $atendimentoBase['atdIdcliente'];
        $dataSave['atdIdcontrato']  = $atendimentoBase['atdIdcontrato'];
        $dataSave['atdIdfuncionario'] = $this->currentUser->getUserIdCrm();
        $dataSave['atdTipo']          = $atendimentoBase['atdTipo'];
        $dataSave['atdHorainicio'] = $data['time'].":00";
        $dataSave['atdHorafinal']  = $data['time_end'].":00";
        $dataSave['atdIdmotivo'] = (int)279;
        $dataSave['atdObs']      = "Reagendamento pelo técnico via Área de Implantação.";
        $dataSave['atdDtcadastro'] = $date[2]."-".$date[1]."-".$date[0];
        $dataSave['atdSituacao']   = 'A';
        $dataSave['atdIdusuario']  = 16;
        $dataSave['atdContato']    = "Administrador do Sistema.";
        $dataSave['atdConfirmado'] = 1;

        return $this->windelAPI()->doPOSTRequest("agendamento-implantacao", (object)$dataSave);
    }

    private function gerarNovoAtendimentoZend($atdAtual, $novo, $dadosAtual, $clienteNome, $clienteEmail)
    {
        $dDate = $atdAtual->getDate()->format('d/m/Y');
        $dataToResave = [];

        $dataToResave['id_client'] = $atdAtual->getIdClient();
        $dataToResave['id_attendance_crm'] = $novo;
        $dataToResave['id_last_ds'] = $atdAtual->getId();
        $dataToResave['client_name'] = $clienteNome; #$clienteNome
        $dataToResave['date'] = $dadosAtual['date']; #$dadosAtual
        $dataToResave['time'] = $dadosAtual['time'];
        $dataToResave['time_end'] = $dadosAtual['time_end'];
        $dataToResave['status'] = 1;
        $dataToResave['obs'] = "Pausa na implantação do dia: {$dDate}. Reagendamento pelo técnico.";

        return $this->deploymentScheduleManager->addDeploymentSchedule($dataToResave, $clienteNome, $clienteEmail);
    }

    private function finalizarAgendamentoCRM($idAtendimento, $dados)
    {

        return $this->windelAPI()->doPOSTRequest("agendamento-implantacao", (object)$dados, $idAtendimento);
    }

    private function atualizarHorasDisponiveis($atdAtual, $tempoTotal)
    {
        $horasDisponiveis = $this->windelAPI()
                                 ->doGETRequest("horas-disponiveis-implantacao",
                                                $this->getRequest(), 0, $atdAtual['atdIdcontrato']);

        $hoje = new DateTime('now');
        $tempoUsado = new DateTime($tempoTotal);

        $hrsUsado = $tempoUsado->format('H')*60;
        $minUsado = $tempoUsado->format('i')*60;
        $tempoFinal = ($segUsado + $minUsado)/60;

        $dataSave['HUS_IDATENDIMENTO'] = $atdAtual['atdIdatendimento'];
        $dataSave['HUS_IDCONTRATADA'] = $horasDisponiveis['id'];
        $dataSave['HUS_HORAS'] = $tempoFinal;
        $dataSave['HUS_DATA'] = $hoje->format('Y-m-d');
        $dataSave['HUS_IDCLIENTE'] = $atdAtual['atdIdcliente'];
        $dataSave['atdIdcontrato'] = $atdAtual['atdIdcontrato'];

        return $this->windelAPI()->doPOSTRequest("horas-disponiveis-implantacao", (object)$dataSave, $horasDisponiveis['id']);
    }

    /**
     * The "remove" action remove the DeploymentSchedule item from database.
     */
    public function removeAction()
    {
        // $id = $this->params()->fromRoute('id');
        $id = $this->params()->fromPost('id');

        $deploymentSchedule = $this->entityManager->getRepository(DeploymentSchedule::class)
                    ->findOneById($id);

        if ($deploymentSchedule == null) {
          $this->getResponse()->setStatusCode(404);
          return;
        }

        $this->deploymentScheduleManager->removeDeploymentSchedule($deploymentSchedule);

        // Redirect the DeploymentSchedule to "index" page.
        return $this->redirect()->toRoute('deployment_schedule', ['action'=>'index']);
    }

    /**
     * The ToggleActive action change status more quickly.
     */
    public function toggleActiveAction()
    {
        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();

            $deploymentSchedule = $this->entityManager->getRepository(DeploymentSchedule::class)->find($data['id']);

            if ($deploymentSchedule == null) {
                $this->getResponse()->setStatusCode(404);
                return;
            }

            $this->deploymentScheduleManager->patchDeploymentSchedule($deploymentSchedule, $data);
            return true;
        }
    }

    public function iniciarImplantacaoCRMAction()
    {
        $data = $this->params()->fromPost();
        $id = $data['scheduling'];

        $deploymentSchedule = $this->entityManager
                                   ->getRepository(DeploymentSchedule::class)
                                   ->find($id);

        if ($deploymentSchedule == null) {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        $idCRM = $deploymentSchedule->getIdAttendanceCrm();
        $dataSave['atdIdmotivo'] = 431;

        return $this->windelAPI()->doPOSTRequest("agendamento-implantacao", (object)$dataSave, $idCRM);
    }

    public function startImplantacaoAction()
    {
        try {

            if ($this->getRequest()->isPost())
            {

                $data = $this->params()->fromPost();
                $id = $data['scheduling'];

                $deploymentSchedule = $this->entityManager
                                           ->getRepository(DeploymentSchedule::class)
                                           ->find($id);

                if ($deploymentSchedule == null)
                {
                    $this->getResponse()->setStatusCode(404);
                    return;
                }

                $hoje = new DateTime('now');
                $data['started'] = $hoje->format('Y-m-d H:i:s');

                return $this->deploymentScheduleManager
                           ->patchDeploymentSchedule($deploymentSchedule, $data);

            }

        } catch (Exception $e) {
             echo $e->getMessage();
             die;
        }
    }
}