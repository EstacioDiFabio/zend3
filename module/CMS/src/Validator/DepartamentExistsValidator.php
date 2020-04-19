<?php
namespace CMS\Validator;

use Zend\Validator\AbstractValidator;
use CMS\V1\Entity\Departament;

/**
 * This validator class is designed for checking if there is an existing departament
 * with such an name.
 */
class DepartamentExistsValidator extends AbstractValidator
{
    /**
     * Available validator options.
     * @var array
     */
    protected $options = array(
        'entityManager' => null,
        'departament' => null
    );

    // Validation failure message IDs.
    const NOT_SCALAR  = 'notScalar';
    const DEPARTAMENT_EXISTS = 'jobExists';

    /**
     * Validation failure messages.
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_SCALAR  => "O Setor deve ser um valor escalar(numérico).",
        self::DEPARTAMENT_EXISTS  => "Outro Setor com esse nome já existe."
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
            if(isset($options['departament']))
                $this->options['departament'] = $options['departament'];
        }

        // Call the parent class constructor
        parent::__construct($options);
    }

    /**
     * Check if departament exists.
     */
    public function isValid($value)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        // Get Doctrine entity manager.
        $entityManager = $this->options['entityManager'];

        $departament = $entityManager->getRepository(Departament::class)->findOneByName($value);

        if($this->options['departament']==null) {
            $isValid = ($departament==null);
        } else {
            if($this->options['departament']->getName()!=$value && $user!=null)
                $isValid = false;
            else
                $isValid = true;
        }

        // If there were an error, set error message.
        if(!$isValid) {
            $this->error(self::DEPARTAMENT_EXISTS);
        }

        // Return validation result.
        return $isValid;
    }
}

