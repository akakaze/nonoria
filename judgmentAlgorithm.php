<?php
namespace src;
use src\sql\someThing;
use src\sql\hortensia;
class judgmentAlgorithm extends msgFormat {

    function __construct(&$ts) {
        $this->timestamp = $ts;
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

    // private function crusaderVote() {
    //     $this->msg[] = [
    //         "type"      => "template",
    //         "altText"   => "大十字的投票~用手機才能投呦~ ヽ(`◕ω◕´✿)",
    //         "template"  => [
    //             "type"              => "buttons",
    //             "thumbnailImageUrl" => "https://example.com/bot/images/image.jpg",
    //             "title"             => "Menu",
    //             "text"              => "Please select",
    //             "actions"           => [
    //                 [
    //                     "type"  => "postback",
    //                     "label" => "Buy",
    //                     "data"  => "action=buy&itemid=123"
    //                 ],
    //                 [
    //                     "type"  => "postback",
    //                     "label" => "Add to cart",
    //                     "data"  => "action=add&itemid=123"
    //                 ],
    //                 [
    //                     "type"  => "uri",
    //                     "label" => "View detail",
    //                     "uri"   => "http://example.com/page/123"
    //                 ]
    //             ]
    //         ]
    //     ];
    // }

    private function dice() {
        $args = func_get_args();
        $side = 6;
        $count = 3;
        $sum = 0;
        $rec = [];
        $print = "";

        if((is_numeric($args[0]) && (int)$args[0] > 1 && (int)$args[0] <= 100)
        && (is_numeric($args[1]) && (int)$args[1] > 0 && (int)$args[1] <= 10)) {
            $side = (int)$args[0];
            $count = (int)$args[1];
        }
        else {
            $print .= "諾諾這邊只有2~100面、最多10顆的骰子喔ヽ(´◕ω◕`✿)不然，讓人家先用6面的3顆丟看看～\n\n";
        }
        $print .= "擲了%d面骰%d顆\n擲出了%s";
        if($count > 1)
            $print .= "\n總和是%d";
        $print .= "呦~ ヽ(`◕ω◕´✿)";
        for($i = 0; $i < $count; $i ++) {
            $rec[] = rand(1, $side);
        }
        if($count > 1) {
            $sum = array_sum($rec);
        }
        $rec = implode(", ", $rec);
        $print = sprintf($print, $side, $count, $rec, $sum);
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

    // private function sleep() {
    //     sleep(3);
    //     $pid = pcntl_fork(); //在這裡開始產生程式的分岔
    //     if ($pid == -1) {
    //         die('無法產生子程序');
    //     } else if ($pid) {
    //         return true;
    //     } else {
    //         $this->text("醒了！");
    //     }
    //     //$this->text("醒了！");
    //     // return true;
    // }

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
}

class ImageAnalysis extends msgFormat {

}