<?php
namespace app\modules;

use app\lib\Application;
use app\lib\Helper;

class Download extends BasePage {

    public function doGet()
    {
        $file = $_GET['file'] ?? null;
        $name = $_GET['name'] ?? null;

        $filePath = Application::$document_root . $file;

        if ($file && file_exists($filePath)) {
            // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
            // если этого не сделать файл будет читаться в память полностью!
            if (ob_get_level()) {
                ob_end_clean();
            }
            if (!$name) {
                $name = Helper::getNameFile($file);
            }

            // send content:
            header("Content-Type: " . Helper::getMimeTypeByName($file));
            header("Content-Length: " . filesize($filePath));
            header("Last-Modified: ".gmdate("D, j M Y G:i:s T", time()) );
//				header("ETag: ".$etag);
            header("Accept-Ranges: bytes");
//				header('Content-Transfer-Encoding: binary');
//				header('Expires: 0');
//				header('Cache-Control: must-revalidate');
//				header('Pragma: public');
            header('Content-Disposition: attachment; filename="' . $name . '"');
            //print $image->content;
            flush();
            readfile($filePath);
            exit;
        } else {
            print 'file not found: ' . $filePath;
        }
    }
}
