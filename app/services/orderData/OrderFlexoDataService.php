<?php

namespace app\services\orderData;

use \app\models\model\ModelInterface;
use app\models\orderData\OrderFlexoData;
use app\models\orderData\OrderHPData;
use app\repositories\orderData\OrderFlexoDataRepository;
use app\repositories\orderData\OrderHPDataRepository;

class OrderFlexoDataService {
    protected OrderFlexoDataRepository $repository;
    protected string $error = '';

    public static function create(OrderFlexoDataRepository $repository): OrderFlexoDataService
    {
        $s = new static();
        $s->repository = $repository;
        return $s;
    }

    public function saveItem(ModelInterface $model): bool
    {
        if (!$model->validate()) {
            return false;
        }
        $result = $this->repository->upsert($model);
        $this->error = $this->repository->getError();
        return $result;
    }

    public function fetchByOrderId($orderId): ?OrderFlexoData
    {
        $db = $this->repository->fetchByOrderId($orderId);
        if (!$db) return null;
        return new OrderFlexoData($db->getDataArray());
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }
}
