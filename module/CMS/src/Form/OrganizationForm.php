<?php
namespace CMS\Form;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use CMS\Validator\OrganizationExistsValidator;
use CMS\V1\Entity\Organization;
use CMS\V1\Entity\OrganizationOfficeHour;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

/**
 * This form is used to collect organization's name and status.
 */
class OrganizationForm extends Form
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

    private $day = [
        0 => 'sunday',
        1 => 'monday',
        2 => 'tuesday',
        3 => 'wednesday',
        4 => 'thursday',
        5 => 'friday',
        6 => 'saturday'
    ];

    private $dia = [
        0 => 'Domingo',
        1 => 'Segunda',
        2 => 'Terça',
        3 => 'Quarta',
        4 => 'Quinta',
        5 => 'Sexta',
        6 => 'Sábado'
    ];

    private $notEmpty = 'Esse campo não pode ficar vazio.';
    private $tooShort = 'Esse campo deve ter no mínimo 1 caracter.';
    private $tooLong  = 'Esse campo deve ter no máximo 255 caracteres.';

    /**
     * Constructor.
     */
    public function __construct($scenario = 'create', $entityManager = null)
    {
        // Define form name
        parent::__construct('organization-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');

        // Save parameters for internal use.
        $this->scenario = $scenario;
        $this->entityManager = $entityManager;

        $this->addElements();
        $this->addInputFilter();
    }

    private function getOrganizations()
    {
        $organizations = $this->entityManager->getRepository(Organization::class)->findBy([], ['id'=>'ASC']);

        if(count($organizations) > 0){
            $organizationData = array();
            foreach($organizations as $org){

                $organizationData[$org->getId()] = $org->getName();
            }
        }

        return $organizationData;
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements()
    {
        // Add "name" field
        $this->add([
            'type'  => 'text',
            'name' => 'name',
            'options' => [
                'label' => 'Nome',
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

        $this->add([
            'type'  => 'text',
            'name' => 'day',
            'options' => [
                'label' => 'Dia',
            ],
        ]);

        for ($i=0; $i <= 6; $i++) {

            $this->add([
                'type'  => 'checkbox',
                'name' => $this->day[$i].'[status_hour]',
                'options' => [
                    'label' => $this->dia[$i],
                    'use_hidden_element' => false,
                ]
            ]);

            $this->add([
                'type'  => 'time',
                'name' => $this->day[$i].'[morning_start_time]',
                'label' => ' ',
            ]);

            $this->add([
                'type'  => 'time',
                'name' => $this->day[$i].'[morning_closing_time]',

            ]);

            $this->add([
                'type'  => 'time',
                'name' => $this->day[$i].'[afternoon_start_time]',

            ]);

            $this->add([
                'type'  => 'time',
                'name' => $this->day[$i].'[afternoon_closing_time]',

            ]);

        }

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

        // Add input for "name" field
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
            ],
        ]);

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

        // Add input for "day" field
        $inputFilter->add([
            'name'     => 'day',
            'required' => false,
            'filters'  => [
                ['name' => 'ToInt'],
            ],
            'validators' => [
                ['name' => 'InArray',
                 'options' => [
                    'haystack' => [
                        'domingo', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'
                    ]
                 ]
                ]
            ],
        ]);

        for ($i=0; $i <= 6; $i++) {

            $inputFilter->add([
                'name'     => $this->day[$i].'[morning_start_time]',
                'required' => true,
                'filters' => [
                    [
                        'name' => 'DateTimeFormatter',
                        'options' => [
                            'format' => 'H:i:s',
                        ],
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'date',
                        'options' => ['format' => "H:i:s"]
                    ]
                ]
            ]);

            $inputFilter->add([
                'name'     => $this->day[$i].'[morning_closing_time]',
                'required' => true,
                'filters' => [
                    [
                        'name' => 'DateTimeFormatter',
                        'options' => [
                            'format' => 'H:i:s',
                        ],
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'date',
                        'options' => ['format' => "H:i:s"]
                    ]
                ]
            ]);

            $inputFilter->add([
                'name'     => $this->day[$i].'[afternoon_start_time]',
                'required' => true,
                'filters' => [
                    [
                        'name' => 'DateTimeFormatter',
                        'options' => [
                            'format' => 'H:i:s',
                        ],
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'date',
                        'options' => ['format' => "H:i:s"]
                    ]
                ]
            ]);

            $inputFilter->add([
                'name'     => $this->day[$i].'[afternoon_closing_time]',
                'required' => true,
                'filters' => [
                    [
                        'name' => 'DateTimeFormatter',
                        'options' => [
                            'format' => 'H:i:s',
                        ],
                    ]
                ],
                'validators' => [
                    [
                        'name' => 'date',
                        'options' => ['format' => "H:i:s"]
                    ]
                ]
            ]);
        }

    }
}