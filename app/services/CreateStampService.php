<?php

namespace app\services;

class CreateStampService
{
    public static function create(): CreateStampService
    {
        return new self();
    }

    public function getItems(): array
    {
        $params = [];
        $params['where'] = ' AND z.with_create_stamp=1';
        $params['order'] = 'date_end ASC';
        $params['is_complete_delivery'] = 0;
        $params['is_complete_print'] = 0;
        $items = \OrderHP::fetchListItems($params);
        if (!$items) $items = [];
        $itemsTest = \OrderHPTest::fetchListItems($params);
        if (!$itemsTest) $itemsTest = [];
        $itemsOS = \OrderOffset::fetchListItems($params);
        if (!$itemsOS) $itemsOS = [];
        return array_merge($items, $itemsTest, $itemsOS);
    }
}