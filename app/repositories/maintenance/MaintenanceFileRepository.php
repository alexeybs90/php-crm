<?php

namespace app\repositories\maintenance;

use app\models\MaintenanceFile;
use app\models\model\ModelInterface;
use app\repositories\model\ModelRepository;

class MaintenanceFileRepository extends ModelRepository
{
    public function table(ModelInterface $model = null): string
    {
        return 'maintenance_files';
    }

    public function createModel($data = []): ModelInterface {
        return new MaintenanceFile($data);
    }

    public function fetchListByMaintenanceIds(array $ids = []): array
    {
        $aliases = array_map(fn($a) => '?', $ids);
        $files = [];
        $query = "SELECT * FROM {$this->table()} WHERE parent_id IN (" . implode(', ', $aliases) . ")";
        try {
            $statement = $this->db->prepare($query);
            $statement->execute($ids);
            if ($statement->rowCount()) {
                $files = $statement->fetchAll(\PDO::FETCH_ASSOC);
            }
        } catch (\PDOException $e) {
            $this->error = 'SQL error: ' . $e->getMessage() . ' QUERY: ' . $query;
        }
        return $files;
    }

    public function fetchCountFiles(int $parent_id): int
    {
        try {
            $statement = $this->db->prepare("SELECT COUNT(id) AS num FROM {$this->table()} WHERE parent_id=:id");
            $statement->execute(['id' => $parent_id]);
            if ($statement->rowCount()) {
                $file = $statement->fetch(\PDO::FETCH_ASSOC);
                return (int)$file['num'];
            }
        } catch (\PDOException $e) {
            $this->error = 'SQL error: ' . $e->getMessage() . ' QUERY: ' . $query;
        }
        return 0;
    }

    public function saveFile(MaintenanceFile $model): bool
    {
        $query = "INSERT INTO {$this->table()} (parent_id, file, pos, name, r_name, created_at)"
            . " VALUES (:parent_id, :file, :pos, :name, :r_name, NOW())";
        try {
            $statement = $this->db->prepare($query);
            $statement->execute([
                'parent_id' => $model->parent_id,
                'file' => $model->file,
                'pos' => $model->pos,
                'name' => $model->name,
                'r_name' => $model->r_name,
            ]);
            return true;
        } catch (\PDOException $e) {
            $this->error = 'SQL error: ' . $e->getMessage() . ' QUERY: ' . $query;
        }
        return false;
    }
}