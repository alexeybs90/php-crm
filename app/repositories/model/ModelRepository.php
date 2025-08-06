<?php
namespace app\repositories\model;

use app\models\model\ModelInterface;
use app\models\model\Model;

class ModelRepository implements ModelRepositoryInterface
{
    protected \PDO $db;
    protected string $error = '';

    public static function create(\PDO $db): static
    {
        $s = new static();
        $s->db = $db;
        return $s;
    }

    function table(ModelInterface $model = null): string
    {
        return '';
    }

    public function fetchListByParams($params = []): array
    {
        $q = "";
        if ($params) {
            $keys = array_keys($params);
            foreach ($keys as $field) {
                if ($q) $q .= " AND ";
                $q .= "{$field}=:{$field}";
            }
            $q = " WHERE " . $q;
        }
        return $this->fetchList("SELECT * FROM " . $this->table() . $q, $params);
    }

    public function fetchList(string $query, array $params = []): array
    {
        if (!$query) $query = "SELECT * FROM " . $this->table();
        try {
            $statement = $this->db->prepare($query);
            $statement->execute($params);
            if ($statement->rowCount()) {
                $arr = $statement->fetchAll(\PDO::FETCH_ASSOC);
                if (!$arr) return [];
                $items = [];
                foreach ($arr as $data) {
                    $items[] = $this->createModel($data);
                }
                return $items;
            }
        } catch (\PDOException $e) {
            $this->error = 'SQL error: ' . $e->getMessage();
        }
        return [];
    }

    public function fetchOne($id, string $query = '', array $params = []): ModelInterface|null
    {
        if (!$query) $query = "SELECT * FROM {$this->table()} WHERE {$this->createModel()->pk()}=:id";
        try {
            $statement = $this->db->prepare($query);
            $statement->execute(['id' => $id]);
            if ($statement->rowCount()) {
                $file = $statement->fetch(\PDO::FETCH_ASSOC);
                return $this->createModel($file);
            }
        } catch (\PDOException $e) {
            $this->error = 'SQL error: ' . $e->getMessage() . ' QUERY: ' . $query;
        }
        return null;
    }

    public function createModel($data = []): ModelInterface {
        return new Model($data);
    }

    function insert(ModelInterface $model): bool
    {
        $table = $this->table();
        $fields = array_filter($model->getPropertiesArray(), fn($prop) => $prop !== $model->pk());
        $data = array_filter($model->getDataArray(), fn($prop) => $prop !== $model->pk(), ARRAY_FILTER_USE_KEY);

        $query = "INSERT INTO {$table} (" . implode(', ', $fields) . ") VALUES (:"
            . implode(', :', $fields) . ")";
        try {
            $statement = $this->db->prepare($query);
            $statement->execute($data);
//            $statement->debugDumpParams();
        } catch (\PDOException $e) {
            $this->error = 'SQL error: ' . $e->getMessage() . ' QUERY: ' . $query;
            return false;
        }
        $model->setPkValue($this->db->lastInsertId());
        return true;
    }

    function update(ModelInterface $model): bool
    {
        $table = $this->table();
        $fields = array_filter($model->getPropertiesArray(), fn($prop) => $prop !== $model->pk());
        $data = $model->getDataArray();

        $query = "UPDATE {$table} SET ";
        $q = "";
        foreach ($fields as $field) {
            if ($q) $q .= ", ";
            $q .= "{$field}=:{$field}";
        }
        $query .= $q . " WHERE {$model->pk()}=:{$model->pk()}";

        try {
            $statement = $this->db->prepare($query);
            $statement->execute($data);
        } catch (\PDOException $e) {
            $this->error = 'SQL error: ' . $e->getMessage() . ' QUERY: ' . $query;
            return false;
        }
        return true;
    }

    function upsert(ModelInterface $model): bool
    {
        $table = $this->table();
        $fields = $model->getPropertiesArray();
        $data = $model->getDataArray();

        $query = "INSERT INTO {$table} (" . implode(', ', $fields) . ") VALUES (:"
            . implode(', :', $fields) . ")";
        $q = "";
        foreach ($fields as $field) {
            if ($field == $model->pk()) continue;
            if ($q) $q .= ", ";
            $q .= "{$field}=:{$field}q";
            $data[$field. 'q'] = $data[$field];
        }
        $query .= " ON DUPLICATE KEY UPDATE " . $q;

        try {
            $statement = $this->db->prepare($query);
            $statement->execute($data);
        } catch (\PDOException $e) {
            $this->error = 'SQL error: ' . $e->getMessage() . ' QUERY: ' . $query;
            return false;
        }
        return true;
    }

    function delete(ModelInterface $model): bool
    {
        $table = $this->table($model);
        $query = "DELETE FROM {$table} WHERE {$model->pk()}=:{$model->pk()}";
        try {
            $statement = $this->db->prepare($query);
            $statement->execute([$model->pk() => $model->pkValue()]);
        } catch (\PDOException $e) {
            $this->error = 'SQL error: ' . $e->getMessage() . ' QUERY: ' . $query;
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }
}