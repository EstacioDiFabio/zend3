<?php

namespace Implantation\Controller;

use Base\Controller\BaseController;

class ImplantationController extends BaseController
{
    public $searchMethods = [
        '=' => 'Igual',
        '<>' => 'Diferente',
        '>' => 'Maior que',
        '<' => 'Menor que',
        '>=' => 'Maior ou igual que',
        '<=' => 'Menor ou igual que'
    ];

    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

}
