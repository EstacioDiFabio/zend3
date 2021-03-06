<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace CMS;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [

    'doctrine' => [
        'driver' => [
            'Default_driver_zend' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    1 => './module/CMS/src/V1/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'CMS' => 'Default_driver_zend',
                ],
            ],
        ],
    ],

    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'users' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/usuarios[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller'    => Controller\UserController::class,
                        'action'        => 'index', 
                    ],
                ],
            ],
            'departaments' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/setores[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller'    => Controller\DepartamentController::class,
                        'action'        => 'index',
                    ],
                ],
            ],

            'mailTemplates' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/mail-templates[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]*',
                    ],
                    'defaults' => [
                        'controller'    => Controller\MailTemplateController::class,
                        'action'        => 'index',
                    ],
                ],
            ],

        ],
    ],
    'controllers' => [
        'factories' => [

            Controller\IndexController::class        => Controller\Factory\IndexControllerFactory::class,
            Controller\UserController::class         => Controller\Factory\UserControllerFactory::class,
            Controller\DepartamentController::class  => Controller\Factory\DepartamentControllerFactory::class,
            Controller\MailTemplateController::class => Controller\Factory\MailTemplateControllerFactory::class,
            Controller\CMSController::class          => Controller\Factory\CMSControllerFactory::class,

        ],
    ],
    // We register module-provided controller plugins under this key.
    'controller_plugins' => [
        'factories' => [],
        'aliases' => [],
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
            Controller\IndexController::class => [
                ['actions' => ['index'], 'allow' => '*'],
                ['actions' => ['settings'], 'allow' => '+basic.manage']
            ],
            Controller\UserController::class => [
                ['actions' => ['resetPassword', 'message', 'setPassword'], 'allow' => '*'],
                ['actions' => ['index', 'add', 'view', 'edit', 'remove',
                               'changePassword', 'search', 'toggleActive'],
                               'allow' => '+basic.manage']
            ],
            Controller\DepartamentController::class => [
                ['actions' => '*','allow' => '+basic.manage']
            ],
            Controller\MailTemplateController::class => [
                ['actions' => '*', 'allow' => '+mail-template.manage']
            ],
        ]
    ],
    'service_manager' => [
        'factories' => [
            Service\UserManager::class         => Service\Factory\UserManagerFactory::class,
            Service\DepartamentManager::class  => Service\Factory\DepartamentManagerFactory::class,
            Service\NavManager::class          => Service\Factory\NavManagerFactory::class,
            Service\CsecMail::class          => Service\Factory\CsecMailFactory::class,
            Service\MailTemplateManager::class => Service\Factory\MailTemplateManagerFactory::class,
            Service\ImageManager::class        => InvokableFactory::class,
        ],
    ],
    'view_helpers' => [],
    'view_manager' => [
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewJsonStrategy',
        ],
    ],
    // The following key allows to define custom styling for FlashMessenger view helper.
    'view_helper_config' => [
        'flashmessenger' => [
            'message_open_format'      => '<div%s><ul><li>',
            'message_close_string'     => '</li></ul></div>',
            'message_separator_string' => '</li><li>'
        ]
    ],
];
