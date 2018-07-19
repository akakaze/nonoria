<?php
namespace src\sql;
class someThing extends LINEbotSQL {
    function getSentance() {
        if($q = $this->query("SELECT * FROM `do_some_thing`")) {
            return $q->select();
        }
    }
    function updateSentence(&$ts, &$r2, &$name, &$id) {
        $this->query("UPDATE `do_some_thing` SET `timestamp` = '$ts', `goodorbad` = '$r2', `lastname` = '$name' WHERE `do_some_thing`.`id` = $id");
    }
}
?>