<?php

namespace app\services\call;

class CallService implements PhoneCallInterface
{
    const KEY = '###';
    const CAMPAIGN_ID = 9999999;
    const MAX_CALL_TIME = 40;
    const URL = 'https://zvonok.com/manager/cabapi_external/api/v1/phones/call/';

    public function call(string $phone, string $text = '') : mixed
    {
        if (!trim($phone)) return [];
        if (\app\config\Config::IS_DEV) return [];
        $data = [
            'public_key' => self::KEY,
            'phone' => $phone,
            'campaign_id' => self::CAMPAIGN_ID,
            'max_call_time' => self::MAX_CALL_TIME,
            'text' => $text,
        ];
        $headers = [
            'http' => [
                'timeout' => 5,
                'method' => 'POST',
                'ignore_errors' => 1,
                'header' => "Content-type: application/x-www-form-urlencoded\r\n"
                    . "Content-Length: " . strlen(http_build_query($data)) . "\r\n",
                'content' => http_build_query($data)
            ],
        ];
        $ctx = stream_context_create($headers);
        return json_decode(file_get_contents(self::URL, false, $ctx), true);
    }

    public static function create(): PhoneCallInterface
    {
        return new self();
    }
}