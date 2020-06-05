<?php
namespace Base\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Quiz\V1\Entity\QuestionFieldFilledValue;

/**
 * This controller plugin is used for role-based access control (RBAC).
 */
class CsecInputPlugin extends AbstractPlugin
{
    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Authentication service.
     * @var Zend\Authentication\AuthenticationService
     */
    private $authService;

    /**
     * Constructor.
     */
    public function __construct($entityManager, $authService)
    {
        $this->entityManager = $entityManager;
        $this->authService = $authService;
    }

    public function createInput($question, $field=false, $idDeploymentSchedule=false, $enunciado=false)
    {

        if(is_array($question)) {
            $type = $question['type'];
            $id = $question['id'];
            $name = $question['name'];
            $formName = $question['formName'];
        } else {
            $type = $question->getType();
            $id = $question->getId();
            $name = $question->getName();
            $formName = 'questionario';
        }

        switch ($type) {
            case 0:
                return $this->createLabel($id, $formName, $idDeploymentSchedule, $enunciado); break;
            case 1:
                return $this->createNumber($id, $formName, $idDeploymentSchedule, $enunciado); break;
            case 2:
                return $this->createTextBox($id, $formName, $idDeploymentSchedule, $enunciado); break;
            case 3:
                return $this->createTextArea($id, $formName, $idDeploymentSchedule, $enunciado); break;
            case 4:
                return $this->createCheckbox($id, $name, $formName, $idDeploymentSchedule, $field); break;
            case 5:
                return $this->createRadio($id, $name, $formName, $idDeploymentSchedule, $field); break;
            default:
                return false; break;
        }
    }

    private function all()
    {

        if(is_array($field)){
            $id = $field['id'];
            $label = $field['label'];
        } else {
            $id = $field->getId();
            $label = $field->getLabel();
        }

        if ($resposta) {
            if($resposta->getValue() == $id)
                $resposta = $resposta->getValue();

        } else {
            $resposta = '';
        }

        if($resposta == $id){
            $checked = 'checked';
        } else {
            $checked  = '';
        }


        if (is_array($question)) {
            $name = $question['id'];
            $formName = $question['formName'];
        } else {
            $name = $question->getId();
            $formName = 'questionario';
        }

        if (is_array($field)) {
            $id = $field['id'];
            $label = $field['label'];
        } else {
            $id = $field->getId();
            $label = $field->getLabel();
        }

        if ($resposta) {
            if($resposta[0]->getValue() == $id){
                $checked = 'checked';
            }
        } else {
            $checked  = '';
        }
    }

    private function createLabel($id, $formName, $idDeploymentSchedule, $enunciado)
    {
        return "<label for='{$id}'>{$enunciado}</label>";
    }

    private function createNumber($id, $formName, $idDeploymentSchedule, $enunciado)
    {
        $respondido   = $this->entityManager
                             ->getRepository(QuestionFieldFilledValue::class)
                             ->findOneBy(['idDeploymentSchedule' => $idDeploymentSchedule,
                                          'idQuestion' => $id]);

        if ($respondido) {
            $resposta = $respondido->getValue();
            $dataRespondido = "data-respondido='true'";
            $readonly = 'readonly="readonly"';
        } else {
            $resposta = '';
            $dataRespondido = "data-respondido='false'";
            $readonly = '';
        }


        $number = "<label for='{$id}'>{$enunciado}</label>";
        $number .= "<input type='number'
                          id='{$id}'
                          class='form-control'
                          name='{$formName}[{$id}]'
                          value='{$resposta}'
                          {$dataRespondido}
                          {$readonly}
                          >";

        return $number;
    }

