<?php
/**
 * Application initialization and dispatching an action
 *
 * @Author: Krishna S
 */

namespace Application;

class Application extends \Framework\Bootstrap
{
    public static function run()
    {
        self::init();
        self::dispatch();
    }

}