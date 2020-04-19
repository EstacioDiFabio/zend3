<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Quiz;

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
                    1 => './module/Quiz/src/V1/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'Quiz' => 'Windel_driver',
                ],
            ],
        ],
    ],
    'router' => [
        'routes' => [
            'quizForm' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/formulario[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\QuestionFormController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'quiz' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/questionario[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\QuestionController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'quizField' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/campos[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\QuestionFieldController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'produto' => [
                'type' => Segment::class,
                'options' => [
                    'route'    => '/produto[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\ProdutoController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\QuestionFormController::class      => Controller\Factory\QuestionFormControllerFactory::class,
            Controller\QuestionController::class          => Controller\Factory\QuestionControllerFactory::class,
            Controller\QuestionFieldController::class     => Controller\Factory\QuestionFieldControllerFactory::class,
            Controller\ProdutoController::class           => Controller\Factory\ProdutoControllerFactory::class,
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
            Controller\QuestionFormController::class => [
                ['actions' => '*', 'allow' => '+quiz.manage'],
            ],
            Controller\QuestionController::class => [
                ['actions' => '*', 'allow' => '+quiz.manage'],
            ],
            Controller\QuestionFieldController::class => [
                ['actions' => '*', 'allow' => '+quiz.manage'],
            ],
            Controller\ProdutoController::class => [
                ['actions' => '*', 'allow' => '+basic.manage'],
            ],
        ]
    ],
    'service_manager' => [
        'factories' => [
            Service\QuestionFormManager::class             => Service\Factory\QuestionFormManagerFactory::class,
            Service\QuestionManager::class                 => Service\Factory\QuestionManagerFactory::class,
            Service\QuestionFieldManager::class            => Service\Factory\QuestionFieldManagerFactory::class,
            Service\QuestionFieldFilledValueManager::class => Service\Factory\QuestionFieldFilledValueManagerFactory::class,
            Service\ProdutoManager::class                  => Service\Factory\ProdutoManagerFactory::class,
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
