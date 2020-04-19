<?php
namespace CMS\Form;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use CMS\Validator\MailTemplateExistsValidator;
use CMS\V1\Entity\MailTemplate;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;
/**
 * This form is used to collect mail_template's name, header, footer and status.
 */
class MailTemplateForm extends Form
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
    private $tooSoLong  = 'Esse campo deve ter no máximo 16777215 caracteres.';

    /**
     * Constructor.
     */
    public function __construct($scenario = 'create', $entityManager = null)
    {
        // Define form name
        parent::__construct('mail-template-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');

        // Save parameters for internal use.
        $this->scenario = $scenario;
        $this->entityManager = $entityManager;

        $this->addElements();
        $this->addInputFilter();
    }

    private function getMailTemplates()
    {
        $mailTemplates = $this->entityManager->getRepository(MailTemplate::class)->findBy([], ['id'=>'ASC']);

        if(count($mailTemplates) > 0){
            $templateData = array();
            foreach($mailTemplates as $template){

                $templateData[$template->getId()] = $template->getName();
            }
        }

        return $templateData;
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

        // Add "header" field
        $this->add([
            'type'  => 'textarea',
            'name' => 'header',
            'options' => [
                'label' => 'Cabeçalho',
            ],
        ]);

        // Add "content" field
        $this->add([
            'type'  => 'textarea',
            'name' => 'content',
            'options' => [
                'label' => 'Conteúdo',
            ],
        ]);

        // Add "footer" field
        $this->add([
            'type'  => 'textarea',
            'name' => 'footer',
            'options' => [
                'label' => 'Rodapé',
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

        // Add "file" field
        $this->add([
            'type'  => 'file',
            'name' => 'image',
            'attributes' => [
                'class' => 'file'
            ],
            'options' => [
                'label' => 'Anexo arquivo/imagem',
            ],
        ]);
        // Add "file" field
        $this->add([
            'type'  => 'text',
            'name' => 'identifier',
            'options' => [
                'label' => 'Identificador',
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

        // Add input for "header" field
        $inputFilter->add([
                'name'     => 'header',
                'required' => false,
                'filters'  => [
                    ['name' => 'HtmlEntities'],
                ],
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'min' => 0,
                            'max' => 16777215,
                            'messages' => [
                                StringLength::TOO_SHORT => $this->tooShort,
                                StringLength::TOO_LONG  => $this->tooSoLong,
                            ]
                        ],
                    ],
                ],
            ]);

        // Add input for "content" field
        $inputFilter->add([
                'name'     => 'content',
                'required' => false,
                'filters'  => [
                    ['name' => 'HtmlEntities'],
                ],
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'min' => 0,
                            'max' => 16777215,
                            'messages' => [
                                StringLength::TOO_SHORT => $this->tooShort,
                                StringLength::TOO_LONG  => $this->tooSoLong,
                            ]
                        ],
                    ],
                ],
            ]);

        // Add input for "footer" field
        $inputFilter->add([
                'name'     => 'footer',
                'required' => false,
                'filters'  => [
                    ['name' => 'HtmlEntities'],
                ],
                'validators' => [
                    [
                        'name'    => StringLength::class,
                        'options' => [
                            'min' => 0,
                            'max' => 16777215,
                            'messages' => [
                                StringLength::TOO_SHORT => $this->tooShort,
                                StringLength::TOO_LONG  => $this->tooSoLong,
                            ]
                        ],
                    ],
                ],
            ]);

        // Add input for "image" field
        $inputFilter->add([
                'type'     => 'Zend\InputFilter\FileInput',
                'name'     => 'image',
                'required' => false,
                'validators' => [
                    ['name'    => 'FileUploadFile'],
                    [
                        'name'    => 'FileMimeType',
                        'options' => [
                            'mimeType'  => ['image/jpeg', 'image/png',
                                            'image/gif', 'application/pdf',
                                            'text/csv', 'application/msword',
                                            'application/vnd.oasis.opendocument.text',
                                            'application/vnd.oasis.opendocument.spreadsheet']
                        ]
                    ],
                    // ['name'    => 'FileIsImage'],
                    // [
                    //     'name'    => 'FileImageSize',
                    //     'options' => [
                    //         'minWidth'  => 128,
                    //         'minHeight' => 128,
                    //         'maxWidth'  => 4096,
                    //         'maxHeight' => 4096
                    //     ]
                    // ],
                ],
                'filters'  => [
                    [
                        'name' => 'FileRenameUpload',
                        'options' => [
                            'target' => \Base\Module::UPLOAD_DIR,
                            'useUploadName' => false,
                            'useUploadExtension' => true,
                            'overwrite' => true,
                            'randomize' => true,
                        ]
                    ],
                ],
        ]);

        // Add input for "identifier" field
        $inputFilter->add([
                'name'     => 'identifier',
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