<?php
namespace src;
use src\sql\msgPair;
use src\sql\nickname;
use src\field;
class interactiveAlgorithm extends msgFormat {
    private $id;
    private $repeatList = [];
    
    public function __construct(&$ts, &$s) {
        $this->timestamp = $ts;
        if($s["type"] == "user") {
            $this->id = $s["userId"];
        }
        else if($s["type"] == "group") {
            $this->id = $s["groupId"];
        }
        if(file_exists(__DIR__."/repeatList.json")) {
            $repeatListJSON = file_get_contents(__DIR__."/repeatList.json");
            $this->repeatList = json_decode($repeatListJSON, true);
            $this->clearScan();
        }
    }

    public function __destruct() {
        file_put_contents(__DIR__."/repeatList.json", json_encode($this->repeatList));
    }

    private function clearScan() {
        $this->repeatList = array_filter($this->repeatList, function($room_all_msg){
            $room_all_msg = array_filter($room_all_msg, function($msg) {
                return $this->timestamp - $msg["ts"] < 180000;
            });
            return count($room_all_msg) != 0;
        });
    }


    public function interactive(&$input) {
        $newRepeatCheck = true;
        if(!isset($this->repeatList[$this->id]) || !is_array($this->repeatList[$this->id])) {
            $this->repeatList[$this->id] = [];
        }
        else {
            $pairs = new msgPair();
            $matchpair = [];
            $needreply = false;
            foreach($pairs->getPair() as &$p) {
                $m = preg_split("/\r\n/", $p["pair"]);
                foreach($m as $i => &$k) {
                    if(strpos($input, $k) !== false) {
                        array_splice($m, $i, 1);
                        $matchpair[] = array_replace($p, ["pair" => $m]);
                    }
                }
            }
            foreach($this->repeatList[$this->id] as &$value) {
                if(!empty($matchpair)) {
                    foreach($matchpair as $i => &$p) {
                        foreach($p["pair"] as $j => &$k) {
                            if(strpos($value["msg"], $k) !== false) {
                                array_splice($p["pair"], $j, 1);
                            }
                        }
                        if(empty($p["pair"])) {
                            array_splice($matchpair, $i, 1);
                            if($this->timestamp - $p["timestamp"] > 300000) {
                                $this->text($p["success"]);
                                $pairs->updateTs($this->timestamp, $p["id"]);
                            }
                            else {
                                $this->text($p["wait"]);
                            }
                            $needreply = true;
                        }
                    }
                }
                
                if($input == $value["msg"]) {
                    $newRepeatCheck = false;
                    if($needreply) {
                        $value["count"] = -1;
                        continue;
                    }
                    if($value["count"] == -1) {
                        continue;
                    }
                    if($value["count"] == -2) {
                        $value["count"] = -1;
                        $this->text("不要~人家才不要跟著說這個~\n"._EMO["fishy"]);
                        $needreply = true;
                        continue;
                    }
                    $value["count"] ++;
                    $value["tl"] = $this->timestamp;
                    if($value["count"] > 2) {
                        $value["count"] = -1;
                        $this->text($input);
                        $needreply = true;
                        //$this->repeatRec();
                        continue;
                    }
                }
            }
        }
        if($newRepeatCheck) {
            $this->repeatList[$this->id][] = [
                "msg" => $input,
                "count" => 1,
                "ts" => $this->timestamp,
                "tl" => $this->timestamp
            ];
        }
        return $needreply;
    }

    public function repeatBreak() {
        if(!isset($this->repeatList[$this->id]) || !is_array($this->repeatList[$this->id])) {
            return false;
        }
        $index_for_break;
        $max_count = 0;
        $last_time = 180000;
        $nickname = new nickname();
        foreach($this->repeatList[$this->id] as $i => &$value) {
            if($value["count"] < $max_count) continue;
            if($this->timestamp - $value["tl"] > $last_time) continue;
            foreach($nickname->getNickname() as &$n) {
                if(strpos($value["msg"], $n["nickname"]) !== false) {
                    $index_for_break = $i;
                    $max_count = $value["count"];
                    $last_time = $this->timestamp - $value["tl"];
                    break;
                }
            }
        }
        if(isset($index_for_break)) {
            $msg_for_break = &$this->repeatList[$this->id][$index_for_break];
            $msg_for_break["count"] = -2;
            $msg_for_break["ts"] = $this->timestamp + 300000;
            $this->text("唔~我覺得\"" . $msg_for_break["msg"] . "\"這句話怪怪的~ヾ(`◕д◕´✿)");
            return true;
        }
        return false;
    }

    private function repeatRec() {
        $nickname = new nickname();
        foreach($nickname->getNickname() as &$n) {
            if(strpos($value["msg"], $n["nickname"]) !== false) {
                $index_for_break = $i;
                $max_count = $value["count"];
                $last_time = $this->timestamp - $value["tl"];
                break;
            }
        }
    }
}
?>