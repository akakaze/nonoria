<?php
namespace src\sql;
class nickname extends LINEbotSQL {
    function getNickname() {
        if($q = $this->query("SELECT * FROM `nickname`")) {
            return $q->select();
        }
    }
    function addNickname(&$nn, &$n) {
        if($q = $this->query("INSERT INTO `nickname` (`nickname`, `name`) VALUES ('$nn','$n')")) {
            return true;
        }
        return false;
    }
}
?>