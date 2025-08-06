<?php
namespace app\modules;

class BarCodeImage extends BasePage {

    public function doPost() {
    }

    public function doGet() {
        ob_end_clean();
        $text = $_GET['text'] ?? '';
        header('Content-Type: image/png');

        $generatorHTML = new Picqer\Barcode\BarcodeGeneratorPNG();
        echo $generatorHTML->getBarcode($text, $generatorHTML::TYPE_CODE_128);
        print ob_get_clean();
    }
}