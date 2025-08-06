<?php
namespace app\lib;

class Autoloader
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            if (str_contains($class, "\\")) {
                $class_path = str_replace('\\', '/', $class);
                $file =  $_SERVER["DOCUMENT_ROOT"] . '/../../' . $class_path . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    return true;
                }
                return false;
            }
            return false;
        });
    }
}
Autoloader::register();
