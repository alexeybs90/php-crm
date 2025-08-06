<?php
namespace app\modules;

use app\lib\Helper;

class UploadImg extends BasePage {

    public function doGet() {
    }

    public function doPost() {
        print $this->getFileSrc();
    }

    public function getFileSrc($params = array())
    {
        $ext = Helper::getExtByName($_FILES['file']['name']);
        $array = ['jpg', 'jpeg', 'png', 'bmp', 'gif'];
        if(!in_array(strtolower($ext), $array)) return '';
//        $fileBin = file_get_contents('php://input');
//        $fileBin = base64_decode($data);
//        $hash = substr($fileBin, 0, 8);
//        $types = array('jpeg' => "\xFF\xD8\xFF", 'gif' => 'GIF', 'png' => "\x89\x50\x4e\x47\x0d\x0a", 'bmp' => 'BM', 'psd' => '8BPS', 'swf' => 'FWS');
        /*foreach ($types as $type => $header) {
            if (strpos($hash, $header) === 0) {
                $ext = $type;
                break;
            }
        }*/

        $tmpFileId = 'img_' . uniqid(rand(1,10000),true);
        $path = "upload/upload_img/" . $tmpFileId . "." . $ext;

        move_uploaded_file($_FILES['file']['tmp_name'], $path);

//        $fp = fopen(Application::$document_root . $path, "wb");
//        fwrite($fp, $fileBin);
//        fclose($fp);

        return '/' . $path;
    }
}