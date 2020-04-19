<?php

namespace Quiz\V1\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * QuestionFieldFilledValue
 *
 * @ORM\Table(name="question_field_filled_value", indexes={@ORM\Index(name="fk_id_question_fiedl_idx", columns={"id_question_form_fiedl"})})
 * @ORM\Entity
 */
class QuestionFieldFilledValue
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_field", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $idField;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_question", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $idQuestion;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_deployment_schedule", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $idDeploymentSchedule;

    /**
     * @var string|null
     *
     * @ORM\Column(name="value", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $value;

    /**
     * @var string|null
     *
     * @ORM\Column(name="value_text", type="text", length=65535, precision=0, scale=0, nullable=true, unique=false)
     */
    private $valueText;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set value.
     *
     * @param string|null $value
     *
     * @return QuestionFieldFilledValue
     */
    public function setValue($value = null)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Set idField.
     *
     * @param $idField
     *
     * @return QuestionFieldFilledValue
     */
    public function setIdField($idField = null)
    {
        $this->idField = $idField;

        return $this;
    }

    /**
     * Get idField.
     *
     * @return \Quiz\V1\Entity\Question|null
     */
    public function getIdField()
    {
        return $this->idField;
    }

    /**
     * Set idQuestion.
     *
     * @param $idQuestion
     *
     * @return QuestionFieldFilledValue
     */
    public function setIdQuestion($idQuestion = null)
    {
        $this->idQuestion = $idQuestion;

        return $this;
    }

    /**
     * Get idQuestion.
     *
     * @return \Quiz\V1\Entity\Question|null
     */
    public function getIdQuestion()
    {
        return $this->idQuestion;
    }

    /**
     * Set idDeploymentSchedule.
     *
     * @param $idDeploymentSchedule
     *
     * @return QuestionFieldFilledValue
     */
    public function setidDeploymentSchedule($idDeploymentSchedule = null)
    {
        $this->idDeploymentSchedule = $idDeploymentSchedule;

        return $this;
    }

    /**
     * Get idDeploymentSchedule.
     *
     * @return idDeploymentSchedule
     */
    public function getidDeploymentSchedule()
    {
        return $this->idDeploymentSchedule;
    }

    /**
     * Get value.
     *
     * @return string|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set valueText.
     *
     * @param string|null $valueText
     *
     * @return QuestionFieldFilledValue
     */
    public function setValueText($valueText = null)
    {
        $this->valueText = $valueText;

        return $this;
    }

    /**
     * Get valueText.
     *
     * @return string|null
     */
    public function getValueText()
    {
        return $this->valueText;
    }


}