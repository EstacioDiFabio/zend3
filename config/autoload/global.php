<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */

use Doctrine\DBAL\Driver\PDOMySql\Driver as PDOMySqlDriver;
use Zend\Session\Storage\SessionArrayStorage;
use Zend\Session\Validator\RemoteAddr;
use Zend\Session\Validator\HttpUserAgent;

return [

    'doctrine' => [
        'connection' => [

            'orm_log' => [
                'driverClass' => PDOMySqlDriver::class,
                'params' => [
                    'host'     => 'localhost',
                    'port'     => '3306',
                    'user'     => \Base\Module::LOG_USERNAME,
                    'password' => \Base\Module::LOG_PASSWORD,
                    'dbname'   => \Base\Module::LOG_DATABASE,
                    'charset' => 'utf8',
                    'driverOptions' => ['SET NAMES utf8']
                ],
                // To automatically convert enum to string
                'doctrine_type_mappings' => [
                    'enum' => 'string'
                ],
            ],

            'orm_default' => [
                'driverClass' => PDOMySqlDriver::class,
                'params' => [
                    'host'     => 'localhost',
                    'port'     => '3306',
                    'user'     => \Base\Module::USERNAME,
                    'password' => \Base\Module::PASSWORD,
                    'dbname'   => \Base\Module::DATABASE,
                    'charset' => 'utf8',
                    'driverOptions' => ['SET NAMES utf8']
                ],
                // To automatically convert enum to string
                'doctrine_type_mappings' => [
                    'enum' => 'string'
                ],
            ],

        ],
       'entitymanager' => [
            'orm_log' => [
                'connection' => 'orm_log',
                'configuration' => 'orm_log',
            ]
        ],
        'configuration' => [
            'orm_log' => [
                'metadata_cache' => 'array',
                'query_cache' => 'array',
                'result_cache' => 'array',
                'hydration_cache' => 'array',

                'generate_proxies' => false,
            ]
        ],
    ],
    'session_config' => [
        'cookie_lifetime'     => 60*60*1, // Session cookie will expire in 1 hour.
        'gc_maxlifetime'      => 60*60*24*30, // How long to store session data on server (for 1 month).
    ],
    // Session manager configuration.
    'session_manager' => [
        // Session validators (used for security).
        'validators' => [
            // RemoteAddr::class,
            // HttpUserAgent::class,
        ]
    ],
    // Session storage configuration.
    'session_storage' => [
        'type' => SessionArrayStorage::class
    ],
    'caches' => [
        'FilesystemCache' => [
            'adapter' => [
                'name'    => Filesystem::class,
                'options' => [
                    // Store cached data in this directory.
                    'cache_dir' => './data/cache',
                    // Store cached data for 1 hour.
                    'ttl' => 60*60*1
                ],
            ],
            'plugins' => [
                [
                    'name' => 'serializer',
                    'options' => [
                    ],
                ],
            ],
        ],
    ],

];
