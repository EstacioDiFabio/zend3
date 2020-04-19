<?php
namespace Auth\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Auth\Validator\RoleExistsValidator;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

/**
 * The form for collecting information about Role.
 */
class RoleForm extends Form
{
    private $scenario;

    private $entityManager;

    private $role;

    private $notEmpty    = 'Esse campo não pode ficar vazio.';
    private $tooShort    = 'Esse campo deve ter no mínimo 1 caracter.';
    private $tooLongName = 'Esse campo deve ter no máximo 128 caracteres.';
    private $tooLongDesc = 'Esse campo deve ter no máximo 1024 caracteres.';

    /**
     * Constructor.
     */
    public function __construct($scenario='create', $entityManager = null, $role = null)
    {
        $this->scenario = $scenario;
        $this->entityManager = $entityManager;
        $this->role = $role;

        // Define form name
        parent::__construct('role-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');

        $this->addElements();
        $this->addInputFilter();
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
            'attributes' => [
                'id' => 'name'
            ],
            'options' => [
                'label' => 'Nome da função',
            ],
        ]);

        // Add "description" field
        $this->add([
            'type'  => 'textarea',
            'name' => 'description',
            'attributes' => [
                'id' => 'description'
            ],
            'options' => [
                'label' => 'Descrição',
            ],
        ]);

        // Add "inherit_roles" field
        $this->add([
            'type'  => 'select',
            'name' => 'inherit_roles[]',
            'attributes' => [
                'id' => 'inherit_roles[]',
                'multiple' => 'multiple',
            ],
            'options' => [
                'label' => 'Opcionalmente permissões herdadas dessas funções.'
            ],
        ]);

        // Add the Submit button
        $this->add([
            'type'  => 'submit',
            'name' => 'submit',
            'attributes' => [
                'value' => 'Criar',
                'id' => 'submit',
            ],
        ]);

        // Add the CSRF field
        $this->add([
            'type' => 'csrf',
            'name' => 'csrf',
            'options' => [
                'csrf_options' => [
                'timeout' => 600
                ]
            ],
        ]);
    }

    /**
     * This method creates input filter (used for form filtering/validation).
     */
    private function addInputFilter()
    {
        // Create input filter
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
                            'max' => 128,
                            'messages' => [
                                StringLength::TOO_SHORT => $this->tooShort,
                                StringLength::TOO_LONG  => $this->tooLongName,
                            ]
                        ],

                    ],
                    [
                        'name' => NotEmpty::class,
                        'options' => [
                            'messages' => [
                                NotEmpty::IS_EMPTY => $this->notEmpty,
                            ],
                        ],
                    ],
                    [
                        'name' => RoleExistsValidator::class,
                        'options' => [
                            'entityManager' => $this->entityManager,
                            'role' => $this->role
                        ],
                    ],
                ],
            ]);

        // Add input for "description" field
        $inputFilter->add([
                'name'     => 'description',
                'required' => true,
                'filters'  => [
                    ['name' => 'StringTrim'],
                ],
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'min' => 1,
                            'max' => 1024,
                            'messages' => [
                                StringLength::TOO_SHORT => $this->tooShort,
                                StringLength::TOO_LONG  => $this->tooLongDesc,
                            ]
                        ],
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

        // Add input for "inherit_roles" field
        $inputFilter->add([
                'name'     => 'inherit_roles[]',
                'required' => false,
                'filters'  => [

                ],
                'validators' => [

                ],
            ]);
    }
}
