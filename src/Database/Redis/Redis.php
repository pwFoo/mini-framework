<?php
/**
 * This file is part of Mini.
 * @auth lupeng
 */
declare(strict_types=1);

namespace Mini\Database\Redis;

use Mini\Singleton;

/**
 * Class Redis
 * @package Mini\Database\Redis
 * @mixin \Swoole\Coroutine\Redis | \Redis
 */
class Redis
{
    protected \Redis $connection;

    public function __construct(string $connection = '', array $config = [])
    {
        $this->connection = Pool::getInstance($config)->getConnection($connection);
    }

    public function __call($name, $arguments)
    {
        return $this->connection->{$name}(...$arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        return (new self())->connection->{$name}(...$arguments);
    }
}
