<?php
namespace app\lib;

class RedisCache implements Cache
{
    protected \Redis $resourse;

    public function __construct($host = '127.0.0.1', $port = 6379)
    {
        $this->resourse = new \Redis();
        $this->resourse->connect($host, $port);
    }

    public function exists(string $key): bool
    {
        return (bool)$this->resourse->exists($key);
    }

    public function set(string $key, mixed $value): bool
    {
        return (bool)$this->resourse->set($key, $value);
    }

    public function get(string $key): mixed
    {
        return $this->resourse->get($key);
    }

    public function setex(string $key, int $expire, mixed $value): bool
    {
        return (bool)$this->resourse->setex($key, $expire, $value);
    }

    public function del(string|array $key1, ...$otherKeys): int
    {
        return (int)$this->resourse->del($key1, ...$otherKeys);
    }
}