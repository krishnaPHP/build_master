<?php
/**
 * DownloadController Controller
 *
 * @Author: Krishna
 * Date: 08-02-2018
 */

namespace Application\Controllers;

use Application\Core\Config;
use Application\Models\DownloadRequest;

class DownloadController extends \Application\Core\Controller
{
    private $buildPath, $buildDownloadPath;

    /**
     * @var $buildModel \Application\Models\Build
     */
    private $buildModel;

    /**
     * @var $serialNumberModel \Application\Models\SerialNumber
     */
    private $serialNumberModel;

    /**
     * @var \Application\Models\DownloadRequest
     */
    private $downloadRequestModel;

    public function __construct()
    {
        parent::__construct();

        $this->buildPath = Config::getConfig('build_path');
        $this->buildDownloadPath = Config::getConfig('build_download_path');

        $this->buildModel = $this->loadModel('\Application\Models\Build');
        $this->serialNumberModel = $this->loadModel('\Application\Models\SerialNumber');
        $this->downloadRequestModel = $this->loadModel('\Application\Models\DownloadRequest');
    }

    public function index($serialNumber)
    {
        try {

            $sNInfo = $this->getSerialNumberInfo($serialNumber);
            if ($sNInfo) {
                $build = $this->getBuild($sNInfo->build_id);
                if ($build) {
                    $buildName = $build->build_name . ".zip";
                    $downloadPath = $this->buildDownloadPath . "/" . $buildName;
                    $isBuildReady = $this->checkBuildReady($downloadPath);
                    if ($isBuildReady) {
                        $this->trackDownloadRequest($build->build_id, $serialNumber, DownloadRequest::STATUS_STARTED);

                        header('Content-Type: application/zip');
                        header('Content-Disposition: attachment; filename="' . $buildName . '"');
                        header('Content-Length: ' . filesize($downloadPath));
                        readfile($downloadPath);
                        exit();
                    }
                }
            }
        } catch (\Exception $e) {
        }
    }

    public function trackCompleted($serialNumber)
    {
        try {
            $sNInfo = $this->getSerialNumberInfo($serialNumber);
            if ($sNInfo !== null) {
                $build = $this->getBuild($sNInfo->build_id);
                $this->trackDownloadRequest($build->id, $serialNumber, DownloadRequest::STATUS_COMPLETED);
            }
        } catch (\Exception $e) {
        }
    }

    private function getSerialNumberInfo($serialNumber)
    {
        $sNInfo = $this->serialNumberModel->getBySerialNumber($serialNumber);
        return $sNInfo;
    }

    private function getBuild($build_id)
    {
        $buildInfo = $this->buildModel->getById($build_id);
        return $buildInfo;
    }

    private function checkBuildReady($downloadPath)
    {
        if (@file_get_contents($downloadPath, 0, NULL, 0, 1))
            return true;
        return false;
    }

    private function trackDownloadRequest($build, $serialNumber, $status)
    {
        $status = $this->downloadRequestModel->track($build, $serialNumber, $status);
        return $status;
    }
}