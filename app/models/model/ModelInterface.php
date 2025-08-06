<?php
namespace app\models\model;

interface ModelInterface {
    function loadDataFromArray(array $data);
    function getDataArray(): array;
    function getPropertiesArray(): array;
    function pk(): string;
    function pkValue(): mixed;
    function setPkValue($value);
    function validate(): bool;
}
