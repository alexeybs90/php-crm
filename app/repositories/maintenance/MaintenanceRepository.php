<?php

namespace app\repositories\maintenance;

use app\models\Maintenance;
use app\models\model\ModelInterface;
use app\repositories\model\ModelRepository;

class MaintenanceRepository extends ModelRepository
{
    public function table(ModelInterface $model = null): string
    {
        return 'maintenance';
    }

    public function createModel($data = []): ModelInterface {
        return new Maintenance($data);
    }

    public function fetchListByEquipmentId($equipment_id): array
    {
        return $this->fetchList("SELECT * FROM " . $this->table() . " WHERE equipment_id=:equipment_id", [
            'equipment_id' => $equipment_id
        ]);
    }

    public function completeItem(Maintenance $model): bool
    {
        $this->error = 'error: ' . $model->id . ' ' . $model->name;
        return false;
    }
}