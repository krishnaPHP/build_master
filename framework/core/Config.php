<?php
/**
 * @User: Krishna
 * Date: 08-02-2018
 */

namespace Framework\Core;

class Config
{
    private static $config = array();

    public static function getConfig($key = null)
    {
        self::$config = include(CONFIG_PATH . 'common.php');
        if ($key !== null) {
            return self::$config[$key];
        }
        return self::$config;
    }

    public static function getDBConfig()
    {
        return include(CONFIG_PATH . 'database.php');
    }

}