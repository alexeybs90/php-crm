<?php
namespace app\models\orderData;

class OrderHPData extends OrderData
{
    public string|int $order_id = 0;
    public string|int $exclude_shipment_report = 0;
    public string|int $print_time = 0;
}