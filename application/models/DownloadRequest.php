<?php
/**
 * DownloadRequest Model
 *
 * @Author: Krishna
 * Date: 08-02-2018
 */

namespace Application\Models;

class DownloadRequest extends \Application\Core\Model
{
    const TABLE = "download_requests";

    const STATUS_STARTED = "STARTED";
    const STATUS_COMPLETED = "COMPLETED";

    function __construct()
    {
        $this->table = self::TABLE;
        parent::__construct();
    }

    public function getList()
    {
        $list = $this->selectAllBySql("select * from {$this->table}");
        return $list;
    }

    public function track($build, $serialNumber, $status)
    {
        $record = $this->selectOneBySql("select * from {$this->table} where build_id = $build and serial_number = '$serialNumber'");
        if ($record == null) {
            $this->insert(array('build_id' => $build, 'serial_number' => $serialNumber, 'status' => $status));
        } else {
            $this->update(array('id' => $record->id, 'build_id' => $build, 'serial_number' => $serialNumber, 'status' => $status));
        }
        return true;
    }
}