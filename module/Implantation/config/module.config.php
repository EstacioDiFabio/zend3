<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Implantation;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [

    'doctrine' => [
        'driver' => [
            'Windel_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [
                    1 => './module/Implantation/src/V1/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'Implantation' => 'Windel_driver',
                ],
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'implantation' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/implantacao[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'deployment_schedule' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/agenda-tecnico[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\DeploymentScheduleController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'client_scheduling' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/agenda-cliente[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ClientSchedulingController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class              => Controller\Factory\IndexControllerFactory::class,
            Controller\DeploymentScheduleController::class => Controller\Factory\DeploymentScheduleControllerFactory::class,
            Controller\ClientSchedulingController::class => Controller\Factory\ClientSchedulingControllerFactory::class,
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
                ['actions' => '*', 'allow' => '+implantation.manage'],
            ],
            Controller\DeploymentScheduleController::class => [
                ['actions' => '*', 'allow' => '+implantation.manage'],
            ],
            Controller\ClientSchedulingController::class => [
                ['actions' => '*', 'allow' => '+implantation.manage'],
            ],
        ]
    ],
    'service_manager' => [
        'factories' => [
            Service\DeploymentScheduleManager::class => Service\Factory\DeploymentScheduleManagerFactory::class
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
