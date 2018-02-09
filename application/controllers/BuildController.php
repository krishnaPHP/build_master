<?php
/**
 * Build Controller
 *
 * @Author: Krishna S
 * Date: 08-02-2018
 */

namespace Application\Controllers;


use Application\Core\Config;

class BuildController extends \Application\Core\Controller
{

    private $model;

    private $buildPath;

    public function __construct()
    {
        parent::__construct();

        $this->model = $this->loadModel('\Application\Models\Build');

        $this->buildPath = Config::getConfig('build_path');
    }

    public function index()
    {
        //$listOfRecords = $this->model->getList(); echo "<pre>"; var_dump($listOfRecords);

    }

    public function create()
    {

        $configVars = $this->getConfigVars("SSC_V_6_0");
        var_dump($configVars);

        exit;

        if ($this->isPost()) {
            $build_name = $this->getPost('build_name');
            $configVars = $this->getConfigVars($build_name);

        }

        $builds = $this->getBuilds();

        $listOfRecords = $this->model->getList();
        foreach ($listOfRecords as $record) {
            if (($key = array_search($record->build_name, $builds)) !== false)
                unset($builds[$key]);
        }

        $view = $this->loadView('build/form');
        $view->set('builds', $builds);
        $view->render();
    }

    private function getBuilds()
    {
        $builds = array();
        if (is_dir($this->buildPath)) {
            $directories = scandir($this->buildPath);
            foreach ($directories as $item) {
                if ($item != '..' && $item != '.' && is_dir($this->buildPath . "/" . $item))
                    array_push($builds, $item);
            }
        }
        return $builds;
    }

    private function getConfigVars($build_name)
    {
        $configVars = array();
        $build = $this->buildPath . "/" . $build_name;

        // Admin Config Vars
        $configVars['admin'] = array();
        $adminConfig = $build . "/examengine/admin/dbconfig.php";
        $contents = file_get_contents($adminConfig, false, null);
        if (preg_match_all('/[\$dbuser].*/', $contents, $matches)) {
            echo "<pre>";
            print_r($matches);
        }

        //var_dump($configVars);
        //}
        exit;


        while (!feof($fh)) {
            $line = fgets($fh);
            if (strpos($line, '$dbuser')) {
                echo $line . "<br>";
            }
        }
        fclose($fh);

        // Client Config vars
        $configVars['client'] = array();
        $clientConfig = $build . "examengine/dbconfig.php";

        return $configVars;
    }

    private function getValue($line, $key)
    {
        echo $line . "<br/>";
        if (strpos($line, "'.$key.'")) {
            $rr = explode('=', $line);
            return str_replace(array('"', ';'), array('', ''), $rr[1]);
        }
        return "";
    }

}