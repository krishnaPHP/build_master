<?php
/**
 * DownloadController Controller
 *
 * @Author: Krishna
 * Date: 08-02-2018
 */

namespace Application\Controllers;


use Application\Core\Config;

class DownloadController extends \Application\Core\Controller
{
    const STATUS_STARTED = "STARTED";
    const STATUS_COMPLETED = "COMPLETED";

    private $buildPath;

    public function __construct()
    {
        parent::__construct();

        $this->buildPath = Config::getConfig('build_path');
    }

    public function index()
    {
        if ($this->isGet()) {
            $serialNumber = $this->getGet('serialNumber');
            $serialNumberInfo = $this->getSerialNumberInfo($serialNumber);
            if ($serialNumberInfo !== null) {
                $build = $serialNumberInfo->build . "zip";
                $buildDownload = $this->buildPath . "/" . $build;
                $isBuildReady = $this->checkBuildReady($serialNumberInfo->build);
                if (!$isBuildReady)
                    $this->createBuild($serialNumberInfo->build);

                if ($isBuildReady) {

                    $this->trackDownload($serialNumber, self::STATUS_STARTED, $buildDownload);

                    header('Content-Type: application/zip');
                    header('Content-Disposition: attachment; filename="' . $build . '"');
                    header('Content-Length: ' . filesize($buildDownload));
                    readfile($buildDownload);
                    exit();
                }
            }
        }
    }

    public function updateCompleted($serialNumber)
    {
        $this->trackDownload($serialNumber, self::STATUS_COMPLETED);
    }

    private function getSerialNumberInfo($serialNumber)
    {

    }

    private function checkBuildReady($build)
    {
        $downloadBuild = $this->buildPath . "/" . "$build.zip";
        if (file_exists($downloadBuild))
            return true;
        return false;
    }

    private function createBuild($build)
    {

    }

    private function trackDownload($serialNumber, $status, $buildPath = null)
    {

    }
}