<?php
/**
 * BuildHelper Helper
 * Date: 30-11-2017
 */

namespace Application\Helpers;

use Application\Core\Config;

class BuildHelper
{
    private $buildPath, $buildDownloadPath;

    public function __construct()
    {
        $this->buildPath = Config::getConfig('build_path');
        $this->buildDownloadPath = Config::getConfig('build_download_path');
    }

    public function getBuilds()
    {
        $builds = array();
        if (is_dir($this->buildPath)) {
            $directories = scandir($this->buildPath);
            foreach ($directories as $item) {
                if ($item != '..' && $item != '.'
                    && is_dir($this->buildPath . "/$item")
                    && !file_exists($this->buildDownloadPath . "/$item.zip"))
                    array_push($builds, $item);
            }
        }
        return $builds;
    }

    public function getConfigVars($build_name)
    {
        $configVars = array();
        $build = $this->buildPath . $build_name;

        // Admin Config Vars
        $adminConfig = $build . "/examengine/admin/dbconfig.php";
        $fh = fopen($adminConfig, "r");
        $configVars['db_username'] = $this->getValue($fh, '/\$dbuser.*/');
        $configVars['db_password'] = $this->getValue($fh, '/\$dbpwd.*/');
        $configVars['db_name'] = $this->getValue($fh, '/\$database.*/');
        fclose($fh);

        // Client Config vars
        $clientConfig = $build . "/examengine/dbconfig.php";
        $fh = fopen($clientConfig, "r");
        $configVars['client_dbuser'] = $this->getValue($fh, '/\$dbuser.*/');
        $configVars['client_dbpass'] = $this->getValue($fh, '/\$dbpwd.*/');
        fclose($fh);

        return $configVars;
    }

    private function getValue($fh, $key)
    {
        while (!feof($fh)) {
            $line = fgets($fh);
            if (preg_match($key, $line, $m))
                return $this->extractValue($m[0]);
        }
        return null;
    }

    private function extractValue($value)
    {
        $rr = explode('=', $value);
        return trim(str_replace(array('"', '\'', ';'), array('', '', ''), $rr[1]));
    }
}

?>