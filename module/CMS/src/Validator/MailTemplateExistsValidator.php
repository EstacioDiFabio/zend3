<?php
namespace CMS\Validator;

use Zend\Validator\AbstractValidator;
use CMS\V1\Entity\MailTemplate;

/**
 * This validator class is designed for checking if there is an existing mail_template
 * with such an name.
 */
class MailTemplateExistsValidator extends AbstractValidator
{
    /**
     * Available validator options.
     * @var array
     */
    protected $options = array(
        'entityManager' => null,
        'mail_template' => null
    );

    // Validation failure message IDs.
    const NOT_SCALAR  = 'notScalar';
    const MAIL_TEMPLATE_EXISTS = 'mailTemplateExists';

    /**
     * Validation failure messages.
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_SCALAR  => "O Template de E-mail deve ser um valor escalar(numérico).",
        self::MAIL_TEMPLATE_EXISTS  => "Um template de e-mail com esse nome já existe."
    );

    /**
     * Constructor.
     */
    public function __construct($options = null)
    {
        // Set filter options (if provided).
        if(is_array($options)) {
            if(isset($options['entityManager']))
                $this->options['entityManager'] = $options['entityManager'];
            if(isset($options['mail_template']))
                $this->options['mail_template'] = $options['mail_template'];
        }

        // Call the parent class constructor
        parent::__construct($options);
    }

    /**
     * Check if mail_template exists.
     */
    public function isValid($value)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        // Get Doctrine entity manager.
        $entityManager = $this->options['entityManager'];

        $mail_template = $entityManager->getRepository(MailTemplate::class)->findOneByName($value);

        if($this->options['mail_template']==null) {
            $isValid = ($mail_template==null);
        } else {
            if($this->options['mail_template']->getName()!=$value && $user!=null)
                $isValid = false;
            else
                $isValid = true;
        }

        // If there were an error, set error message.
        if(!$isValid) {
            $this->error(self::MAIL_TEMPLATE_EXISTS);
        }

        // Return validation result.
        return $isValid;
    }
}

