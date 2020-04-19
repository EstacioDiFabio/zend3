<?php

namespace Quiz\Form;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;
use Quiz\V1\Entity\Produto;

/**
 * This form is used to collect question_form's
 * name, and status.
 */

class QuestionFormForm extends Form
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
    private $tooShort = 'Esse campo deve ter no mínimo 3 caracteres.';
    private $tooLong  = 'Esse campo deve ter no máximo 255 caracteres.';

    /**
     * Constructor.
     */
    public function __construct($scenario = 'create', $entityManager = null)
    {
        // Define form name
        parent::__construct('question-form-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');

        // Save parameters for internal use.
        $this->scenario = $scenario;
        $this->entityManager = $entityManager;

        $produtos = $this->entityManager->getRepository(Produto::class)
                                         ->findBy([], ['id'=>'ASC']);

        if($produtos) {
            foreach($produtos as $produto){
                $nome = $produto->getNome();
                $this->produtos[$produto->getId()] = $nome;
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
        // Add "name" field
        $this->add([
            'type'  => 'text',
            'name' => 'name',
            'options' => [
                'label' => 'Nome do Formulário',
            ],
        ]);
        // Add "local" field
        $this->add([
            'type'  => 'text',
            'name' => 'local',
            'options' => [
                'label' => 'Local',
            ],
        ]);
        // Add "sequence" field
        $this->add([
            'type'  => 'number',
            'name' => 'sequence',
            'options' => [
                'label' => 'Ordem',
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
        // Add "id_produto" field
        $this->add([
            'type'  => 'select',
            'name' => 'id_produto',
            'options' => [
                'label' => 'Produto',
                'value_options' => $this->produtos
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

        // Add "local" filter
        $inputFilter->add([
            'name'     => 'local',
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

        // Add "sequence" filter
        $inputFilter->add([
            'name'     => 'sequence',
            'required' => false,
            'filters'  => [
                ['name' => 'StringTrim'],
            ]
        ]);

        // Add "status" filter
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