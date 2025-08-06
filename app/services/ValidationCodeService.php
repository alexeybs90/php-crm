<?php

namespace app\services;

use app\models\ValidationCode;
use app\repositories\validationcode\ValidationCodeRepository;

class ValidationCodeService {
    protected ValidationCodeRepository $repository;
    protected string $error = '';

    public static function create(ValidationCodeRepository $repository): ValidationCodeService
    {
        $s = new static();
        $s->repository = $repository;
        return $s;
    }

    public function fetchListByParams($params = []): array
    {
        return $this->repository->fetchListByParams($params);
    }

    public function saveItem(ValidationCode $model): bool
    {
        if (!$model->validate()) {
            return false;
        }
        if ($model->pkValue()) $result = $this->repository->update($model);
        else $result = $this->repository->insert($model);
        $this->error = $this->repository->getError();
        return $result;
    }

    public function saveItemsFromPost($array = []): array
    {
        $error = '';
        $items = [];
        if ($array) {
            foreach ($array as $data) {
                $model = new ValidationCode($data);
                $ok = $this->saveItem($model);
                if (!$ok) {
                    $error .= $this->getError() . '<br>';
                }
                $items[] = $model;
            }
        }
        $this->error = $error;
        return $items;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }
}
