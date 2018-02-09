<?php
/**
 * User Model
 *
 * @Author: Krishna
 * Date: 08-02-2018
 */

namespace Application\Models;

class Build extends \Application\Core\Model
{
    const TABLE = "build";

    function __construct()
    {
        $this->table = self::TABLE;
        parent::__construct();
    }

    public function getList()
    {
        $list = $this->selectAllBySql("select * from $this->table");
        return $list;
    }
}