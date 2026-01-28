<?php

function redis_client(): Redis
{
    static $redis = null;

    if ($redis === null) {
        $redis = new Redis();

        $host = getenv('REDIS_HOST') ?: 'redis';
        $port = (int)(getenv('REDIS_PORT') ?: 6379);
        $password = getenv('REDIS_PASSWORD') ?: 's3cureRedisP@ss';

        // Conexión
        if (!$redis->connect($host, $port)) {
            throw new RuntimeException("No se pudo conectar a Redis en {$host}:{$port}");
        }

        // Autenticación obligatoria
        if (!$redis->auth($password)) {
            throw new RuntimeException("No se pudo autenticar en Redis (clave incorrecta)");
        }
    }

    return $redis;
}
