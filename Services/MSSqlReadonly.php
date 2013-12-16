<?php

/**
 *
 * @author    Thomas Lundquist <thomasez@redpill-linpro.com>
 * @copyright 2012 Thomas Lundquist
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 *
 * Warning: This is pretty untested, the only I've tested with is
 * findOneByKeyVal, but it should work.
 *
 */

namespace RedpillLinpro\NosqlBundle\Services;

class MSSqlReadonly implements ServiceInterfaceReadonly
{

    private $connection;

    public function __construct($dbhost, $dbport = 1433, $dbname, $dbuser, $dbpasswd)
    {
        // Connect to mssql server 
        $this->connection = mssql_connect($dbhost . ":" . $dbport, $dbuser, $dbpasswd);
        // Select a database 
        $db = mssql_select_db($dbname, $this->connection);
    }

    public function findOneById($table, $id_key, $id, $params = array())
    {

        if (is_int($id)) {
            $sql = 'SELECT * from '.$table .' WHERE '.$id_key."=" . $id . ";";
        } else {
            $sql = 'SELECT * from '.$table .' WHERE '.$id_key."='" . $id . "';";
        }
        // Nicked from
        // http://stackoverflow.com/questions/3252651/how-do-you-escape-quotes-in-a-sql-query-using-php
        // But not used.
        // $escaped_sql = str_replace("'", "''", $sql);
        $result = mssql_query($sql, $this->connection);

        // No iteration, we'll pick the first one.
        $data = mssql_fetch_array($result);
        mssql_free_result($result);
        return $data;

    }
    
    public function findOneByKeyVal($table, $key, $val, $params = array())
    {
        if (is_string($val)) {
            $value = mb_convert_encoding($val, "ISO-8859-1");
        } else {
            $value = $val;
        }

        // This must be a hack.. Need to fix it somehow.
        if ($key == "End") { 
            $key = '[End]'; 
        }

        if (is_int($val)) {
            $sql = 'SELECT * from '.$table .' WHERE '.$key."=" . $val . ";";
        } else {
            $sql = 'SELECT * from '.$table .' WHERE '.$key."='" . $val . "';";
        }

        // Nicked from
        // http://stackoverflow.com/questions/3252651/how-do-you-escape-quotes-in-a-sql-query-using-php
        // But not used.
        // $escaped_sql = str_replace("'", "''", $sql);
        $result = mssql_query($sql, $this->connection);

        // No iteration, we'll pick the first one.
        $data = mssql_fetch_array($result);
        mssql_free_result($result);

        return $data;
    }
    
    public function findByKeyVal($table, $key, $val, $params = array())
    {
        if (is_string($val)) {
            $value = mb_convert_encoding($val, "ISO-8859-1");
        } else {
            $value = $val;
        }

        // This must be a hack.. Need to fix it somehow.
        if ($key == "End") { 
            $key = '[End]'; 
        }

        if (is_int($val)) {
            $sql = 'SELECT * from '.$table .' WHERE '.$key."=" . $val . ";";
        } else {
            $sql = 'SELECT * from '.$table .' WHERE '.$key."='" . $val . "';";
        }
        // Nicked from
        // http://stackoverflow.com/questions/3252651/how-do-you-escape-quotes-in-a-sql-query-using-php
        $escaped_sql = str_replace("'", "''", $sql);
        $result = mssql_query($sql, $this->connection);

        $data = array();
        while ($row = mssql_fetch_array($result)) {
            $data[] = $row;
        }
        mssql_free_result($result);

        return $data;
    }

    public function findAll($table, $params = array())
    {

        $sql = 'SELECT * from '.$table .';';
        $result = mssql_query($sql, $this->connection);

        // No iteration, we'll pick the first one.
        $data = array();
        while ($row = mssql_fetch_array($result)) {
            $data[] = $row;
        }
        mssql_free_result($result);

        return $data;
    }
    

}
