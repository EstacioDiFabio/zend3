<?php

namespace Quiz\V1\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * QuestionForm
 *
 * @ORM\Table(name="question_form")
 * @ORM\Entity
 */
class QuestionForm
{
    const STATUS_ACTIVE  = 1;
    const STATUS_RETIRED = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Quiz\V1\Entity\Produto
     *
     * @ORM\OneToOne(targetEntity="Produto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_produto", referencedColumnName="id", nullable=true)
     * })
     */
    private $idProduto;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="local", type="string", length=255, nullable=false)
     */
    private $local;

    /**
     * @var int
     *
     * @ORM\Column(name="sequence", type="integer", nullable=true, options={"default"="999"})
     */
    private $sequence = '999';

    /**
     * @var bool|null
     *
     * @ORM\Column(name="status", type="boolean", nullable=true, options={"default"="1"})
     */
    private $status = '1';

    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE  => 'Ativo',
            self::STATUS_RETIRED => 'Inativo'
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
     * Set idProduto.
     *
     * @param \Quiz\V1\Entity\Produto|null $idProduto
     *
     * @return Produto
     */
    public function setIdProduto(\Quiz\V1\Entity\Produto $idProduto = null)
    {
        $this->idProduto = $idProduto;

        return $this;
    }

    /**
     * Get idProduto.
     *
     * @return \Quiz\V1\Entity\Produto|null
     */
    public function getIdProduto()
    {
        return $this->idProduto;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return QuestionForm
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
     * Set local.
     *
     * @param string $local
     *
     * @return QuestionForm
     */
    public function setLocal($local)
    {
        $this->local = $local;

        return $this;
    }

    /**
     * Get local.
     *
     * @return string
     */
    public function getLocal()
    {
        return $this->local;
    }

    /**
     * Set sequence.
     *
     * @param int $sequence
     *
     * @return QuestionForm
     */
    public function setSequence($sequence = null)
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * Get sequence.
     *
     * @return int
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Set status.
     *
     * @param bool|null $status
     *
     * @return QuestionForm
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

    public function getStatusToggle()
    {
        $id = $this->getId();
        $status = $this->getStatusAsString();
        $status_id = $this->getStatus();

        $toggle = "<div class='status_string_".$id."'>";
            $toggle .= $status;
        $toggle .= "</div>";

        $toggle .= "<div class='material-switch'>";
            $toggle .= "<input class='status-index'
                               type='checkbox'
                               name='status'
                               value='".$status_id."'
                               data-status='".$status."'
                               data-id='".$id."'
                               id='status_".$id."'>";
        $toggle .= "<label for='status_".$id."'></label>";
        $toggle .= "</div>";

        return $toggle;
    }

}