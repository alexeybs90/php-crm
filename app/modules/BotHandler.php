<?php
namespace app\modules;

use app\lib\Logger;
use app\lib\TelegramBot;

class BotHandler extends BasePage {

    public function doPost() {

        $content = file_get_contents("php://input");
        $update = json_decode($content, true);

        $this->handleData($update);
    }

    public function doGet() {
        $this->handleData($_GET);
    }

    public function handleData($data = [])
    {
        $logger = new Logger("upload/temp/logs/bot_log.txt");
        $logger->write("REQUEST:\n"
            . " REMOTE_ADDR: " . $_SERVER['REMOTE_ADDR'] . "\n"
            . " REMOTE_HOST: " . ($_SERVER['REMOTE_HOST'] ?? '') . "\n"
            . " REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n"
            . ' REQUEST_METHOD: ' . $_SERVER['REQUEST_METHOD'] . "\n"
            . "DATA:\n " . print_r($data, true) . "\n");

//        $content = file_get_contents("php://input");
//        $update = json_decode($content, TRUE);

        if (isset($data['message']) && isset($data['message']['text']) && isset($data['message']['chat'])) {
            $chatId = $data['message']['chat']['id'];
            $text = $data['message']['text'];

            if(mb_strtolower($text) == 'test') {
                $res = TelegramBot::sendMessage($chatId, $text);
            }
        }
        $logger->close();
    }
}