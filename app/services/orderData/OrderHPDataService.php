<?php

namespace app\services\orderData;

use \app\models\model\ModelInterface;
use app\models\orderData\OrderHPData;
use app\repositories\orderData\OrderHPDataRepository;

class OrderHPDataService {
    protected OrderHPDataRepository $repository;
    protected string $error = '';

    public static function create(OrderHPDataRepository $repository): OrderHPDataService
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

    public function fetchByOrderId($orderId): ?OrderHPData
    {
        $db = $this->repository->fetchByOrderId($orderId);
        if (!$db) return null;
        return new OrderHPData($db->getDataArray());
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }
}
