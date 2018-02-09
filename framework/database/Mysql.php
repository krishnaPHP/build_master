<?php
/**
 * Mydql Database
 */

namespace Framework\Database;

class Mysql
{
    protected $conn = false;  //DB connection resources
    protected $sql;           //sql statement

    /**
     * Constructor, to connect to database, select database and set charset
     * @param array
     */
    public function __construct($config = array())
    {
        $host = $config['host'];
        $user = $config['username'];
        $pass = $config['password'];
        $port = isset($config['port']) ? $config['port'] : '3306';

        $dbname = $config['dbname'];
        $charset = isset($config['charset']) ? $config['charset'] : 'utf8';

        $this->conn = mysql_connect("$host:$port", $user, $pass) or die('Database connection error');

        mysql_select_db($dbname) or die('Database selection error');

        $this->setChar($charset);

    }

    /**
     *
     * @param $sql string
     * @return mixed
     */
    public function query($sql)
    {
        $this->sql = $sql;
        $result = mysql_query($this->sql, $this->conn);
        if (!$result)
            die($this->errno() . ':' . $this->error() . '&lt;br />Error SQL statement is ' . $this->sql . '&lt;br />');

        return $result;
    }

    /**
     * Get the first column of the first record
     * @param $sql
     * @return string
     */
    public function getOne($sql)
    {
        $result = $this->query($sql);
        $row = mysql_fetch_row($result);
        return isset($row[0]) ? $row[0] : null;
    }

    /**
     * @param $sql
     * @return object | null
     */
    public function getRow($sql)
    {
        $result = $this->query($sql);
        $row = mysql_fetch_object($result);
        return $row;
    }

    /**
     * @param $sql
     * @return array
     */
    public function getAll($sql)
    {
        $result = $this->query($sql);

        $list = array();
        while ($row = mysql_fetch_object($result)) {
            $list[] = $row;
        }
        return $list;

    }

    /**
     * Get last insert id
     */
    public function getInsertId()
    {
        return mysql_insert_id($this->conn);
    }

    /**
     * @return string
     */
    public function errno()
    {
        return mysql_errno($this->conn);
    }

    /**
     * @return string
     */
    public function error()
    {
        return mysql_error($this->conn);
    }

    /**
     * @param $charest
     */
    private function setChar($charest)
    {
        $sql = 'set character set ' . $charest;
        $this->query($sql);
    }

}