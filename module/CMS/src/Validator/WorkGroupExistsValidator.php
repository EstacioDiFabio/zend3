<?php
namespace CMS\Validator;

use Zend\Validator\AbstractValidator;
use CMS\V1\Entity\WorkGroup;

/**
 * This validator class is designed for checking if there is an existing workGroup
 * with such an name.
 */
class WorkGroupExistsValidator extends AbstractValidator
{
    /**
     * Available validator options.
     * @var array
     */
    protected $options = array(
        'entityManager' => null,
        'workGroup' => null
    );

    // Validation failure message IDs.
    const NOT_SCALAR  = 'notScalar';
    const WORK_GROUP_EXISTS = 'WorkGroupExists';

    /**
     * Validation failure messages.
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_SCALAR  => "O Grupo deve ser um valor escalar(numérico).",
        self::WORK_GROUP_EXISTS  => "Outro grupo com esse nome já existe."
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
            if(isset($options['workGroup']))
                $this->options['gworkGroup'] = $options['workGroup'];
        }

        // Call the parent class constructor
        parent::__construct($options);
    }

    /**
     * Check if WorkGroup exists.
     */
    public function isValid($value)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        // Get Doctrine entity manager.
        $entityManager = $this->options['entityManager'];

        $workGroup = $entityManager->getRepository(WorkGroup::class)->findOneByName($value);

        if($this->options['workGroup']==null) {
            $isValid = ($group==null);
        } else {
            if($this->options['workGroup']->getName()!=$value && $user!=null)
                $isValid = false;
            else
                $isValid = true;
        }

        // If there were an error, set error message.
        if(!$isValid) {
            $this->error(self::WORK_GROUP_EXISTS);
        }

        // Return validation result.
        return $isValid;
    }
}

