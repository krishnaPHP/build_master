<?php
/**
 * UrlHelper
 * Date: 30-11-2017
 */

namespace Application\Helper;

class UrlHelper
{

    function getBaseUrl()
    {
        global $config;
        return $config['base_url'];
    }

    function segment($seg)
    {
        if (!is_int($seg)) return false;

        $parts = explode('/', $_SERVER['REQUEST_URI']);
        return isset($parts[$seg]) ? $parts[$seg] : false;
    }

}

?>