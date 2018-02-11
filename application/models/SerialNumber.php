<?php
/**
 * SerialNumber Model
 *
 * @Author: Krishna
 * Date: 08-02-2018
 */

namespace Application\Models;

class SerialNumber extends \Application\Core\Model
{
    const TABLE = "serial_numbers";

    function __construct()
    {
        $this->table = self::TABLE;
        parent::__construct();
    }

    public function getAll($build = null)
    {
        $whereCondition = $build ? "where build_id = $build" : "";
        $list = $this->selectAllBySql("select * from {$this->table} $whereCondition order by build_id, centre_code");
        return $list;
    }

    public function getBySerialNumber($serialNumber)
    {
        $record = $this->selectOneBySql("select * from {$this->table} where serial_number = '$serialNumber'");
        return $record;
    }

    public function getResultsIn($centerCodes, $serialNumbers)
    {
        $sql = "select centre_code, serial_number from {$this->table} 
                  where centre_code IN($centerCodes) or serial_number IN($serialNumbers)";
        $rows = $this->selectAllBySql($sql);
        return $rows;
    }

    public function removeItems($items)
    {
        // Archive Items first
        $this->archiveItems($items);

        $itemsCommaSeparated = implode(',', $items);
        $query = "delete from {$this->table} where id in($itemsCommaSeparated)";

        $result = $this->db->query($query);
        return $result;
    }

    private function archiveItems($items)
    {
        $itemsCommaSeparated = implode(',', $items);
        $query = "insert into `serial_numbers_archive` 
                    (`build_id`, `centre_code`, `serial_number`, `exam_engine_admin`, `exam_engine_pwd`) 
                    select `build_id`, `centre_code`, `serial_number`, `exam_engine_admin`, `exam_engine_pwd` 
                    from {$this->table} where id in($itemsCommaSeparated)";

        $result = $this->db->query($query);
        return $result;
    }

}