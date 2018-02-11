<?php
/**
 * SerialNumber Controller
 *
 * @Author: Krishna S
 * Date: 08-02-2018
 */

namespace Application\Controllers;


use Application\Core\Config;
use Application\Helpers\BuildHelper;

class SerialNumberController extends \Application\Core\Controller
{
    /**
     * @var \Application\Models\SerialNumber
     */
    private $model;

    /**
     * @var \Application\Models\Build
     */
    private $buildModel;

    public function __construct()
    {
        parent::__construct();

        $this->model = $this->loadModel('\Application\Models\SerialNumber');
        $this->buildModel = $this->loadModel('\Application\Models\Build');
    }

    public function index()
    {
        $view = $this->loadView('serialnumber/index');

        $view->set('list', $this->model->getAll());
        $view->render();
    }

    public function remove()
    {
        if ($this->isPost()) {
            try {
                $removeItems = $this->getPost('removeItems');
                $result = $this->model->removeItems($removeItems);
                if (!$result) {
                    // set flash message
                    //$flash->set("errorMsg", "There was an error with remove Items. Please contact admin");
                }
            } catch (\Exception $e) {
            }

            $this->redirect("serialnumber");
        }
    }

    public function upload()
    {
        $view = $this->loadView('serialnumber/form');

        if ($this->isPost()) {

            try {

                $build_id = $this->getPost('build_id');
                $build_name = $this->getPost('build_name');

                //$buildHelper = new BuildHelper();
                //$configVars = $buildHelper->getConfigVars($build_name);

                $xlsFile = $this->moveUploadedFile(Config::getConfig('upload_path'), $this->getFile('serial_numbers_xls'));
                if ($xlsFile) {

                    $data = $this->parseXlsData($xlsFile);

                    $sheetno = 0;
                    $no_of_sheets = count($data->sheets);
                    $no_of_rows = $data->sheets[0]['numRows'];
                    $no_of_cols = $data->sheets[0]['numCols'];

                    if (($no_of_cols == 0) || ($no_of_rows == 0)) {
                        $view->set('alert', array("status" => "failed", "message" => "Error in Excel file. No Records Found"));
                    } else if ($no_of_cols <> 2) {
                        $view->set('alert', array("status" => "failed", "message" => "Error in Excel file(Should have 2 columns)"));
                    } else if ($no_of_sheets > 1) {
                        $view->set('alert', array("status" => "failed", "message" => "Error in Excel file(Should not have more than one sheet)"));
                    } else {

                        $i = 0;
                        $insertedCount = 0;
                        $alreadyExistedCount = 0;
                        $duplicateInExcelCount = 0;
                        $centerCodeDuplicatedCount = 0;
                        $serialNoDuplicatedCount = 0;
                        $excelArray = array();
                        $tableResult = array();
                        $centerCodeArray = array();
                        $serialNoArray = array();
                        $centerCodeExists = array();
                        $serialNoExists = array();
                        $centerCodes = '';
                        $serialNumbers = '';
                        $combineArray = array();
                        for ($j = 2; $j <= $no_of_rows; $j++) {
                            $centerCode = '';
                            $serialNo = '';

                            if (isset($data->sheets[$sheetno]['cells'][$j][1]))
                                $centerCode = addslashes(trim($data->sheets[$sheetno]['cells'][$j][1]));
                            if (isset($data->sheets[$sheetno]['cells'][$j][2]))
                                $serialNo = addslashes(trim($data->sheets[$sheetno]['cells'][$j][2]));

                            if ($centerCode) {
                                $excelArray[$i]['CENTRE_CODE'] = $centerCode;
                                $excelArray[$i]['SERIAL_NO'] = $serialNo;

                                array_push($combineArray, $centerCode . '-' . $data->sheets[$sheetno]['cells'][$j][2]);

                                if (in_array($serialNo, $serialNoArray)) {
                                    array_push($serialNoExists, $serialNo);
                                } else {
                                    array_push($centerCodeArray, addslashes($centerCode));
                                    array_push($serialNoArray, addslashes($serialNo));
                                    if (!$centerCodes) {
                                        $centerCodes = "'" . addslashes($centerCode) . "'";
                                        $serialNumbers = "'" . addslashes($serialNo) . "'";
                                    } else {
                                        $centerCodes .= ",'" . addslashes($centerCode) . "'";
                                        $serialNumbers .= ",'" . addslashes($serialNo) . "'";
                                    }
                                }
                            }
                            $i++;
                        }

                        // Get Existing Records
                        $existingRows = $this->model->getResultsIn($centerCodes, $serialNumbers);
                        foreach ($existingRows as $exRow) {
                            $tableResult[$exRow->centre_code . "||" . $exRow->mac_id] = $exRow->centre_code . "_" . $exRow->mac_id;
                            $tableResult[$exRow->mac_id] = $exRow->centre_code;
                        }

                        $displayContent = '<table class="excel_result_table" border="1">';
                        $displayContent .= '<tr><th>ROW NO</th><th>CENTRE_CODE</th><th>SERIAL_NO</th><th>RESULT</th></tr>';

                        if (sizeof($excelArray) > 0) {
                            foreach ($excelArray as $key => $dataArray) {

                                $combinationExists = false;
                                $centerCodeEx = false;
                                $serialNoEx = false;
                                $errorValue = 0;
                                $dupicateCheckArray = array_count_values($combineArray);
                                $duplicateInExcel = false;

                                $trClass = '';
                                $msg = '';
                                if (isset($dupicateCheckArray[$dataArray['CENTRE_CODE'] . "-" . $dataArray['SERIAL_NO']]) && $dupicateCheckArray[$dataArray['CENTRE_CODE'] . "-" . $dataArray['SERIAL_NO']] > 1) {
                                    $duplicateInExcel = true;
                                    $errorValue = 6;
                                    $duplicateInExcelCount++;
                                    $trClass = 'fail-bold';
                                    $msg = 'DUPLICATE ROW IN EXCEL';
                                } else if (isset($tableResult[$dataArray['CENTRE_CODE'] . "||" . $dataArray['SERIAL_NO']])) {
                                    $combinationExists = true;
                                    $errorValue = 1;
                                    $alreadyExistedCount++;
                                    $trClass = 'fail-exists';
                                    $msg = 'ALREADY EXISTS';
                                    //$displayContent.= '<tr><td class="fail">'.$dataArray['CENTRE_CODE'].'</td><td class="fail">'.$dataArray['SERIAL_NO'].'</td><td class="fail">ALREADY EXISTS</td></tr>';
                                } /*else if (isset($tableResult[$dataArray['CENTRE_CODE']])) {
                                    $centerCodeEx = true;
                                    $errorValue = 2;
                                    $centerCodeDuplicatedCount++;
                                    $displayContent .= '<tr><td class="fail">' . $dataArray['CENTRE_CODE'] . '</td><td>' . $dataArray['SERIAL_NO'] . '</td><td class="fail">Center Code Duplicated</td></tr>';
                                } */ else if (isset($tableResult[$dataArray['SERIAL_NO']])) {
                                    $serialNoEx = true;
                                    $errorValue = 3;
                                    $serialNoDuplicatedCount++;
                                    $trClass = 'fail-sl';
                                    $msg = 'Serial NO Duplicated';
                                } /* else if(in_array($dataArray['CENTRE_CODE'],$centerCodeExists)){
                                  $centerCodeEx = true;
                                  $errorValue = 4;
                                  $centerCodeDuplicatedCount++;
                                  $displayContent.= '<tr><td class="fail">'.$dataArray['CENTRE_CODE'].'</td><td>'.$dataArray['SERIAL_NO'].'</td><td class="fail">Center Code Duplicated</td></tr>';
                                  } */ else if (in_array($dataArray['SERIAL_NO'], $serialNoExists)) {
                                    $serialNoEx = true;
                                    $errorValue = 5;
                                    $serialNoDuplicatedCount++;
                                    $trClass = 'fail-sl';
                                    $msg = 'Serial NO Duplicated';
                                    //$displayContent.= '<tr><td>'.$dataArray['CENTRE_CODE'].'</td><td class="fail">'.$dataArray['SERIAL_NO'].'</td><td class="fail">Serial NO Duplicated</td></tr>';
                                }
                                switch ($errorValue) {
                                    case 0:
                                        $insertData = array(
                                            'build_id' => $build_id,
                                            'centre_code' => $dataArray['CENTRE_CODE'],
                                            'serial_number' => $dataArray['SERIAL_NO'],
                                            'exam_engine_admin' => '',
                                            'exam_engine_pwd' => '',
                                        );
                                        $this->model->insert($insertData);
                                        $insertedCount++;
                                        break;
                                }
                                $printKey = $key + 2;
                                if ($msg)
                                    $displayContent .= '<tr class="' . $trClass . '"><td>' . $printKey . '</td><td>' . $dataArray['CENTRE_CODE'] . '</td><td>' . $dataArray['SERIAL_NO'] . '</td><td>' . $msg . '</td></tr>';
                            }
                        }

                        $displayContent .= '</table>';
                        $view->set('displayContent', $displayContent);

                        $uploadedResult = '';
                        if ($insertedCount) {
                            $uploadedResult .= '<tr><td class="success"> <b>' . $insertedCount . '</b> row(s) inserted.</td></tr>';
                        }
                        if ($alreadyExistedCount) {
                            $uploadedResult .= '<tr><td class="fail"> <b>' . $alreadyExistedCount . '</b> row(s) already exists.</td></tr>';
                        }
                        if ($duplicateInExcelCount) {
                            $uploadedResult .= '<tr><td class="fail"> <b>' . $duplicateInExcelCount . '</b> row(s) have duplicate row in excel.</td></tr>';
                        }
                        if ($centerCodeDuplicatedCount) {
                            $uploadedResult .= '<tr><td class="fail"> <b>' . $centerCodeDuplicatedCount . '</b> row(s) have duplicate center code.</td></tr>';
                        }
                        if ($serialNoDuplicatedCount) {
                            $uploadedResult .= '<tr><td class="fail"> <b>' . $serialNoDuplicatedCount . '</b> row(s) have duplicate serial no.</td></tr>';
                        }
                        $view->set('uploadedResult', $uploadedResult);

                    }

                } else
                    $view->set('alert', array("status" => "failed", "message" => "Xls file not found"));

            } catch (\Exception $e) {
                $view->set('alert', array("status" => "failed", "message" => $e->getMessage()));
            }
        }

        $view->set('builds', $this->buildModel->getAll());
        $view->render();
    }

    private function moveUploadedFile($uploadPath, $file)
    {
        if (is_dir($uploadPath)) {
            if (is_writable($uploadPath)) {
                $destination = $uploadPath . "/" . $file['name'] . "_" . time();
                if (move_uploaded_file($file['tmp_name'], $destination))
                    return $destination;
            } else {
                throw new \Exception('Directory is not writable, Please contact admin...!');
            }
        }
        throw new \Exception('Directory not found, Please create the directory!');
    }

    private function parseXlsData($file)
    {
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('CP1251');
        $data->read($file);
        return $data;
    }

}