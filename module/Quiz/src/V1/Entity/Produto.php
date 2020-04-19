<?php

namespace Quiz\V1\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Produto
 *
 * @ORM\Table(name="produto")
 * @ORM\Entity
 */
class Produto
{
    // Produto status constants.
    const STATUS_ACTIVE       = 1; // Produto Ativo.
    const STATUS_RETIRED      = 0; // Produto Inativo.
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nome", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $nome;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", precision=0, scale=0, nullable=false, options={"default"="1"}, unique=false)
     */
    private $status = '1';

    /**
     * Returns possible statuses as array.
     * @return array
     */
    public static function getStatusList()
    {
        return [
            self::STATUS_ACTIVE => 'Ativo',
            self::STATUS_RETIRED => 'Inativo'
        ];
    }

    /**
     * Returns departament status as string.
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
     * Set nome.
     *
     * @param string $nome
     *
     * @return Produto
     */
    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get nome.
     *
     * @return string
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set status.
     *
     * @param bool $status
     *
     * @return Produto
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status.
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }
}
