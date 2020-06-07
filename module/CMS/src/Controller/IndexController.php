<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CMS\Controller;

use CMS\Controller\CMSController;
use Zend\View\Model\ViewModel;
use Zend\Code\Reflection\ClassReflection;
use CMS\V1\Entity\User;

class IndexController extends CMSController
{

    /**
     * Entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Auth service.
     * @var Zend\Authentication\Authentication
     */
    private $authService;


    /**
     * Constructor.
     */
    public function __construct($entityManager, $authService)
    { 
        $this->entityManager = $entityManager;
        $this->authService   = $authService;
        
    }

    public function indexAction()
    {
        if ($this->identity()!=null) {
            $userEmail = $this->identity();
        }

        return new ViewModel();
    }
}
