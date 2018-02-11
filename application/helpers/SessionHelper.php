<?php
/**
 * Session Helper
 * Date: 30-11-2017
 */

namespace Application\Helpers;

class SessionHelper
{
    function set($key, $val)
    {
        $_SESSION[$key] = $val;
    }

    function get($key)
    {
        return isset($_SESSION[$key]) ? $_SESSION["$key"] : null;
    }

    function destroy()
    {
        session_destroy();
    }

}

?>