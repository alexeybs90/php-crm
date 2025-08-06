<?php
namespace app\services\call;

interface PhoneCallInterface {
    public function call(string $phone, string $text = '') : mixed;
}