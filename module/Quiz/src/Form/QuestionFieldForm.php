<?php

namespace Quiz\Form;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Quiz\V1\Entity\Question;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

/**
 * This form is used to collect question-field's
 * id_question, label, type, order, depends_field and depends_field_condition.
 */

class QuestionFieldForm extends Form
{
    /**
     * Scenario ('create' or 'update').
     * @var string
     */
    private $scenario;

    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager = null;

    private $questions = [];
    private $notEmpty  = 'Esse campo não pode ficar vazio.';
    private $tooShort  = 'Esse campo deve ter no mínimo 3 caracteres.';
    private $tooLong   = 'Esse campo deve ter no máximo 500 caracteres.';
    private $tooLongId = 'Esse campo deve ter no máximo 11 caracteres.';

    /**
     * Constructor.
     */
    public function __construct($scenario = 'create', $entityManager = null)
    {
        // Define form name
        parent::__construct('question-field-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');

        // Save parameters for internal use.
        $this->scenario = $scenario;
        $this->entityManager = $entityManager;

        $questions = $this->entityManager->getRepository(Question::class)
                                         ->findBy([], ['id'=>'ASC']);

        if($questions) {
            foreach($questions as $question){
                $name = $question->getIdQuestionForm()->getName()." - ".$question->getName();
                $this->questions[$question->getId()] = $name;
            }
        }

        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements()
    {

        // Add "id_question" field
        $this->add([
            'type'  => 'select',
            'name' => 'id_question',
            'options' => [
                'label' => 'Pergunta',
                'empty_option' => '',
                'value_options' => $this->questions
            ],
        ]);
        // Add "label" field
        $this->add([
            'type'  => 'text',
            'name' => 'label',
            'options' => [
                'label' => 'Label',
            ],
        ]);
        // Add "number" field
        $this->add([
            'type'  => 'number',
            'name' => 'sequence',
            'options' => [
                'label' => 'Sequência de exibição',
            ],
        ]);

        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Criar'
            ],
        ]);
    }

    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter()
    {
        // Create main input filter
        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);
        // Add "id_question_form" filter
        $inputFilter->add([
            'name'     => 'id_question',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => $this->notEmpty,
                        ],
                    ],
                ],
                [
                    'name' => 'InArray',
                    'options' => [
                        'haystack' => $this->questions
                    ]
                ]
            ],
        ]);

        // Add "label" filter
        $inputFilter->add([
            'name'     => 'label',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => $this->notEmpty,
                        ],
                    ],
                ],
                [
                    'name'    => StringLength::class,
                    'options' => [
                        'min' => 3,
                        'max' => 500,
                        'messages' => [
                            StringLength::TOO_SHORT => $this->tooShort,
                            StringLength::TOO_LONG  => $this->tooLong,
                        ]
                    ],
                ],
            ],
        ]);

        // Add "sequence" filter
        $inputFilter->add([
            'name'     => 'sequence',
            'required' => false,
            'filters'  => [
                ['name' => 'StringTrim'],
            ]
        ]);

    }

}