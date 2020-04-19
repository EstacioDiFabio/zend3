<?php

namespace Quiz\V1\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Quiz\V1\Entity\Question;

/**
 * QuestionField
 *
 * @ORM\Table(name="question_field", indexes={@ORM\Index(name="fk_id_question_field_idx", columns={"id_question"})})
 * @ORM\Entity
 */
class QuestionField
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
     * @var \Quiz\V1\Entity\Question
     *
     * @ORM\OneToOne(targetEntity="Question")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_question", referencedColumnName="id", nullable=true)
     * })
     */
    private $idQuestion;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $label;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sequence", type="integer", precision=0, scale=0, nullable=true, options={"default"="1"}, unique=false)
     */
    private $sequence = '1';

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
     * Set idQuestion.
     *
     * @param \Quiz\V1\Entity\Question|null $idQuestion
     *
     * @return QuestionField
     */
    public function setIdQuestion(\Quiz\V1\Entity\Question $idQuestion = null)
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
     * Returns question_form as string.
     * @return string
     */
    public function getQuestionAsString()
    {
        return $this->getIdQuestion() != null ? $this->getIdQuestion()->getName() : "";
    }

    /**
     * Set label.
     *
     * @param string $label
     *
     * @return QuestionField
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label.
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set sequence.
     *
     * @param int|null $sequence
     *
     * @return QuestionField
     */
    public function setSequence($sequence = null)
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * Get sequence.
     *
     * @return int|null
     */
    public function getSequence()
    {
        return $this->sequence;
    }

}