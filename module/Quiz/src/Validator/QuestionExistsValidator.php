<?php
namespace Quiz\Validator;

use Zend\Validator\AbstractValidator;
use Quiz\V1\Entity\QuestionForm;

/**
 * This validator class is designed for checking if there is an existing Question
 * with such an name.
 */
class QuestionExistsValidator extends AbstractValidator
{
    /**
     * Available validator options.
     * @var array
     */
    protected $options = array(
        'entityManager' => null,
        'question' => null
    );

    // Validation failure message IDs.
    const NOT_SCALAR  = 'notScalar';
    const QUESTION_EXISTS = 'exists';

    /**
     * Validation failure messages.
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_SCALAR            => "O Formulário de Perguntas deve ser um valor escalar(numérico).",
        self::QUESTION_EXISTS  => "Outro Formulário de Perguntas nesse formulário já existe."
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
            if(isset($options['question']))
                $this->options['question'] = $options['question'];
        }

        // Call the parent class constructor
        parent::__construct($options);
    }

    /**
     * Check if Question exists.
     */
    public function isValid($value)
    {
        if(!is_scalar($value)) {
            $this->error(self::NOT_SCALAR);
            return false;
        }

        // Get Doctrine entity manager.
        $entityManager = $this->options['entityManager'];

        $question = $entityManager->getRepository(Question::class)->findOneByName($value);

        if($this->options['question']==null) {
            $isValid = ($question==null);
        } else {
            if($this->options['question']->getName()!=$value && $user!=null)
                $isValid = false;
            else
                $isValid = true;
        }

        // If there were an error, set error message.
        if(!$isValid) {
            $this->error(self::QUESTION_EXISTS);
        }

        // Return validation result.
        return $isValid;
    }
}

