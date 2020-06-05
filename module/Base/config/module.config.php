<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Base;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [

    'doctrine' => [
        'driver' => [

            'log_driver'  => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    // 0 => './module/Base/src/Entity',
                    __DIR__ . '/../src/Entity'
                ]
            ],
            'orm_log' => [
                'drivers' => [
                    'Base' => 'log_driver'
                ],
            ],

        ],

    ],
    'router' => [
        'routes' => [
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\BaseController::class             => Controller\Factory\BaseControllerFactory::class,
        ],
    ],
    // We register module-provided controller plugins under this key.
    'controller_plugins' => [
        'factories' => [
            Controller\Plugin\AccessPlugin::class        => Controller\Plugin\Factory\AccessPluginFactory::class,
            Controller\Plugin\CurrentUserPlugin::class   => Controller\Plugin\Factory\CurrentUserPluginFactory::class,
            Controller\Plugin\CsecHtmlPlugin::class    => Controller\Plugin\Factory\CsecHtmlPluginFactory::class,
            Controller\Plugin\CsecFilterPlugin::class  => Controller\Plugin\Factory\CsecFilterPluginFactory::class,
            Controller\Plugin\CsecInputPlugin::class   => Controller\Plugin\Factory\CsecInputPluginFactory::class,
            Controller\Plugin\CsecAPIPlugin::class     => Controller\Plugin\Factory\CsecAPIPluginFactory::class,
        ],
        'aliases' => [
            'access'       => Controller\Plugin\AccessPlugin::class,
            'currentUser'  => Controller\Plugin\CurrentUserPlugin::class,
            'csecHtml'   => Controller\Plugin\CsecHtmlPlugin::class,
            'csecFilter' => Controller\Plugin\CsecFilterPlugin::class,
            'csecInput'  => Controller\Plugin\CsecInputPlugin::class,
            'csecAPI'    => Controller\Plugin\CsecAPIPlugin::class,
        ],
    ],
    // The 'access_filter' key is used by the User module to restrict or permit
    // access to certain controller actions for unauthorized visitors.
    'access_filter' => [
        'options' => [
            // The access filter can work in 'restrictive' (recommended) or 'permissive'
            // mode. In restrictive mode all controller actions must be explicitly listed
            // under the 'access_filter' config key, and access is denied to any not listed
            // action for not logged in users. In permissive mode, if an action is not listed
            // under the 'access_filter' key, access to it is permitted to anyone (even for
            // not logged in users. Restrictive mode is more secure and recommended to use.
            'mode' => 'restrictive'
        ],
        'controllers' => [
            Controller\BaseController::class => [
                ['actions' => '*', 'allow' => '*'],
            ],
        ]
    ],
    'service_manager' => [
        'factories' => [
            Service\BaseManager::class     => Service\Factory\BaseManagerFactory::class,
            Service\ActivityManager::class => Service\Factory\ActivityManagerFactory::class,
            Service\ErrorManager::class    => Service\Factory\ErrorManagerFactory::class,
            Event\EventListener::class     => Service\Factory\EventListenerFactory::class
        ],
    ],
    'view_helpers' => [
        'factories' => [
            View\Helper\Menu::class        => View\Helper\Factory\MenuFactory::class,
            View\Helper\Breadcrumbs::class => InvokableFactory::class,
            View\Helper\Access::class      => View\Helper\Factory\AccessFactory::class,
            View\Helper\CurrentUser::class => View\Helper\Factory\CurrentUserFactory::class,
            View\Helper\InputForm::class   => View\Helper\Factory\InputFormFactory::class,
        ],
        'aliases' => [
            'mainMenu'        => View\Helper\Menu::class,
            'pageBreadcrumbs' => View\Helper\Breadcrumbs::class,
            'access'          => View\Helper\Access::class,
            'currentUser'     => View\Helper\CurrentUser::class,
            'inputForm'       => View\Helper\InputForm::class,
        ],
    ],
    'view_manager' => [

        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'cms/index/index'         => __DIR__ . '/../../CMS/view/cms/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],

    ],

];
