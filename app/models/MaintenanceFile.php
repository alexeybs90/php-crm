<?php

namespace app\models;

use app\models\model\Model;

class MaintenanceFile extends Model
{
    public string|int $id;
    public string|int $parent_id;
    public string $file;
    public string $name;
    public string|int $pos;
    public string $r_name;
    public string $created_at;
}