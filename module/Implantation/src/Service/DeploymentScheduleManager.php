<?php
namespace Implantation\Service;

use Implantation\V1\Entity\DeploymentSchedule;
use DateTime;
use Time;
use Exception;

/**
 * This service is responsible for adding/editing deployment-schedules.
 */
class DeploymentScheduleManager
{
    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    private $windelMail;

    /**
     * Constructs the service.
     */
    public function __construct($entityManager, $windelMail)
    {
        $this->entityManager = $entityManager;
        $this->windelMail = $windelMail;
    }

    /**
     * This method adds a new deployment-_chedule.
     */
    public function addDeploymentSchedule($data, $name, $mail)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();

            $date = DateTime::createFromFormat('d/m/Y', $data['date']);

            // Create new DeploymentSchedule entity.
            $deploymentSchedule = new DeploymentSchedule();
            $deploymentSchedule->setIdClient($data['id_client']);
            $deploymentSchedule->setIdAttendanceCrm($data['id_attendance_crm']);
            $deploymentSchedule->setClientName($data['client_name']);
            $deploymentSchedule->setDate($date->format('Y-m-d'));
            $deploymentSchedule->setTime($data['time']);

            if(isset($data['time_end']))
                $deploymentSchedule->setTimeEnd($data['time_end']);
            if(isset($data['time_end_init']))
                $deploymentSchedule->setTimeEnd($data['time_end_init']);

            if(isset($data['obs'])){
                $deploymentSchedule->setObs($data['obs']);
            }
            else{
                $deploymentSchedule->setObs('Agendamento confirmado pelo técnico.');
            }
            if(isset($data['id_last_ds'])){
                $deploymentSchedule->setIdLastDs($data['id_last_ds']);
            }

            $deploymentSchedule->setStatus($data['status']);
            $hoje = new DateTime('now');
            $deploymentSchedule->setCreated($hoje->format('Y-m-d H:i:s'));

            // Add the entity to the entity manager.
            $this->entityManager->persist($deploymentSchedule);
            // Apply changes to database.
            $this->entityManager->flush();

            $conn->commit();

            /* SEND EMAIL */
            #$this->sendMailConfirmation($deploymentSchedule, $name, $mail);

            return $deploymentSchedule;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    /**
     * This method updates data of an existing deployment-_chedule.
     */
    public function updateDeploymentSchedule($deploymentSchedule, $data, $name, $mail)
    {
        $conn = $this->entityManager->getConnection();
        try {

            $conn->beginTransaction();
            $deploymentSchedule->setStatus($data['status']);

            $hoje = new DateTime('now');
            $deploymentSchedule->setFinished($hoje->format('Y-m-d H:i:s'));
            // Apply changes to database.
            $this->entityManager->flush();
            $conn->commit();
            /* SEND EMAIL */
            #$this->sendMailFinish($deploymentSchedule, $name, $mail);

            return true;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }

    }

    public function patchDeploymentSchedule($deploymentSchedule, $data, $name=false, $mail=false, $status=true)
    {
        $conn = $this->entityManager->getConnection();

        try {

            $conn->beginTransaction();
            $obs = $deploymentSchedule->getObs();

            if (!$status) {
                $deploymentSchedule->setStatus(0);
            } else {

                if (isset($data['date'])) {
                    $date = new DateTime($date['date']);
                    $date = DateTime::createFromFormat('d/m/Y', $data['date']);
                    $deploymentSchedule->setDate($date->format('Y-m-d'));
                }
                if (isset($data['time'])) {
                    $time = new DateTime($data['time']);
                    $deploymentSchedule->setTime($time->format('H:i'));
                }
                if(isset($data['started']) && empty($deploymentSchedule->getStarted())) {
                    $deploymentSchedule->setStarted($data['started']);
                }
            }

            if (isset($data['updated'])) {
                $deploymentSchedule->setUpdated($data['updated']);
            }

            // Apply changes to database.
            $this->entityManager->flush();

            $conn->commit();

            /* SEND EMAIL */
            // $this->sendMailReschedule($deploymentSchedule, $name, $mail);

            return $deploymentSchedule;

        } catch (Exception $e) {
            $conn->rollBack();
            return $e->getMessage();
        }
    }

    /**
     * This method remove data of an existing DeploymentSchedule.
     */
    public function removeDeploymentSchedule($data)
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
     * Checks whether an active DeploymentSchedule with given name already exists in the database.
     */
    public function checkDeploymentScheduleExists($client_id, $date)
    {

        $deploymentSchedule = $this->entityManager->getRepository(DeploymentSchedule::class)
                                                   ->findBy(['idClient' => $client_id, 'date' => $date],
                                                            ['id'=>'ASC']);

        return count($deploymentSchedule) > 0;
    }

    private function sendMailConfirmation($atendimento, $name, $mail)
    {

        $subject = 'Confirmação de Implantação';
        $httpHost = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost';
        $user = ['name' => $name, 'email' => $mail];

        // Send email to user.
        $options['header'] = [
            'client_name' => $name
        ];
        $atd = $atendimento->getDate();
        $options['content'] = [
            'implantation_date' => $atendimento->getDate()->format('d/m/Y'),
            'implantation_hour' => $atendimento->getTime()->format('H:i'),
            'cancelamento_date' => $atd->modify("-1 day")->format('d/m/Y')
        ];

        $this->windelMail->sendMail($user, $subject, 'confirmar_implantacao', $options, false);
    }

    private function sendMailReschedule($atendimento, $name, $mail)
    {

        $subject = 'Pausa na Implantação';
        $httpHost = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost';
        $user = ['name' => $name, 'email' => $mail];

        // Send email to user.
        $options['header'] = [
            'cliente_nome' => $name
        ];
        $options['content'] = [
            'implantation_date' => $atendimento->getDate()->format('d/m/Y')." ás ".$atendimento->getTime()->format('H:i'),
        ];

        $this->windelMail->sendMail($user, $subject, 'pausar_implantacao', $options, false);
    }

    private function sendMailFinish($atendimento, $name, $mail)
    {

        $subject = 'Conclusão de Implantação';
        $httpHost = isset($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:'localhost';
        $user = ['name' => $name, 'email' => $mail];

        // Send email to user.
        $options['header'] = [
            'cliente_nome' => $name
        ];
        $options['content'] = [
            'implantacao_dia'  => $atendimento->getDate()->format('d/m/Y'),
            'implantacao_hora' => $atendimento->getTimeEnd()->format('H:i'),
            'resumo_link'      => \Base\Module::CLIENTE_ENV."implantacao"
        ];

        $this->windelMail->sendMail($user, $subject, 'finalizar_implantacao', $options, false);
    }
}