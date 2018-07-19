<?php
namespace src\sql;
class LINEbotSQL {
    protected $mysqli;
    function __construct() {
        $this->mysqli = new \mysqli("localhost", "nwoew_akakaze", "@_AGpt;(MhnT]!y(De", "akakaze");
        /* check connection */
        if ($this->mysqli->connect_error) {
            error_log("[LINEbotSQL] Connect failed: " . $this->mysqli->connect_error);
            return;
        }
        $this->mysqli->query("SET NAMES UTF8");
    }
    function __destruct() {
        $this->mysqli->close();
    }
    protected function query($q) {
        /* Select queries return a resultset */
        if ($result = $this->mysqli->query($q)) {
            return new SQLquery($result);
        }
        error_log("[LINEbotSQL] Query failed: " . mysqli_error());
        return false;
    }
}

class SQLquery {
    private $result;
    function __construct($result) {
        $this->result = $result;
    }
    function select() {
        $req = [];
        if ($this->result->num_rows > 0) {
            while($row = $this->result->fetch_array()){
                $req[] = $row;
            }
        }
        return $req;
    }
    function getCount() {
        return $this->result->num_rows;
    }
}
?>