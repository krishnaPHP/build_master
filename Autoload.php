<?php
/**
 * Used to auto-load all files in directories
 */

class AutoLoad
{
    public static function load($className)
    {
        $parts = explode("\\", $className);
        $className = end($parts);

        //list comma separated directory name
        $directory = array(
            'framework/',
            'framework/core/',
            'framework/database/',

            'application/',
            'application/core/',
            'application/controllers/',
            'application/helpers/',
            'application/models/');

        //list of comma separated file format
        $fileFormat = array('%s.php');

        foreach ($directory as $current_dir) {
            foreach ($fileFormat as $current_format) {
                $path = $current_dir . sprintf($current_format, $className); //echo $path. "<br/>";
                if (file_exists($path)) {
                    include $path; break;
                }
            }
        }
    }
}

spl_autoload_register('Autoload::load');