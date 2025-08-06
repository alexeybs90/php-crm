<?php

namespace app\repositories\orderData;

use app\models\orderData\OrderOffsetData;
use app\models\model\ModelInterface;
use app\repositories\model\ModelRepository;

class OrderOffsetDataRepository extends ModelRepository
{
    public function table(ModelInterface $model = null): string
    {
        return 'order_offset_data';
    }

    public function createModel($data = []): ModelInterface {
        return new OrderOffsetData($data);
    }

    public function fetchByOrderId($orderId): ?ModelInterface
    {
        return $this->fetchOne($orderId);
    }
}