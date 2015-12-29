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
use Proton\Application;

class Config {

    static function setupConfig(Application $app) {
        $loader = new YamlLoader;
        $schema = new MetaYaml($loader->loadFromFile(__DIR__ . '/config_schema.yml'));

        $schema->validate($config = $loader->loadFromFile(__DIR__ . '/../config/config.yml'));

        foreach ($config as $key => $value) {
            $app->setConfig($key, $value);
        }
        if (array_key_exists('SCRIPT_NAME', $_SERVER) && is_string($_SERVER['SCRIPT_NAME']) && !empty($_SERVER['SCRIPT_NAME'])) {
            $dir = dirname($_SERVER['SCRIPT_NAME']);
        } else {
            $dir = '/';
        }
        $app->setConfig('dir', $dir);
    }

}
