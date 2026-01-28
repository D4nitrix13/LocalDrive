<?php

function redis_client(): Redis
{
    static $redis = null;

    if ($redis === null) {
        $redis = new Redis();

        $host = 'redis';
        $port = 6379;

        if (!$redis->connect($host, $port)) {
            throw new RuntimeException("No se pudo conectar a Redis en {$host}:{$port}");
        }

    }

    return $redis;
}
