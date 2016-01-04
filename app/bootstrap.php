<?php

/*
 * This file is part of the Supervisor Monitor project.
 *
 * (c) Márk Sági-Kazár <mark.sagikazar@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use RomaricDrigon\MetaYaml\Loader\YamlLoader;
use RomaricDrigon\MetaYaml\MetaYaml;
use Supervisor\Monitor\Manager;

return [
    'di' => [
        'manager' => [
            'definition' => function () {
                $loader = new YamlLoader();
                $schema = new MetaYaml($loader->loadFromFile(__DIR__.'/schema.yml'));

                $schema->validate($config = $loader->loadFromFile(__DIR__.'/../config/supervisor.yml'));

                return new Manager($config);
            },
        ],
        'twig_loader' => [
            'class'     => 'Twig_Loader_Filesystem',
            'arguments' => [__DIR__.'/views'],
        ],
        'twig_extension' => [
            'class' => 'Supervisor\Monitor\TwigExtension',
        ],
        'twig' => [
            'class'     => 'Twig_Environment',
            'arguments' => ['twig_loader'],
            'methods'   => [
                'addExtension' => ['twig_extension'],
            ],
        ],
        'controller' => [
            'class'     => 'Supervisor\Monitor\Controller',
            'arguments' => ['app'],
        ],
    ],
];
