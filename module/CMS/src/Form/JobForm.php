<?php
namespace CMS\Form;

use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;
use CMS\V1\Entity\Job;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

/**
 * This form is used to collect job's name, the relation with job id_top_job and status.
 */
class JobForm extends Form
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

    private $jobs = [];

    private $notEmpty = 'Esse campo não pode ficar vazio.';
    private $tooShort = 'Esse campo deve ter no mínimo 3 caracteres.';
    private $tooLong  = 'Esse campo deve ter no máximo 255 caracteres.';
    /**
     * Constructor.
     */
    public function __construct($scenario = 'create', $entityManager = null)
    {
        // Define form name
        parent::__construct('job-form');

        // Set POST method for this form
        $this->setAttribute('method', 'post');

        // Save parameters for internal use.
        $this->scenario = $scenario;
        $this->entityManager = $entityManager;

        $this->jobs = $this->getJobs();

        $this->addElements();
        $this->addInputFilter();
    }

    private function getJobs()
    {
        $jobs = $this->entityManager->getRepository(Job::class)->findBy([], ['id'=>'ASC']);

        $jobData = array();
        if(count($jobs) > 0){
            foreach($jobs as $job){

                $jobData[$job->getId()] = $job->getName();
            }
        }

        return $jobData;
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

        // Add "id_top_job" field
        $this->add([
            'type'  => 'text',
            'name' => 'id_top_job',
            'options' => [
                'label' => 'Cargo Superior',
            ],
        ]);

        $this->add([
            'type'  => 'select',
            'name' => 'id_top_job',
            'options' => [
                'label' => 'Cargo Superior',
                'value_options' => $this->jobs,
                'empty_option' => '-------------',
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
                ['name'=>'InArray',
                 'options'=>['haystack'=>[1, 0]]]
            ],
        ]);

        $inputFilter->add([
                'name'     => 'id_top_job',
                'required' => false
            ]);
    }
}