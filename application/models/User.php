<?php
/**
 * User Model
 *
 * @Author: Krishna
 * Date: 08-02-2018
 */

namespace Application\Models;

class User extends \Application\Core\Model
{
    const TABLE = "users";

    function __construct()
    {
        $this->table = self::TABLE;
        parent::__construct();
    }

    public function getUsers()
    {
        $users = $this->selectAllBySql("select * from $this->table");
        return $users;
    }
}