<?php
namespace app\repositories\model;

use app\models\model\ModelInterface;

interface ModelRepositoryInterface {
    function table(ModelInterface $model = null): string;
    function fetchList(string $query, array $params = []): array;
    function fetchOne($id, string $query = '', array $params = []): ModelInterface|null;
    function insert(ModelInterface $model): bool;
    function update(ModelInterface $model): bool;
    function delete(ModelInterface $model): bool;
    function getError(): string;
    function createModel(): ModelInterface|null;
}