<?php

/*
 * This file is part of the Supervisor Monitor project.
 *
 * (c) Márk Sági-Kazár <mark.sagikazar@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__.'/../vendor/autoload.php';

use Proton\Application;
use League\Container\Container;

$bootstrap = require __DIR__.'/../app/bootstrap.php';
$container = new Container($bootstrap);

$app = new Application;
$app->setContainer($container);

require __DIR__.'/../app/config.php';
Config::setupConfig($app);

$app->get('/', 'controller::index');

$app->get('/start/{instance}', 'controller::startAll');
$app->get('/restart/{instance}', 'controller::restartAll');
$app->get('/stop/{instance}', 'controller::stopAll');

$app->get('/start/{instance}/{process}', 'controller::startProcess');
$app->get('/restart/{instance}/{process}', 'controller::restartProcess');
$app->get('/stop/{instance}/{process}', 'controller::stopProcess');

$app->run();
