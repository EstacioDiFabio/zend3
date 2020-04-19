<?php
namespace CMS\Validator;

use Zend\Validator\AbstractValidator;
use CMS\V1\Entity\Organization;

/**
 * This validator class is designed for checking if there is an existing organization
 * with such an name.
 */
class OrganizationExistsValidator extends AbstractValidator
{
    /**
     * Available validator options.
     * @var array
     */
    protected $options = array(
        'entityManager' => null,
        'organization' => null
    );

    // Validation failure message IDs.
    const NOT_SCALAR  = 'notScalar';
    const ORGANIZATION_EXISTS = 'organizationExists';

    /**
     * Validation failure messages.
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_SCALAR  => "A unidade deve ser um valor escalar(numérico).",
        self::ORGANIZATION_EXISTS  => "Outra unidade com esse nome já existe."
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
            if(isset($options['organization']))
                $this->options['organization'] = $options['organization'];
        }

        // Call the parent class constructor
        parent::__construct($options);
    }

    /**
     * Check if job exists.
     */
    public function isValid($value)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        // Get Doctrine entity manager.
        $entityManager = $this->options['entityManager'];

        $organization = $entityManager->getRepository(Organization::class)->findOneByName($value);

        if($this->options['organization']==null) {
            $isValid = ($organization==null);
        } else {
            if($this->options['organization']->getName()!=$value && $user!=null)
                $isValid = false;
            else
                $isValid = true;
        }

        // If there were an error, set error message.
        if(!$isValid) {
            $this->error(self::ORGANIZATION_EXISTS);
        }

        // Return validation result.
        return $isValid;
    }
}