    private function createTextBox($id, $formName, $idDeploymentSchedule, $enunciado)
    {

        $respondido   = $this->entityManager
                             ->getRepository(QuestionFieldFilledValue::class)
                             ->findOneBy(['idDeploymentSchedule' => $idDeploymentSchedule,
                                       'idQuestion' => $id]);
        if ($respondido) {
            $resposta = $respondido->getValue();
            $dataRespondido = "data-respondido='true'";
            $readonly = 'readonly="readonly"';
        } else {
            $resposta = '';
            $dataRespondido = "data-respondido='false'";
            $readonly = '';
        }

        $texto = "<label for='{$id}'>{$enunciado}</label>";
        $texto .= "<input type='text'
                          id='{$id}'
                          class='form-control'
                          name='{$formName}[{$id}]'
                          value='{$resposta}'
                          {$dataRespondido}
                          {$readonly}
                          >";

        return $texto;
    }

    private function createTextArea($id, $formName, $idDeploymentSchedule, $enunciado)
    {

        $respondido   = $this->entityManager
                             ->getRepository(QuestionFieldFilledValue::class)
                             ->findOneBy(['idDeploymentSchedule' => $idDeploymentSchedule,
                                          'idQuestion' => $id]);
        if ($respondido) {
            $resposta = $respondido->getValue();
            $dataRespondido = "data-respondido='true'";
            $readonly = 'readonly="readonly"';
        } else {
            $resposta = '';
            $dataRespondido = "data-respondido='false'";
            $readonly = '';
        }


        $area = "<label for='{$id}'>{$enunciado}</label>";
        $area .= "<textarea id='{$id}'
                            class='form-control'
                            {$dataRespondido}
                            name='{$formName}[{$id}]'>";
                            $area .= $resposta;
        $area .= "</textarea>";

        return $area;
    }

    private function createCheckbox($id, $name, $formName, $idDeploymentSchedule, $field)
    {

        if(is_array($field)){
            $idAwnser = $field['id'];
            $label = $field['label'];
        } else {
            $idAwnser = $field->getId();
            $label = $field->getLabel();
        }

        $respondido   = $this->entityManager
                             ->getRepository(QuestionFieldFilledValue::class)
                             ->findOneBy(['idDeploymentSchedule' => $idDeploymentSchedule,
                                       'idQuestion' => $id,
                                       'value' => $idAwnser]);
        if ($respondido) {
            $checked = 'checked';
            $dataRespondido = "data-respondido='true'";
            $disabled = 'disabled="disabled"';
        } else {
            $dataRespondido = "data-respondido='false'";
            $disabled = '';
        }

        $check = "<div class='form-check-inline'>";
            $check .= "<label class='form-check-label'>";
                $check .= "<input type='checkbox'
                                  id='{$id}'
                                  class='form-check-input'
                                  name='{$formName}[{$id}][]'
                                  value='{$idAwnser}'
                                  {$checked}
                                  {$dataRespondido}
                                  {$disabled}
                                  >";
                $check .= $label;
            $check .= "</label>";
        $check .= "</div></br>";

        return $check;
    }

    private function createRadio($id, $name, $formName, $idDeploymentSchedule, $field)
    {
        if(is_array($field)){
            $idAwnser = $field['id'];
            $label = $field['label'];
        } else {
            $idAwnser = $field->getId();
            $label = $field->getLabel();
        }

        $respondido   = $this->entityManager
                             ->getRepository(QuestionFieldFilledValue::class)
                             ->findOneBy(['idDeploymentSchedule' => $idDeploymentSchedule,
                                       'idQuestion' => $id,
                                       'value' => $idAwnser]);
        if ($respondido) {
            $checked = 'checked';
            $dataRespondido = "data-respondido='true'";
            $disabled = 'disabled="disabled"';
        } else {
            $checked = '';
            $dataRespondido = "data-respondido='false'";
            $disabled = '';
        }

        $radio  = "<div class='form-check-inline'>";
            $radio .= "<label class='form-check-label'>";
                $radio .= "<input type='radio'
                                  id='{$id}'
                                  class='form-check-input'
                                  name='{$formName}[{$id}]'
                                  value='{$idAwnser}'
                                  {$checked}
                                  {$dataRespondido}
                                  {$disabled}
                                  > {$label}";
            $radio .= "</label>";
        $radio .= "</div></br>";

        return $radio;
    }
}