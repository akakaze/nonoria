<?php
namespace src\sql;
class hortensia extends LINEbotSQL {
    private $q;
    function __construct() {
        parent::__construct();
        $this->q = $this->query("SELECT * FROM `hortensia`");
    }
    function getMember() {
        if($this->q){
            return $this->q->select();
        }
        return false;
    }
    function getMemberCount() {
        return $this->q->getCount();
    }
}
?>