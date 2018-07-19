<?php
namespace src;
use src\sql\someThing;
use src\sql\hortensia;
use src\sepat;
use src\field;
class judgmentAlgorithm extends msgFormat {
    private $timestamp;
    private $source;

    function __construct(&$ts, &$source) {
        $this->timestamp = $ts;
        $this->source = $source;
    }

    public function callCommand($input) {
        $cmdListJSON = file_get_contents(__DIR__."/commandList.json");
        $cmdList = json_decode($cmdListJSON, true);
        $cmdAll = explode(" ", $input);
        $cmdStr = mb_substr($cmdAll[0], 1, null, "UTF-8");
        $arg = array_slice($cmdAll, 1);
        $cmd = &$cmdList[$cmdStr];

        if(isset($cmd)) {
            return call_user_func_array([$this, $cmd], $arg);
        }
        return false;
    }

    private function dice() {
        $args = func_get_args();
        $face = 6;
        $amount = 3;
        $sum = 0;
        $rec = [];
        $defultcheck = false;

        if((is_numeric($args[0]) && (int)$args[0] > 1 && (int)$args[0] <= 100)
        && (is_numeric($args[1]) && (int)$args[1] > 0 && (int)$args[1] <= 10)) {
            $face = (int)$args[0];
            $amount = (int)$args[1];
        }
        else {
            $defultcheck = true;
        }
        for($i = 0; $i < $count; $i ++) {
            $rec[] = rand(1, $side);
        }
        $sum = array_sum($rec);


        $msgBuilder = new msgBuilder();
        $msgBuilder->addTag("face", $face);
        $msgBuilder->addTag("amount", $amount);
        $msgBuilder->addTag("sum", $sum);
        if($defultcheck) 
            $msgBuilder->addPrintText(_DICE["defult"]);
        $msgBuilder->addPrintText(_DICE["start"]);
        if($count > 1)
            $msgBuilder->addPrintText(_DICE["sum"]);
        $msgBuilder->addPrintText(_DICE["final"]);
        $msgBuilder->setEMO("defult");
        $print = $msgBuilder->buildeMsg();
        $this->text($print);
        return true;
    }

    private function doSomeThing() {
        $do = new someThing();
        $thing = $do->getSentance();
        $info = &$thing[array_rand($thing)];
        $r2;
        $id = $info["id"];
        $sen;
        $print;
        $name;

        if($this->timestamp - $info["timestamp"] > 300000) {
            $r2 = rand(0, 1);
            $sen = $info[$r2+1];
            $member = new hortensia();
            $qn = $member->getMember();
            $rn = rand(0, $member->getMemberCount()-1);
            $name = $qn[$rn]["name"];
            $do->updateSentence($this->timestamp, $r2, $name, $id);
        }
        else {
            $r2 = $info["goodorbad"];
            $sen = $info[$r2+3];
            $name = $info["lastname"];
        }
        $print = sprintf($sen, $name, $name);
        $this->text($print);
        return true;
    }
    
