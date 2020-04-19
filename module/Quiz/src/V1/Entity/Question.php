<?php

namespace Quiz\V1\Entity;

use Doctrine\ORM\Mapping as ORM;
use Quiz\V1\Entity\QuestionForm;
/**
 * Question
 *
 * @ORM\Table(name="question")
 * @ORM\Entity
 */
class Question
{

    const STATUS_ACTIVE    = 1;
    const STATUS_RETIRED   = 0;
    const REQUIRED_ACTIVE  = 1;
    const REQUIRED_RETIRED = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Quiz\V1\Entity\QuestionForm
     *
     * @ORM\ManyToOne(targetEntity="Quiz\V1\Entity\QuestionForm")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_question_form", referencedColumnName="id", nullable=true)
     * })
     */
    private $idQuestionForm;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $name;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="type", type="integer", precision=0, scale=0, nullable=true, unique=false)
     */
    private $type;

    /**
     * @var int|null
     *
     * @ORM\Column(name="sequence", type="integer", precision=0, scale=0, nullable=true, options={"default"="1"}, unique=false)
     */
    private $sequence = '1';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="required", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $required;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="status", type="boolean", precision=0, scale=0, nullable=true, options={"default"="1"}, unique=false)
     */
    private $status = '1';

    /**
     * Returns possible statuses as array.
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => 'Habilitado',
            self::STATUS_RETIRED => 'Desabilitado'
        ];
    }

    /**
     * Returns possible required as array.
     * @return array
     */
    public static function getRequiredList()
    {
        return [
            self::REQUIRED_ACTIVE => 'ObrigatÃ³rio',
            self::REQUIRED_RETIRED => 'Opcional'
        ];
    }

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
     * Set idQuestionForm.
     *
     * @param \CMS\V1\Entity\QuestionForm $questionForm
     * @return Question
     */
    public function setIdQuestionForm(\Quiz\V1\Entity\QuestionForm $questionForm = null)
    {
        $this->idQuestionForm = $questionForm;

        return $this;
    }

    /**
     * Get questionForm.
     * @return \CMS\V1\Entity\QuestionForm
     */
    public function getIdQuestionForm()
    {
        return $this->idQuestionForm;
    }

    /**
     * Returns question_form as string.
     * @return string
     */
    public function getQuestionFormAsString()
    {
        return $this->getIdQuestionForm() != null ? $this->getIdQuestionForm()->getName() : "";
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Question
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type.
     *
     * @param int $type
     *
     * @return QuestionField
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    public function getTypeAsString()
    {
        switch ($this->type) {
            case 1:
                return 'Número'; break;
            case 2:
                return 'Texto'; break;
            case 3:
                return 'TextArea'; break;
            case 4:
                return 'Checkbox'; break;
            case 5:
                return 'Radio'; break;
            default:
                return 'Desconhecido'; break;
        }
    }

    /**
     * Set sequence.
     *
     * @param int|null $sequence
     *
     * @return Question
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

    /**
     * Set required.
     *
     * @param bool|null $required
     *
     * @return Question
     */
    public function setRequired($required = null)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Get required.
     *
     * @return bool|null
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Returns question_form required as string.
     * @return string
     */
    public function getRequiredAsString()
    {
        $list = self::getRequiredList();
        if (isset($list[$this->required]))
            return $list[$this->required];

        return 'Desconhecido';
    }

    /**
     * Set status.
     *
     * @param bool|null $status
     *
     * @return Question
     */
    public function setStatus($status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return bool|null
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Returns question_form status as string.
     * @return string
     */
    public function getStatusAsString()
    {
        $list = self::getStatusList();
        if (isset($list[$this->status]))
            return $list[$this->status];

        return 'Desconhecido';
    }

}