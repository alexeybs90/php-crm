<?php
namespace app\models\orderData;

class OrderOffsetData extends OrderData
{
    public string|int $order_id = 0;
    public string|int $exclude_shipment_report = 0;
}