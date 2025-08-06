<?php

namespace app\services\orderData;

use \app\models\model\ModelInterface;
use app\models\orderData\OrderOffsetData;
use app\repositories\orderData\OrderOffsetDataRepository;

class OrderOffsetDataService {
    protected OrderOffsetDataRepository $repository;
    protected string $error = '';

    public static function create(OrderOffsetDataRepository $repository): OrderOffsetDataService
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

    public function fetchByOrderId($orderId): ?OrderOffsetData
    {
        $db = $this->repository->fetchByOrderId($orderId);
        if (!$db) return null;
        return new OrderOffsetData($db->getDataArray());
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }
}
