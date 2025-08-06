<?php
namespace app\lib;

class TelegramBot {

    const BOT_ID = '###';
    const CHAT_IDS = [
        'TEST_CHAT_ID' => '###'
    ];

    public static function sendMessage($chatId, $message, $isHTML = 0) {
        if (Config::IS_DEV) $chatId = self::CHAT_IDS['TEST_CHAT_ID'];
        if (Config::IS_DEV && $chatId != self::CHAT_IDS['TEST_CHAT_ID']) return [];
//    error_reporting(E_ALL & ~E_WARNING);
        error_reporting(E_ALL & ~(E_WARNING|E_NOTICE));
//        $chatId = self::CHAT_IDS['TEST_CHAT_ID'];
//        $data = array ('chatId' => $chatId, 'message' => $message, 'isHTML' => $isHTML);
//        $data = http_build_query($data);
        $sendMessageURL = 'https://api.telegram.org/bot' . self::BOT_ID
            . '/sendMessage?chat_id=' . $chatId . '&text=' . urlencode($message)
            . ($isHTML ? '&parse_mode=HTML' : '');
        $ctx = stream_context_create([
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ]
        ]);
        $response = file_get_contents($sendMessageURL, 0, $ctx);
        $sendResult = json_decode($response, true);
        error_reporting(E_ALL);
        return $sendResult;
    }

    public static function info() {
//        $ctx = stream_context_create(array('http' => array('timeout' => 5)));
        $ctx = stream_context_create([
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ]
        ]);

        $URL = 'https://api.telegram.org/bot' . self::BOT_ID . '/getUpdates';

        return json_decode(file_get_contents($URL, 0, $ctx), true);
    }
}