<?php

namespace app\models;

use app\models\model\Model;

class Maintenance extends Model
{
    public string|int $id;
    public string|int $equipment_id;
    public string|int $pos;
    public string $name;
    public string $description;
    public string $next_date;
    public string|int $repeat_type;
    public string|int $days;

    public function url() {
        return '/equipments/#/maintenance/' . $this->equipment_id;
    }
}