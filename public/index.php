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

use League\Container\Container;
use Proton\Application;

$bootstrap = require __DIR__.'/../app/bootstrap.php';
$container = new Container($bootstrap);

$app = new Application();
$app->setContainer($container);

require __DIR__.'/../app/config.php';
config::setupConfig($app);

$app->get('/', 'controller::index');

// Global actions
$app->get('/start', 'controller::startAllServers');
$app->get('/restart', 'controller::restartAllServers');
$app->get('/stop', 'controller::stopAllServers');
$app->get('/clearAllProcessLogs', 'controller::clearAllProcessLogs');

// Instance actions
$app->get('/start/{instance}', 'controller::startAll');
$app->get('/restart/{instance}', 'controller::restartAll');
$app->get('/stop/{instance}', 'controller::stopAll');
$app->get('/clearAllProcessLogs/{instance}', 'controller::clearAllProcessLogsInstance');

// Process actions
$app->get('/start/{instance}/{process}', 'controller::startProcess');
$app->get('/restart/{instance}/{process}', 'controller::restartProcess');
$app->get('/stop/{instance}/{process}', 'controller::stopProcess');
$app->get('/tailStdout/{instance}/{process}', 'controller::tailStdout');
$app->get('/tailStderr/{instance}/{process}', 'controller::tailStderr');
$app->get('/clearProcessLogs/{instance}/{process}', 'controller::clearProcessLogs');

$app->run();
