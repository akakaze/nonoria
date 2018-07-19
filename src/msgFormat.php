<?php
namespace src;
class msgFormat {
    private $msg = [
        "reply" => [],
        "push" => []
    ];
    protected $timestamp;

    public function getContent() {
        return $this->msg;
    }
    protected function text($v, $type = "reply") {
        $this->msg[$type] = [
            "type" => "text",
            "text" => $v
        ];
    }
    protected function sticker($pkgId, $stkId, $type = "reply") {
        $this->msg[$type] = [
            "type" => "sticker",
            "packageId" => $pkgId,
            "stickerId" => $stkId
        ];
    }
    protected function image($v, $type = "reply") {
        $url = "https://akakaze.wartw.top/LINEbot/nonoria/src/img.php?n=$v";
        $this->msg[$type] = [
            "type"              => "image",
            "originalContentUrl"=> $url,
            "previewImageUrl"   => $url
        ];
    }
    protected function flex($altText, $contents, $type = "reply") {
        $this->msg[$type] = [  
            "type" => "flex",
            "altText" => $altText,
            "contents" => $contents
        ];
    }
    protected function cleanMsg() {
        $this->msg = [];
    }
}
?>