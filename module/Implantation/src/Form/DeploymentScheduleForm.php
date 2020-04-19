<?php
namespace Implantation\Form;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Zend\Filter\DateTimeFormatter;
use Zend\Validator\NotEmpty;

/**
 * This form is used to collect departament's name and status.
 */
class DeploymentScheduleForm extends Form
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

    private $notEmpty = 'Esse campo não pode ficar vazio.';
    /**
     * Constructor.
     */
    public function __construct($scenario = 'create', $entityManager = null)
    {
        // Define form name
        parent::__construct('deployment-schedule-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');

        // Save parameters for internal use.
        $this->scenario = $scenario;
        $this->entityManager = $entityManager;

        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements()
    {


        // Add "id_client" field
        $this->add([
            'type'  => 'hidden',
            'name' => 'id_client',
            'options' => [
                'label' => 'Cliente ID',
            ],
        ]);

        // Add "id_client" field
        $this->add([
            'type'  => 'text',
            'name' => 'client_name',
            'options' => [
                'label' => 'Cliente',
            ],
        ]);

        if ($this->scenario == 'update') {

            // Add "date" field
            $this->add([
                'type'  => 'date',
                'name' => 'date',
                'options' => [
                    'label' => 'Data',
                    'autocomplete' => 'disabled'
                ],
            ]);

            // Add "time" field
            $this->add([
                'type'  => 'time',
                'name' => 'time',
                'options' => [
                    'label' => 'Horário',
                    'autocomplete' => 'disabled'
                ],
            ]);

            // Add "time" field
            $this->add([
                'type'  => 'text',
                'name' => 'time_end',
                'options' => [
                    'label' => 'Horário de Término',
                ],
            ]);

        } else {

            // Add "date" field
            $this->add([
                'type'  => 'text',
                'name' => 'date',
                'options' => [
                    'label' => 'Data',
                    'autocomplete' => 'disabled'
                ],
            ]);

            // Add "time" field
            $this->add([
                'type'  => 'text',
                'name' => 'time',
                'options' => [
                    'label' => 'Horário',
                    'autocomplete' => 'disabled'
                ],
            ]);

            // Add "time" field
            $this->add([
                'type'  => 'text',
                'name' => 'time_end',
                'options' => [
                    'label' => 'Horário Final',
                    'autocomplete' => 'disabled'
                ],
            ]);
        }

        // Add "status" field
        $this->add([
            'type'  => 'select',
            'name' => 'status',
            'options' => [
                'label' => 'Status',
                'value_options' => [
                    1 => 'Agendado',
                    0 => 'Finalizado',
                ]
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

        // Add input for "id_client" field
        $inputFilter->add([
                'name'     => 'id_client',
                'required' => true,
            ]);

        // Add input for "client_name" field
        $inputFilter->add([
                'name'     => 'client_name',
                'required' => true,
            ]);

        if ($this->scenario == 'client') {
            // Add input for "date" field
            $inputFilter->add([
                    'name'     => 'date',
                    'required' => false,
                    'filters' => [],
                    'validators' => [
                        [
                        'name' => NotEmpty::class,
                        'options' => [
                            'messages' => [
                                NotEmpty::IS_EMPTY => $this->notEmpty,
                            ],
                        ],
                    ],
                    ]
                ]);

            // Add input for "time" field
            $inputFilter->add([
                    'name'     => 'time',
                    'required' => false,
                    'filters' => [],
                    'validators' => [
                        [
                            'name' => NotEmpty::class,
                            'options' => [
                                'messages' => [
                                    NotEmpty::IS_EMPTY => $this->notEmpty,
                                ],
                            ],
                        ],
                    ]
                ]);
        } else {

            // Add input for "date" field
            $inputFilter->add([
                    'name'     => 'date',
                    'required' => true,
                    'filters' => [],
                    'validators' => [
                        [
                        'name' => NotEmpty::class,
                        'options' => [
                            'messages' => [
                                NotEmpty::IS_EMPTY => $this->notEmpty,
                            ],
                        ],
                    ],
                    ]
                ]);

            // Add input for "time" field
            $inputFilter->add([
                    'name'     => 'time',
                    'required' => true,
                    'filters' => [],
                    'validators' => [
                        [
                            'name' => NotEmpty::class,
                            'options' => [
                                'messages' => [
                                    NotEmpty::IS_EMPTY => $this->notEmpty,
                                ],
                            ],
                        ],
                    ]
                ]);
        }

        if ($this->scenario == 'update') {
            // Add input for "time_end" field
            $inputFilter->add([
                    'name'     => 'time_end',
                    'required' => true,
                    'filters' => [],
                    'validators' => [
                        [
                            'name' => NotEmpty::class,
                            'options' => [
                                'messages' => [
                                    NotEmpty::IS_EMPTY => $this->notEmpty,
                                ],
                            ],
                        ],
                    ]
                ]);
        }
        // Add input for "status" field
        $inputFilter->add([
            'name'     => 'status',
            'required' => false,
            'filters'  => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name'=>'InArray', 'options'=>['haystack'=>[1, 0]]]
            ],
        ]);

    }

}