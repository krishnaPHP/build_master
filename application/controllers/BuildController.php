<?php
/**
 * Build Controller
 *
 * @Author: Krishna S
 * Date: 08-02-2018
 */

namespace Application\Controllers;

use Application\Core\Config;
use Application\Helpers\BuildHelper;

class BuildController extends \Application\Core\Controller
{
    /**
     * @var \Application\Models\Build
     */
    private $model;

    private $buildPath, $buildDownloadPath;

    public function __construct()
    {
        parent::__construct();

        if ($this->isAuthenticated())
            $this->redirect('auth');

        $this->model = $this->loadModel('\Application\Models\Build');

        $this->buildPath = Config::getConfig('build_path');
        $this->buildDownloadPath = Config::getConfig('build_download_path');
    }

    public function index()
    {
        $view = $this->loadView('build/index');

        $view->set('list', $this->model->getAll());
        $view->render();
    }

    public function create()
    {
        $buildHelper = new BuildHelper();

        $view = $this->loadView('build/form');

        if ($this->isPost()) {

            try {

                $build_name = $this->getPost('build_name');

                // Compress as Zip
                //Todo: Build creation as zip

                $data = array_merge(array('build_name' => $build_name), $buildHelper->getConfigVars($build_name));
                $lastInsertId = $this->model->insert($data);
                if ($lastInsertId)
                    $view->set('alert', array("status" => "success", "message" => "Build Creation saved successfully"));
                else {
                    $view->set('alert', array("status" => "failed",
                        "message" => "Build Creation could not be saved. Please contact admin.."));
                }

            } catch (\Exception $e) {
                $view->set('alert', array("status" => "failed", "message" => $e->getMessage()));
            }
        }

        $view->set('builds', $buildHelper->getBuilds());
        $view->render();
    }

}