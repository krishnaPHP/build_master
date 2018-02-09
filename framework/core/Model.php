<?php
/**
 * Base Model Class
 *
 * @Author: Krishna
 */

namespace Framework\Core;

class Model
{
    public $db;

    public $table;

    private $fields = array();

    public function __construct()
    {
        $config = Config::getDBConfig();
        $this->db = new \Framework\Database\Mysql($config);

        $this->getFields();
    }

    /**
     * @param $list array
     * @return mixed
     */
    public function insert($list)
    {
        $field_list = '';
        $value_list = '';

        foreach ($list as $k => $v) {
            if (in_array($k, $this->fields)) {
                $field_list .= "`" . $k . "`" . ',';
                $value_list .= "'" . $v . "'" . ',';
            }
        }

        // Trim the comma on the right
        $field_list = rtrim($field_list, ',');
        $value_list = rtrim($value_list, ',');

        $sql = "insert into `{$this->table}` ({$field_list}) values ($value_list)";
        if ($this->db->query($sql))
            return $this->db->getInsertId();
        return null;
    }

    /**
     * @param $list array
     * @return mixed
     */
    public function update($list)
    {
        $uplist = '';
        $where = 0;   //update condition, default is 0

        foreach ($list as $k => $v) {
            if (in_array($k, $this->fields)) {
                if ($k == $this->fields['pk']) // If it’s PK, construct where condition
                    $where = "`$k`=$v";
                else
                    $uplist .= "`$k`='$v'" . ",";
            }
        }

        // Trim comma on the right of update list
        $uplist = rtrim($uplist, ',');

        $sql = "update `{$this->table}` set {$uplist} where {$where}";
        if ($this->db->query($sql)) {
            if ($rows = mysql_affected_rows())
                return $rows;
            return false;
        }
        return false;
    }

    /**
     * @param $pk mixed
     * @return mixed
     */
    public function delete($pk)
    {
        if (is_array($pk))
            $where = "`{$this->fields['pk']}` in (" . implode(',', $pk) . ")";
        else
            $where = "`{$this->fields['pk']}`=$pk";

        $sql = "delete from `{$this->table}` where $where";
        if ($this->db->query($sql)) {
            if ($rows = mysql_affected_rows())
                return $rows;
            return false;
        }
        return false;
    }

    /**
     * @param $pk
     * @return object
     */
    public function selectByPk($pk)
    {
        $sql = "select * from `{$this->table}` where `{$this->fields['pk']}`=$pk";
        return $this->db->getRow($sql);
    }

    /**
     * @param $sql
     * @return object
     */
    public function selectOneBySql($sql)
    {
        return $this->db->getRow($sql);
    }

    /**
     * @param $sql
     * @return array
     */
    public function selectAllBySql($sql)
    {
        return $this->db->getAll($sql);
    }

    /**
     * @return string
     */
    public function getTotal()
    {
        $sql = "select count(*) as total from {$this->table}";
        return $this->db->getOne($sql);
    }

    /**
     * @param $offset
     * @param $limit
     * @param string $where
     * @return array
     */
    public function getPageRows($offset, $limit, $where = '')
    {
        if (empty($where))
            $sql = "select * from {$this->table} limit $offset, $limit";
        else
            $sql = "select * from {$this->table} where $where limit $offset, $limit";

        return $this->db->getAll($sql);
    }

    /**
     * Get list of fields
     */
    private function getFields()
    {
        $sql = "desc " . $this->table;

        $result = $this->db->getAll($sql);
        foreach ($result as $v) {
            $this->fields[] = $v->Field;
            if ($v->Key == 'PRI') // If there is PK, save it in $pk
                $pk = $v->Field;
        }

        if (isset($pk))
            $this->fields['pk'] = $pk;
    }

}