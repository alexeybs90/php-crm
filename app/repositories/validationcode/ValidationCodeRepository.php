<?php

namespace app\repositories\validationcode;

use app\models\ValidationCode;
use app\models\model\ModelInterface;
use app\repositories\model\ModelRepository;

class ValidationCodeRepository extends ModelRepository
{
    public function table(ModelInterface $model = null): string
    {
        return 'validation_codes';
    }

    public function createModel($data = []): ModelInterface {
        return new ValidationCode($data);
    }
}