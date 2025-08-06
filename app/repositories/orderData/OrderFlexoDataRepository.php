<?php

namespace app\repositories\orderData;

use app\models\orderData\OrderFlexoData;
use app\models\model\ModelInterface;
use app\repositories\model\ModelRepository;

class OrderFlexoDataRepository extends ModelRepository
{
    public function table(ModelInterface $model = null): string
    {
        return 'order_flexo_data';
    }

    public function createModel($data = []): ModelInterface {
        return new OrderFlexoData($data);
    }

    public function fetchByOrderId($orderId): ?ModelInterface
    {
        return $this->fetchOne($orderId);
    }
}