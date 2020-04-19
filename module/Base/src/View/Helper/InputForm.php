<?php
namespace Base\View\Helper;

use Zend\View\Helper\AbstractHelper;
/**
 * This view helper class displays inputs by database.
 */
class InputForm extends AbstractHelper
{

    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager = [];

    private $statement;
    private $awnsers = [];

    /**
     * Constructor.
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function setStatement($statement)
    {
        $this->statement = $statement;
    }

    public function setAwnser($awnser)
    {
        $this->awnsers = $awnser;
    }

    /**
     * Renders the breadcrumbs.
     * @return string HTML code of the breadcrumbs.
     */
    public function render()
    {
        $question = "<div class='question'>";
            $question .= "<span>".$this->statement."</span>";

            $respostas = [];
            $final = [];

            foreach ($this->awnsers as $awnser) {

                $respostas = $this->createInput($awnser);
                $final[] = $respostas;
            }

            echo $question."<br>";

            foreach($final as $fim) {
                echo $fim;
            }
            echo "<hr>";
        echo "</div>";
    }

    public function createInput($field)
    {

        switch ($field['type']) {
            case 1:
                return $this->createNumber($question); break;
            case 2:
                return $this->createTextBox($question); break;
            case 3:
                return $this->createTextArea($question); break;
            case 4:
                return $this->createCheckbox($field, $question); break;
            case 5:
                return $this->createRadio($field, $question); break;
            default:
                return false; break;
        }
    }

    private function createNumber($question)
    {

        $id = $question->getId();
        $name = $question->getName();

        $number = "<div class='form-group'>";
            $number .= "<label for='{$id}'>";
                $number .= $label;
            $number .= "</label>";
            $number .= "<input type='number'
                              id='{$id}'
                              class='form-control'
                              name='questionario[{$name}]'
                              >";

        $number .= "</div>";

        return $number;
    }

    private function createTextBox($question)
    {
        $id = $question->getId();
        $name = $question->getName();

        $texto = "<div class='form-group'>";
            $texto .= "<input type='text'
                              id='{$id}'
                              class='form-control'
                              name='questionario[{$name}]'
                              >";

        $texto .= "</div>";

        return $texto;
    }

    private function createTextArea($question)
    {

        $id = $question->getId();
        $name = $question->getName();

        $area = "<div class='form-group'>";
            $area .= "<label for='{$id}'>";
                $area .= $label;
            $area .= "</label>";
            $area .= "<textarea id='{$id}'
                                class='form-control'
                                name='questionario[{$name}]'>";
            $area .= "</textarea>";
        $area .= "</div>";

        return $area;
    }

    private function createCheckbox($field, $question)
    {

        $id = $field->getId();
        $name = $question->getId();
        $label = $field->getLabel();

        $check = "<div class='form-check-inline'>";
            $check .= "<label class='form-check-label'>";
                $check .= "<input type='checkbox'
                                  id='{$id}'
                                  class='form-check-input'
                                  name='questionario[{$name}][]'
                                  value='{$id}'>";
                $check .= $label;
            $check .= "</label>";
        $check .= "</div>";

        return $check;
    }

    private function createRadio($field, $question)
    {
        $id = $field->getId();
        $name = $question->getId();
        $label = $field->getLabel();

        $radio  = "<div class='form-check-inline'>";
            $radio .= "<label class='form-check-label'>";
                $radio .= "<input type='radio'
                                  id='{$id}'
                                  class='form-check-input'
                                  name='questionario[{$name}]'
                                  > {$label}";
            $radio .= "</label>";
        $radio .= "</div>";

        return $radio;
    }
}
