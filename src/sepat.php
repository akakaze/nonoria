<?php
namespace src;
use src\gambling;
class sepat extends gambling{
    const _SAME_COLOR = 0;
    const _THREE_SAME = 1;
    const _TWO_PAIR = 2;
    const _ONE_PAIR = 3;
    const _FOUR_DIFF = 4;

    private $bowl = [];
    private $pair = [];

    public function __construct(&$groupId) {
        parent::__construct($groupId, "sepat");
    }
    public function dice() {
        $this->bowl = [];
        for ($i=0; $i < 4; $i++) {
            $n = rand(1, 6);
            if(in_array($n, $this->bowl)) {
                $this->pair[] = $n;
            }
            $this->bowl[] = $n;
        }
        return $this->bowl;
    }
    public function getScore() {
        $sum = array_sum($this->bowl);
        $rs = [];
        switch (count($this->pair)) {
            case 3:
                //一色
                $rs["score"] = 13;
                $rs["type"] = self::_SAME_COLOR;
            break;
            case 2:
                if($this->pair[0] == $this->pair[1]){
                //三同
                    $rs["score"] = 0;
                    $rs["type"] = self::_THREE_SAME;
                    if($this->pair[0] == 6) $rs["pitty"] = true;
                }
                else {
                //二對
                    $ignore_number = min($this->pair);
                    $rs["score"] = $sum - 2*$ignore_number;
                    $rs["type"] = self::_TWO_PAIR;
                }
            break;
            case 1:
                //一對
                $ignore_number = $this->pair[0];
                $rs["score"] = $sum - 2*$ignore_number;
                $rs["type"] = self::_ONE_PAIR;
            break;
            default:
                //無面
                $rs["score"] = 0;
                $rs["type"] = self::_FOUR_DIFF;
            break;
        }
        return $rs;
    }
    // public function diceFinal() {

    // }
}
?>