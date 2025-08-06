<?php

namespace app\repositories\orderData;

use app\models\orderData\OrderHPData;
use app\models\model\ModelInterface;
use app\repositories\model\ModelRepository;

class OrderHPDataRepository extends ModelRepository
{
    public function table(ModelInterface $model = null): string
    {
        return 'order_hp_data';
    }

    public function createModel($data = []): ModelInterface {
        return new OrderHPData($data);
    }

    public function fetchByOrderId($orderId): ?ModelInterface
    {
        return $this->fetchOne($orderId);
    }
}