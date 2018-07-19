<?php
namespace src\sql;
class sticker extends LINEbotSQL {
    function get(&$sourceId, &$stickerId) {
        $info = [
            "id" => -1,
            "count" => 0
        ];
        if($q = $this->query("SELECT `id`,`count` FROM `sticker_use` WHERE `sourceId` LIKE '$sourceId' AND `stickerId` = '$stickerId'")) {
            if($q->getCount() == 1) {
                $info = $q->select()[0];
            }
        }
        //file_put_contents("./SQL_test", json_encode($info, JSON_UNESCAPED_SLASHES) . "\n", FILE_APPEND);
        return $info;
    }
    function insert(&$sourceType, &$sourceId, &$packageId, &$stickerId) {
        $this->query("INSERT INTO `sticker_use` (`sourceType`, `sourceId`, `packageId`, `stickerId`) VALUES ('$sourceType', '$sourceId', '$packageId', '$stickerId')");
    }
    function add(&$id, $count) {
        $this->query("UPDATE `sticker_use` SET `count` = '$count' WHERE `sticker_use`.`id` = '$id'");
    }
}
?>