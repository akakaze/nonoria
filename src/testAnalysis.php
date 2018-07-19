<?php
namespace src;
class testAnalysis extends msgFormat {
    private $ckip_client_obj;
    public function __construct() {
        $this->ckip_client_obj = new CKIPClient(
            "140.109.19.104",
            "1501",
            "IF_akakaze",
            "!@#Kpf2016324"
        );
    }
    public function analysis($t) {
        $fp = fopen(__DIR__ . "/CKIP.token", "w");
        $start_time = time();
        $needreply = false;
        while(time() - $start_time < 10) {
            if (flock($fp, LOCK_EX)) {
                $return_text = $this->ckip_client_obj->send($t);
                $return_terms = $this->ckip_client_obj->getTerm();
                $s = "";
                foreach($return_terms as &$fragment) {
                    $s .= $fragment["term"] . " : " . $fragment["tag"] . "\n";
                }
                $this->text($s);
                $needreply = true;
                sleep(3);
                flock($fp, LOCK_UN);
                break;
            }
        }
        return $needreply;
    }
}
?>