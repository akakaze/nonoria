<?php
namespace src\autoload;
class loading {
    public static function autoload($className)
    {
       $fileName = str_replace("\\", DIRECTORY_SEPARATOR,  DIR . "\\". $className) . ".php";
       if (is_file($fileName)) {
            require $fileName;
       }
    }
}