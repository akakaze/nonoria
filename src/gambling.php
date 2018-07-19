<?php
namespace src;
class gambling {
    private $gambling_dir;
    private $gambling_file;

    public function __construct(&$groupId, &$type) {
        $this->gambling_dir = __DIR__."/gambling/{$groupId}";
        $this->gambling_file = __DIR__."/gambling/{$groupId}/{$type}.json";
    }
    public function isGamblingExists() {
        return file_exists($this->gambling_file);
    }
    public function createGambling() {
        if(!is_dir($this->gambling_dir)) mkdir($this->gambling_dir, 0755, true);
        file_put_contents($this->gambling_file, "{}");
        // $this->gambling = [
        //     "players" => [],
        //     "bestPlayer" => NULL,
        //     "BGPlayer" => [],
        //     "5timesPlayer" => []
        // ];
        // $this->players = $this->gambling["players"];
    }
    public function addPlayer(&$userId) {
        file_put_contents($this->gambling_file, $userId." ");
    }
    public function getPlayerList() {
        $gamblingText = file_get_contents($this->gambling_file);
        $playerList = trim($gamblingText);
        $playerList = explode(" ", $playerList);
        return $playerList;
    }
    public function disband() {
        unlink($this->gambling_file);
    }
    // public function getPlayers() {
    //     return array_keys($this->players);
    // }
    // public function updateScore(&$userId, &$score) {
    //     $player = $this->players[$userId];
    //     $player["score"] = $score;
    //     $player["times"] ++;
    //     $bestPlayer = $this->gambling["bestPlayer"];
    //     if(!is_null($bestPlayer) ||
    //         $player["score"] > $bestPlayer["score"] ||
    //         ($player["score"] == $bestPlayer["score"] && $player["time"] < $bestPlayer["time"])
    //     ) {
    //         $bestPlayer = $player;
    //     }
    //     if($player["score"] == 3) {
    //         $this->gambling["BGPlayer"][] = $player;
    //     }
    //     if($player["time"] == 5) {
    //         $this->gambling["5timesPlayer"][] = $player;
    //     }
    // }
    // public function getGambling(&$groupId) {
    //     if(file_exists($this->gambling_file)) {
    //         $gamblingText = file_get_contents($this->gambling_file);
    //         $this->gambling_data = json_decode($gamblingText, true);
    //     }
    //     else {
    //         if(!is_dir($this->gambling_dir)) mkdir($this->gambling_dir);
    //     }
    // }
}
?>