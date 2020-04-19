<?php
namespace CMS\Validator;

use Zend\Validator\AbstractValidator;
use CMS\V1\Entity\Job;

/**
 * This validator class is designed for checking if there is an existing job
 * with such an name.
 */
class JobExistsValidator extends AbstractValidator
{
    /**
     * Available validator options.
     * @var array
     */
    protected $options = array(
        'entityManager' => null,
        'job' => null
    );

    // Validation failure message IDs.
    const NOT_SCALAR  = 'notScalar';
    const JOB_EXISTS = 'jobExists';

    /**
     * Validation failure messages.
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_SCALAR  => "O Cargo deve ser um valor escalar(numérico).",
        self::JOB_EXISTS  => "Outro cargo com esse nome já existe."
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
            if(isset($options['job']))
                $this->options['job'] = $options['job'];
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

        $job = $entityManager->getRepository(Job::class)->findOneByName($value);

        if($this->options['job']==null) {
            $isValid = ($job==null);
        } else {
            if($this->options['job']->getName()!=$value && $user!=null)
                $isValid = false;
            else
                $isValid = true;
        }

        // If there were an error, set error message.
        if(!$isValid) {
            $this->error(self::JOB_EXISTS);
        }

        // Return validation result.
        return $isValid;
    }
}

