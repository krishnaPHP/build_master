<?php
/**
 * Build Model
 *
 * @Author: Krishna
 * Date: 08-02-2018
 */

namespace Application\Models;

class Build extends \Application\Core\Model
{
    const TABLE = "builds";

    function __construct()
    {
        $this->table = self::TABLE;
        parent::__construct();
    }

    public function getAll()
    {
        $list = $this->selectAllBySql("select * from {$this->table} order by build_name");
        return $list;
    }

    public function getById($id)
    {
        $record = $this->selectByPk($id);
        return $record;
    }
}