    private function sepatGambling() {
        $groupId = $this->source["groupId"];
        $userId = $this->source["userId"];
        $sepat = new sepat($groupId);
        if(!$sepat->isGamblingExists()) {
            $sepat->createGambling();
        }
        $sepat->addPlayer($userId);
        
        return true;
    }
    private function sepatGamblingStart() {
        $groupId = $this->source["groupId"];
        $sepat = new sepat($groupId);
        $msgBuilder = new msgBuilder();
        
        if(!$sepat->isGamblingExists()) {
            $playerList = $sepat->getPlayerList();
            $scoreboard = [
                "player" => [],
                "bestScore" => 4,
                "bestPlayer" => [],
                "BGPlayer" => [],
                "5timesPlayer" => []
            ];
            foreach ($playerList as $playerId) {
                $name = "";//nickname->getNickName($userId)
                $dices = "";
                $scoreboard["player"][$playerId] = [
                    "name" => $name,
                    "time" => 0,
                    "score" => 0
                ];
                $time = &$scoreboard["player"][$playerId]["time"];
                $score = &$scoreboard["player"][$playerId]["score"];
                do {
                    $bowl = $sepat->dice();
                    $result = $sepat->getScore();
                    $time ++;
                    $dices = implode(", ", $bowl);
                    $score = $result["score"];

                    $msgBuilder->clearPrintText();
                    $msgBuilder->addTag("name", $name);
                    $msgBuilder->addTag("time", $time);
                    $msgBuilder->addTag("dices", $dices);
                    $msgBuilder->addTag("score", $score);
                    $msgBuilder->addPrintText(_SEPAT["start"]);
                    switch($result["type"]) {
                        case sepat::_SAME_COLOR:
                            $msgBuilder->addPrintText(_SEPAT["same_color"]);
                            $msgBuilder->setEMO("ecstatic");
                        break;
                        case sepat::_THREE_SAME:
                            if($result["pitty"] == true)
                                $msgBuilder->addPrintText(_SEPAT["pitty"]);
                            else
                                $msgBuilder->addPrintText(_SEPAT["d'count"]);
                            $msgBuilder->setEMO("beyond");
                        break;
                        case sepat::_FOUR_DIFF:
                            $msgBuilder->addPrintText(_SEPAT["four_diff"]);
                            $msgBuilder->setEMO("beyond");
                        break;
                        case sepat::_TWO_PAIR:
                        case sepat::_ONE_PAIR:
                            if($result["score"] == 12) {
                                $msgBuilder->addPrintText(_SEPAT["sepata"]);
                                $msgBuilder->setEMO("pleasant");
                            }
                            else if($result["score"] == 3) {
                                $msgBuilder->addPrintText(_SEPAT["BG"]);
                                $msgBuilder->setEMO("sad");
                            }
                            else {
                                $msgBuilder->addPrintText(_SEPAT["get_score"]);
                                $msgBuilder->setEMO("defult");
                            }
                        break;
                    }
                    $print = $msgBuilder->buildeMsg();
                    $this->text($print, "push");
                } while ($result["score"] == 0);
                
                if($score > $scoreboard["bestScore"]) {
                    $scoreboard["bestPlayer"] = [];
                    $scoreboard["bestScore"] = $score;
                }
                if($score == $scoreboard["bestScore"]) {
                    $scoreboard["bestPlayer"][] = $name;
                }
                if($score == 3) {
                    $scoreboard["BGPlayer"][] = $name;
                    continue;
                }
                if($time >= 5) {
                    $scoreboard["5timesPlayer"][] = $name;
                }
            }
            $flexContent = [
                "type" => "bubble",
                "header" => [
                    "type" => "text",
                    "text" => "記分板",
                    "size" => "lg",
                    "align" => "center",
                    "weight" => "bold"
                ]
            ];
            $body = [
                "type" => "box",
                "layout" => "vertical",
                "contents" => [
                    [
                        "type" => "box",
                        "layout" => "horizontal",
                        "contents" => [
                            [
                                "type" => "text",
                                "text" => "玩家",
                                "align" => "center"
                            ],[
                                "type" => "text",
                                "text" => "次數",
                                "align" => "center"
                            ],[
                                "type" => "text",
                                "text" => "得分",
                                "align" => "center"
                            ]
                        ]
                    ]
                ]
            ];
        }
    }
/*
    private function sleep() {
        sleep(3);
        $pid = pcntl_fork(); //在這裡開始產生程式的分岔
        if ($pid == -1) {
            die('無法產生子程序');
        } else if ($pid) {
            return true;
        } else {
            $this->text("醒了！");
        }
        //$this->text("醒了！");
        // return true;
    }

    private function pos() {
        $db = new \SQLite3("./src/output.sqlite");

        $result = $db->query("SELECT DISTINCT(pos_13pos), pos_cpos FROM cwn_pos");
        // $result = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
        $s = "";
        while($r = $result->fetchArray()) {
            $s .= $r["pos_13pos"] . " : " . $r["pos_cpos"] . "\n";
        }
        $this->text($s);
        return true;
    }

    private function crusaderVote() {
        $this->msg[] = [
            "type"      => "template",
            "altText"   => "大十字的投票~用手機才能投呦~ ヽ(`◕ω◕´✿)",
            "template"  => [
                "type"              => "buttons",
                "thumbnailImageUrl" => "https://example.com/bot/images/image.jpg",
                "title"             => "Menu",
                "text"              => "Please select",
                "actions"           => [
                    [
                        "type"  => "postback",
                        "label" => "Buy",
                        "data"  => "action=buy&itemid=123"
                    ],
                    [
                        "type"  => "postback",
                        "label" => "Add to cart",
                        "data"  => "action=add&itemid=123"
                    ],
                    [
                        "type"  => "uri",
                        "label" => "View detail",
                        "uri"   => "http://example.com/page/123"
                    ]
                ]
            ]
        ];
    }
*/
}

class msgBuilder {
    private $table = [];
    private $msg = "";
    private $emo = "defult";
    public function addTag($tag, $value) {
        $this->table[":{$tag}"] = $value;
    }
    public function addPrintText($p) {
        $this->msg .= $p;
    }
    public function clearPrintText() {
        $this->msg .= "";
    }
    public function setEMO($e) {
        $this->emo = $e;
    }
    private function buildeMsg() {
        $search = array_keys($this->table);
        $replace = array_values($this->table);
        $print = str_replace($search, $replace, $this->msg);
        $print .= "\n"._EMO[$this->emo];
        return $print;
    }
}