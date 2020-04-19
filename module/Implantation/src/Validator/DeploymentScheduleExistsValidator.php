<?php
namespace Implantation\Validator;

use Zend\Validator\AbstractValidator;
use Implantation\V1\Entity\DeploymentSchedule;

/**
 * This validator class is designed for checking if there is an existing DeploymentSchedule
 * with such an name.
 */
class DeploymentScheduleExistsValidator extends AbstractValidator
{
    /**
     * Available validator options.
     * @var array
     */
    protected $options = array(
        'entityManager' => null,
        'deployment_schedule' => null
    );

    // Validation failure message IDs.
    const NOT_SCALAR  = 'notScalar';
    const DEPLOYMENT_SCHEDULE_EXISTS = 'exists';

    /**
     * Validation failure messages.
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_SCALAR  => "O Agendamento deve ser um valor escalar(numérico).",
        self::DEPLOYMENT_SCHEDULE_EXISTS  => "Outro Agendamento nesse horário já existe."
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
            if(isset($options['deployment_schedule']))
                $this->options['deployment_schedule'] = $options['deployment_schedule'];
        }

        // Call the parent class constructor
        parent::__construct($options);
    }

    /**
     * Check if DeploymentSchedule exists.
     */
    public function isValid($value)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        // Get Doctrine entity manager.
        $entityManager = $this->options['entityManager'];

        $deploymentSchedule = $entityManager->getRepository(DeploymentSchedule::class)->findOneByName($value);

        if($this->options['deployment_schedule']==null) {
            $isValid = ($deploymentSchedule==null);
        } else {
            if($this->options['deployment_schedule']->getName()!=$value && $user!=null)
                $isValid = false;
            else
                $isValid = true;
        }

        // If there were an error, set error message.
        if(!$isValid) {
            $this->error(self::DEPLOYMENT_SCHEDULE_EXISTS);
        }

        // Return validation result.
        return $isValid;
    }
}

