<?php
namespace app\modules;

use app\lib\Helper;

class MakeImage extends BasePage {

    public function doGet() {

        $file = $_GET['file'] ?? null;
        $name = $_GET['name'] ?? null;
        $width = $_GET['width'] ?? '';
        $height = $_GET['height'] ?? '';
        $cut = $_GET['cut'] ?? false;
        $align = $_GET['align'] ?? 'top';

        $filePath = $this->imgSrc($file, $width, $height, $cut, $align);
        $DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];
        if (preg_match("/^\//", $filePath) == 0) $DOCUMENT_ROOT .= "/";
        $filePath = $DOCUMENT_ROOT . $filePath;

        if ($file && $filePath && file_exists($filePath)) {
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
            header("Last-Modified: " . gmdate("D, j M Y G:i:s T", time()) );
//				header("ETag: ".$etag);
            header("Accept-Ranges: bytes");
//				header('Content-Transfer-Encoding: binary');
//				header('Expires: 0');
//				header('Cache-Control: must-revalidate');
//				header('Pragma: public');
            header('Content-Disposition: attachment; filename="' . $name . '"');
            //print $image->content;
            flush();
            @readfile($filePath);
            exit;
        }
    }
}
?>
