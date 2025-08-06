<?php
namespace app\models\orderData;

use app\models\model\Model;

abstract class OrderData extends Model
{
    public string|int $order_id = 0;

    public function pk(): string
    {
        return 'order_id';
    }

    function validate(): bool
    {
        if (!$this->order_id) return false;
        return true;
    }
}