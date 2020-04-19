<?php
namespace CMS\Form;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use CMS\V1\Entity\WorkGroup;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

/**
 * This form is used to collect workGroup's name and status.
 */
class WorkGroupForm extends Form
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
    private $tooShort = 'Esse campo deve ter no mínimo 1 caracter.';
    private $tooLong  = 'Esse campo deve ter no máximo 255 caracteres.';

    /**
     * Constructor.
     */
    public function __construct($scenario = 'create', $entityManager = null)
    {
        // Define form name
        parent::__construct('work-group-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');

        // Save parameters for internal use.
        $this->scenario = $scenario;
        $this->entityManager = $entityManager;

        $this->addElements();
        $this->addInputFilter();
    }

    private function getWorkGroup()
    {
        $workGroup = $this->entityManager->getRepository(WorkGroup::class)->findBy([], ['id'=>'ASC']);

        if(count($jobs) > 0){
            $workGroupData = array();
            foreach($workGroup as $group){

                $workGroupData[$group->getId()] = $group->getName();
            }
        }

        return $workGroupData;
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
                            'min' => 1,
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

        // Add input for "status" field
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