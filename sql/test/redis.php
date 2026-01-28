<?php
// sql/test/redis.php
require_once __DIR__ . '../../redis.php';

try {
    $redis = redis_client();

    $key = 'test:redis:' . bin2hex(random_bytes(4));
    $redis->set($key, 'OK', 10); // TTL 10s

    echo "Escribí la clave {$key} con valor: " . $redis->get($key) . PHP_EOL;
} catch (Throwable $e) {
    http_response_code(500);
    echo "Error Redis: " . $e->getMessage();
}
