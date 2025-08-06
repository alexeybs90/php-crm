<?php
namespace app\models\model;

use app\lib\ReflectionHelper;

class Model implements ModelInterface
{
    public function pk(): string
    {
        return 'id';
    }
    public function pkValue(): mixed
    {
        return $this->{$this->pk()} ?? null;
    }
    public function setPkValue($value)
    {
        $this->{$this->pk()} = $value;
    }

    public function __construct(array $data = [])
    {
        $this->loadDataFromArray($data);
    }

    function loadDataFromArray(array $data)
    {
        if (!empty($data)) {
            //$props = $class->getProperties();
            $fields = $this->getPropertiesArray();
            foreach ($data as $key => $val) {
                // allow only public non-static:
                if(in_array($key, $fields)) $this->{$key} = $val;
//                else $this->_data[$key] = $val;
            }
        }
    }

    function validate(): bool
    {
        return true;
    }

    public function getPropertiesArray(): array
    {
        return ReflectionHelper::getPropertiesArray(get_class($this));
    }

    public function getDataArray(): array {
        return ReflectionHelper::getDataArray($this);
    }
}