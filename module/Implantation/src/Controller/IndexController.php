<?php

namespace Implantation\Controller;

use Implantation\Controller\ImplantationController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;


class IndexController extends ImplantationController
{
    /**
     * Array used to creating dinamic filters
     */
    private $searchArray = [
        'ct.cotIdcontrato' => 'Contrato',
        'cl.cliIdcliente' => 'Cliente'
    ];

    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Constructor.
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * This is the default "index" action of the controller. It displays the
     * list of completed process contracts.
     */
    public function indexAction()
    {
        $datas = $this->windelAPI()->doGETRequest("contratos-processo-concluido", $this->getRequest());

        return new ViewModel([
            'datas' => $datas,
            'search' => $this->searchArray,
            'operators' => $this->searchMethods
        ]);
    }

    /**
     * The "Search" action is used to filter data in the search.
     */
    public function searchAction()
    {

        if ($this->getRequest()->isGet()) {

            $search = $this->params()->fromQuery();
            $finder = $this->windelFilter()->performWhereString($search);
            $responseData = $this->windelAPI()->doGETRequest("contratos-processo-concluido", $this->getRequest(), $finder);
        }

        $retorno = [];

        if(count($responseData) == 0) {
            $retorno['data'] = [];
        } else {
            foreach ($responseData as $key => $data) {

                $retorno[$key] = [
                    '0' => $data['cotIdcontrato'],
                    '1' => $data['cliIdcliente'],
                ];

            }
        }

        return new JsonModel(['data' => $retorno]);
    }

}
