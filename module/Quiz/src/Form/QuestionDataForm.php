<?php

namespace Quiz\Form;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Quiz\V1\Entity\QuestionForm;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

/**
 * This form is used to collect question's
 * name, form, order, required, status.and question_bond.
 */

class QuestionDataForm extends Form
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

    private $forms      = [];
    private $types      = [];
    private $notEmpty   = 'Esse campo não pode ficar vazio.';
    private $tooShort   = 'Esse campo deve ter no mínimo 3 caracteres.';
    private $tooShortId = 'Esse campo deve ter no mínimo 1 caracter.';
    private $tooLong    = 'Esse campo deve ter no máximo 255 caracteres.';

    /**
     * Constructor.
     */
    public function __construct($scenario = 'create', $entityManager = null)
    {
        // Define form name
        parent::__construct('question-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');

        // Save parameters for internal use.
        $this->scenario = $scenario;
        $this->entityManager = $entityManager;

        $questionsForm = $this->entityManager->getRepository(QuestionForm::class)
                                             ->findBy([], ['name'=>'ASC']);

        if ($questionsForm) {
            foreach ($questionsForm as $questionForm) {
                $this->forms[$questionForm->getId()] = $questionForm->getName();
            }
        }

        $this->types = [
            0 => '',
            1 => 'Número',
            2 => 'Texto',
            3 => 'TextArea',
            4 => 'Checkbox',
            5 => 'Radio',
        ];

        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements()
    {

        // Add "id_question_form" field
        $this->add([
            'type'  => 'select',
            'name' => 'id_question_form',
            'options' => [
                'label' => 'Formulário',
                'value_options' => $this->forms,
                'empty_option' => '',

            ],
        ]);

        // Add "name" field
        $this->add([
            'type'  => 'text',
            'name' => 'name',
            'options' => [
                'label' => 'Nome da Pergunta',
            ],
        ]);
        // Add "type" field
        $this->add([
            'type'  => 'select',
            'name' => 'type',
            'options' => [
                'label' => 'Tipo de Campo',
                'value_options' => $this->types
            ],
        ]);


        // Add "sequence" field
        $this->add([
            'type'  => 'number',
            'name' => 'sequence',
            'options' => [
                'label' => 'Sequência de exibição',
            ],
        ]);

        // Add "required" field
        $this->add([
            'type'  => 'checkbox',
            'name' => 'required',
            'id' => 'required',
            'options' => [
                'label' => ' ',
                'use_hidden_element' => false,
            ],
        ]);
        // Add "status" field
        $this->add([
            'type'  => 'checkbox',
            'name' => 'status',
            'id' => 'status',
            'options' => [
                'label' => ' ',
                'use_hidden_element' => false,
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
            'name'     => 'id_question_form',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => 'StringLength',
                    'options' => [
                        'min' => 1,
                        'max' => 11
                    ],
                ],
            ],
        ]);

        // Add "name" filter
        $inputFilter->add([
            'name'     => 'name',
            'required' => true,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => StringLength::class,
                    'options' => [
                        'min' => 3,
                        'max' => 255,
                        'messages' => [
                            StringLength::TOO_SHORT => $this->tooShort,
                            StringLength::TOO_LONG  => $this->tooLong,
                        ]
                    ]
                ],
                [
                    'name' => NotEmpty::class,
                    'options' => [
                        'messages' => [
                            NotEmpty::IS_EMPTY => $this->notEmpty,
                        ],
                    ],
                ],
            ],
        ]);
        // Add "type" filter
        $inputFilter->add([
            'name'     => 'type',
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

            ],
        ]);
        // Add "sequence" filter
        $inputFilter->add([
            'name'     => 'sequence',
            'required' => false,
            'filters'  => [
                ['name' => 'StringTrim'],
            ],
            'validators' => [
                [
                    'name'    => StringLength::class,
                    'options' => [
                        'min' => 1,
                        'max' => 11,
                        'messages' => [
                            StringLength::TOO_SHORT => $this->tooShortId,
                            StringLength::TOO_LONG  => $this->tooLong,
                        ]
                    ]
                ],
            ],
        ]);
        // Add "required" filter
        $inputFilter->add([
            'name'     => 'required',
            'required' => false,
            'filters'  => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                [
                    'name' => 'InArray',
                    'options' => [
                        'haystack' => [1, 0]
                    ]
                ]
            ],
        ]);
        // Add "status" filter
        $inputFilter->add([
            'name'     => 'status',
            'required' => false,
            'filters'  => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                [
                    'name' => 'InArray',
                    'options' => [
                        'haystack' => [1, 0]
                    ]
                ]
            ],
        ]);

    }

}