<?php

namespace app\lib;

interface Cache
{
    public function exists(string $key): bool;
    public function get(string $key): mixed;
    public function set(string$key, mixed $value): bool;
    public function setex(string $key, int $expire, mixed $value): bool;
    public function del(string|array $key1, ...$otherKeys): int;
}