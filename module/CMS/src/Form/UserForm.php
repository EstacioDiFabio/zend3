<?php
namespace CMS\Form;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\ArrayInput;
use CMS\Validator\UserExistsValidator;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;
use Zend\Validator\EmailAddress;
use Zend\Validator\Identical;
use Zend\Validator\GreaterThan;

/**
 * This form is used to collect user's email, full name, password and status. The form
 * can work in two scenarios - 'create' and 'update'. In 'create' scenario, user
 * enters password, in 'update' scenario he/she doesn't enter password.
 */
class UserForm extends Form
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

    /**
     * Current user.
     * @var User\Entity\User
     */
    private $user = null;

    private $notEmpty   = 'Esse campo não pode ficar vazio.';
    private $tooShort   = 'Esse campo deve ter no mínimo 1 caracter.';
    private $tooLong    = 'Esse campo deve ter no máximo 255 caracteres.';
    private $mailNoLong = 'Seu e-mail não pode ser maior que 128 caracteres';
    private $mailFormatInvalid = 'A entrada não é um endereço de email válido. Use o formato básico usuario@csec.com.br';
    private $tooShortPw = "Sua senha deve ter no mínimo 6 caracteres";
    private $tooLongPw  = "Sua senha não pode ser maior que 64 caracteres";
    private $notIdentical = 'Os dois tokens dados não correspondem.';
    private $notGreater = "A entrada não é maior que '% min%'";

    /**
     * Constructor.
     */
    public function __construct($scenario = 'create', $entityManager = null, $user = null)
    {
        // Define form name
        parent::__construct('user-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');

        // Save parameters for internal use.
        $this->scenario = $scenario;
        $this->entityManager = $entityManager;
        $this->user = $user;

        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * This method adds elements to form (input fields and submit button).
     */
    protected function addElements()
    {
        // Add "email" field
        $this->add([
            'type'  => 'text',
            'name' => 'email',
            'options' => [
                'label' => 'E-mail',
            ],
        ]);

        // Add "first_name" field
        $this->add([
            'type'  => 'text',
            'name' => 'first_name',
            'options' => [
                'label' => 'Nome',
            ],
        ]);

        // Add "last_name" field
        $this->add([
            'type'  => 'text',
            'name' => 'last_name',
            'options' => [
                'label' => 'Sobrenome',
            ],
        ]);

        if ($this->scenario == 'create') {

            // Add "password" field
            $this->add([
                'type'  => 'password',
                'name' => 'password',
                'options' => [
                    'label' => 'Senha',
                ],
            ]);

            // Add "confirm_password" field
            $this->add([
                'type'  => 'password',
                'name' => 'confirm_password',
                'options' => [
                    'label' => 'Confirmar senha',
                ],
            ]);
        }

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

        // Add "roles" field
        $this->add([
            'type'  => 'select',
            'name' => 'roles',
            'attributes' => [
                'multiple' => 'multiple',
            ],
            'options' => [
                'label' => 'Função (para o sistema)',
            ],
        ]);

        // Add "departament" field
        $this->add([
            'type'  => 'select',
            'name' => 'departaments',
            'attributes' => [
                'multiple' => 'multiple',
            ],
            'options' => [
                'label' => 'Setor(es)',
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

        // Add input for "email" field
        $inputFilter->add([
                'name'     => 'email',
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
                                StringLength::TOO_LONG  => $this->mailNoLong,
                            ]
                        ],
                    ],
                    [
                        'name' => EmailAddress::class,
                        'options' => [
                            'allow' => \Zend\Validator\Hostname::ALLOW_DNS,
                            'useMxCheck'    => false,
                            'messages' => [
                                EmailAddress::INVALID_FORMAT => $this->mailFormatInvalid

                            ]
                        ],
                    ],
                    [
                        'name' => UserExistsValidator::class,
                        'options' => [
                            'entityManager' => $this->entityManager,
                            'user' => $this->user
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

        // Add input for "full_name" field
        $inputFilter->add([
                'name'     => 'first_name',
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StringTrim'
                    ],
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

        if ($this->scenario == 'create') {

            // Add input for "password" field
            $inputFilter->add([
                'name'     => 'password',
                'required' => false,
                'filters'  => [],
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'min' => 6,
                            'max' => 64,
                            'messages' => [
                                StringLength::TOO_SHORT => $this->tooShortPw,
                                StringLength::TOO_LONG  => $this->tooLongPw,
                            ]
                        ],
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
            ]);

            // Add input for "confirm_password" field
            $inputFilter->add([
                'name'     => 'confirm_password',
                'required' => false,
                'filters'  => [],
                'validators' => [
                    [
                        'name'    => Identical::class,
                        'options' => [
                            'token' => 'password',
                            'messages' => [
                                Identical::NOT_SAME => $this->notIdentical
                            ]
                        ],
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
            ]);
        }

        // Add input for "status" field
        $inputFilter->add([
            'name'     => 'status',
            'required' => false,
            'filters'  => [
                [
                    'name' => 'ToInt'
                ],
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

        // Add input for "roles" field
        $inputFilter->add([
            'class'    => ArrayInput::class,
            'name'     => 'roles',
            'required' => true,
            'filters'  => [
                [
                    'name' => 'ToInt'
                ],
            ],
            'validators' => [
                [
                    'name' => GreaterThan::class,
                    'options' => [
                        'min' => 0,
                        'messages' => [
                            GreaterThan::NOT_GREATER => $this->notGreater
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

        // Add input for "departament" field
        $inputFilter->add([
            'class'    => ArrayInput::class,
            'name'     => 'departaments',
            'required' => false,
            'filters'  => [
                [
                    'name' => 'ToInt'
                ],
            ],
            'validators' => [
                [
                    'name' => GreaterThan::class,
                    'options' => [
                        'min'=>0,
                        'messages' => [
                            GreaterThan::NOT_GREATER => $this->notGreater
                        ]
                    ]
                ]
            ],
        ]);

    }
}