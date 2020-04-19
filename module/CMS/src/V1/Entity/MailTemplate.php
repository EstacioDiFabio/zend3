<?php

namespace CMS\V1\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MailTemplate
 *
 * @ORM\Table(name="mail_template")
 * @ORM\Entity
 */
class MailTemplate
{

    // MailTemplate status constants.
    const STATUS_ACTIVE       = 1; // Active mail_template.
    const STATUS_RETIRED      = 0; // Retired mail_template.

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $name;

    /**
     * @var text
     * @ORM\Column(name="header", type="text", precision=0, scale=0, nullable=true, unique=false)
     */
    private $header;

    /**
     * @var text
     * @ORM\Column(name="content", type="text", precision=0, scale=0, nullable=true, unique=false)
     */
    private $content;

    /**
     * @var text
     * @ORM\Column(name="footer", type="text", precision=0, scale=0, nullable=true, unique=false)
     */
    private $footer;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, precision=0, scale=0, nullable=true, unique=false)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="identifier", type="string", length=255, precision=0, scale=0, nullable=false, unique=false)
     */
    private $identifier;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     */
    private $status;


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
     * Returns mail_template status as string.
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return MailTemplate
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set header
     *
     * @param string $header
     *
     * @return MailTemplate
     */
    public function setHeader($header)
    {
        $this->header = $header;

        return $this;
    }

    /**
     * Get header
     *
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return MailTemplate
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set footer
     *
     * @param string $footer
     *
     * @return MailTemplate
     */
    public function setFooter($footer)
    {
        $this->footer = $footer;

        return $this;
    }

    /**
     * Get footer
     *
     * @return string
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return MailTemplate
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return boolean
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set identifier
     *
     * @param string $identifier
     *
     * @return MailTemplate
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * Get identifier
     *
     * @return boolean
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return MailTemplate
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }
}