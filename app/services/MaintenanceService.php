<?php

namespace app\services;

use app\models\Maintenance;
use app\models\MaintenanceFile;
use app\repositories\maintenance\MaintenanceFileRepository;
use app\repositories\maintenance\MaintenanceRepository;

class MaintenanceService {
    protected MaintenanceRepository $repository;
    protected MaintenanceFileService $fileService;
    protected string $error = '';

    public static function create(MaintenanceRepository $repository, MaintenanceFileRepository $fileRepository): MaintenanceService
    {
        $s = new static();
        $s->repository = $repository;
        $s->fileService = MaintenanceFileService::create($fileRepository);
        return $s;
    }

    public function fetchListByEquipmentId($equipment_id): array
    {
        $items = [];
        $maintenances = $this->repository->fetchListByEquipmentId($equipment_id);
        if ($maintenances) {
            $files = $this->fileService->fetchListByMaintenanceIds($maintenances);
            /** @var Maintenance $maintenance */
            foreach ($maintenances as $k => $maintenance) {
                $item = $maintenance->getDataArray();
                $item['files'] = [];
                $item['newFiles'] = [];
                foreach ($files as $file) {
                    if ($file['parent_id'] == $item['id']) {
                        $item['files'][] = $file;
                    }
                }
                $items[$k] = $item;
            }
        }
        $this->error = $this->repository->getError();
        return $items;
    }

    public function fetchListByParams($params = []): array
    {
        return $this->repository->fetchListByParams($params);
    }

    public function saveItem(Maintenance $model): bool
    {
        if (!$model->validate()) {
            return false;
        }
//            $model->beforeSave();
        if ($model->pkValue()) $result = $this->repository->update($model);
        else $result = $this->repository->insert($model);
//          if ($result) $this->afterSave();
        $this->error = $this->repository->getError();
        return $result;
    }

    public function completeItem(int $maintenance_id): bool
    {
        if (!$maintenance_id) {
            $this->error = 'maintenance id is empty';
            return false;
        }
        $model = $this->repository->fetchOne($maintenance_id);
        if (!$model) {
            $this->error = 'maintenance is not found';
            return false;
        }
        $result = $this->repository->completeItem($model);
        $this->error = $this->repository->getError();
        return $result;
    }

    public function saveItemsFromPost($array = []): array
    {
        $error = '';
        $items = [];
        if ($array) {
            foreach ($array as $data) {
                $model = new Maintenance($data);
                $ok = $this->saveItem($model);
                if (!$ok) {
                    $error .= $this->getError() . '<br>';
                } else {
                    $this->fileService->saveFiles($model, $data['newFiles']);
                }
                $items[] = $model;
            }
        }
        $this->error = $error;
        return $items;
    }

    public function deleteFile(int $fileId): bool
    {
        return $this->fileService->deleteFile($fileId);
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }
}
