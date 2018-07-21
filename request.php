<?php
define('DIR', dirname(__FILE__));
require DIR . '/src/autoload/loading.php';
spl_autoload_register("\\src\\autoload\\loading::autoload"); 
use src\LINEBotTiny;
use src\judgmentAlgorithm;
use src\interactiveAlgorithm;
use src\testAnalysis;
use src\sql\sticker;

$client = new LINEBotTiny(
    "1mM4Ivt98KOuWvR1wmp24HoW5C73RouIb7hCzw1eDKFuCCVWkTGYPcheVa+hstvetcq105wC8YUK4uhkbyMUfBoKaJtx0aqSmvkpybhKksDK6U1x9S1HkPG25K7A1H+3dK7bRDs8/GSrpr/0VI5v3gdB04t89/1O/w1cDnyilFU=",
    "749d0d43c321e0207538d958d562c09a"
);
foreach ($client->parseEvents() as $event) {
	file_put_contents("./log/event_test_20180713.log", "[" . date("d-M-Y H:i:s e") . "] ".json_encode($event, JSON_UNESCAPED_UNICODE)."\n", FILE_APPEND);
    if($event["type"] == "message") {
        $source = $event["source"];
        $check = false;
        $req;
        switch($event["message"]["type"]) {
            case "text":
                $text = $event["message"]["text"];
                $timestamp = $event["timestamp"];
                if($source["type"] == "group") {
                    $userId = isset($source["userId"]) ? $source["userId"] : "-1";
                    putmsg("text_20180712", [
                        $source["groupId"],
                        $userId,
                        $timestamp,
                        preg_replace("/\r?\n/", "\\n", $text)
                    ]);
                }
                /*
                else {
                    if($source["userId"] == "U9ab4918e564c4b73440b8e3388ead129") {
                        $req = new testAnalysis();
                        $check = $req->analysis($text);
                        goto end;
                    }
                }
                if($text == "!test") {
                    $client->pushMessage([
                        "to"        => "Cf45ea7ba7cb84bae903458d3184642a0",
                        "messages"  => [[
                            "type" => "text",
                            "text" => "測試測試，大家不要緊張"
                        ]]
                    ]);
                    goto end;
                }
                */
                $cmd_check = mb_substr($text, 0, 1, "UTF-8");
                if($cmd_check == "!" || $cmd_check == "！") {
                    $req = new judgmentAlgorithm($timestamp, $source);
                    $check = $req->callCommand($text);
                }
                else {
                    $req = new interactiveAlgorithm($timestamp, $source);
                    if($text == "斷") {
                        $check = $req->repeatBreak();
                    }
                    else {
                        $check = $req->interactive($text);
                    }
                }
            break;
            /*
            case "image":
                if($event["source"]["type"] == "group")
                    putmsg("image", $event["source"]["groupId"], $event["timestamp"], $event["message"]["id"]);
                if(isset($event["message"]["id"])) {
                    $id = $event["message"]["id"];
                    $cont = $client->getContent($id);
                    $file_info = new finfo(FILEINFO_MIME_TYPE);
                    $mime_type = $file_info->buffer($cont);
                    $extpos = strpos($mime_type, '/');
                    $extension = "jpg";
                    if($extpos !== false) {
                        $extension = substr($mime_type, $extpos+1);
                    }
                    file_put_contents("./testImg/$id.$extension", $cont);
                }
            break;
            */
            case "sticker":
                if (isset($event["source"]["groupId"]) && isset($event["message"]["stickerId"])) {
                    $ttt = new sticker();
                    $info = $ttt->get($event["source"]["groupId"], $event["message"]["stickerId"]);
                    if($info["id"] === -1) {
                        $ttt->insert($event["source"]["type"], $event["source"]["groupId"], $event["message"]["packageId"], $event["message"]["stickerId"]);
                    }
                    else {
                        $ttt->add($info["id"], $info["count"]+1);
                    }
                }
            break;
        }
        end:
        if($check) {
            $content = $req->getContent();
            if(!empty($content["reply"])) {
                $client->replyMessage([
                    "replyToken"    => $event["replyToken"],
                    "messages"      => $content["reply"]
                ]);
            }
            if(!empty($content["push"])) {
                foreach ($content["push"] as $pushMsg) {
                    sleep(5);
                    $client->pushMessage([
                        "to"            => $source["groupId"],
                        "messages"      => $pushMsg
                    ]);
                }
            }
        }
    }
};

function putmsg($type, $c) {
    $s = implode(" ", $c); 
    file_put_contents("./log/event_$type.log", "[" . date("d-M-Y H:i:s e") . "] $s\n", FILE_APPEND);
}
?>