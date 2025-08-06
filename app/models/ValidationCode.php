<?php

namespace app\models;

use app\models\model\Model;

class ValidationCode extends Model
{
    public string|int $id;
    public string|int $worker_id;
    public string $date_use;
    public string|int $value;
}