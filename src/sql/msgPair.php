<?php
namespace src\sql;
class msgPair extends LINEbotSQL {
    function getPair() {
        if($q = $this->query("SELECT * FROM `msg_pair`")) {
            return $q->select();
        }
    }
    function updateTs(&$ts, &$id) {
        $this->query("UPDATE `msg_pair` SET `timestamp` = '$ts' WHERE `msg_pair`.`id` = $id");
    }
}
?>