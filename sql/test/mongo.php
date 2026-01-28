<?php
// sql/test/mongo.php
// Test directo usando la extensión nativa del driver MongoDB de PHP
// No requiere Composer ni vendor/autoload.php

try {
    // URI del docker-compose
    $uri = getenv('MONGO_URI') ?: 'mongodb://mongo_user:mongo_password@mongo:27017/localdrive_logs?authSource=admin';

    // Crear Manager nativo
    $manager = new MongoDB\Driver\Manager($uri);

    // Documento a insertar
    $doc = [
        'message'     => 'MongoDB Test OK (sin Composer)',
        'php_version' => phpversion(),
        'time'        => new MongoDB\BSON\UTCDateTime(),
        'random'      => bin2hex(random_bytes(4)),
        'container'   => gethostname()
    ];

    // Preparar Write
    $bulk = new MongoDB\Driver\BulkWrite();
    $id = $bulk->insert($doc);

    // Ejecutar write contra la BD y colección
    $manager->executeBulkWrite('localdrive_logs.test_connection', $bulk);

    echo "MongoDB respondió correctamente\n";
    echo "Documento insertado con ID: " . json_encode($id) . "\n";
} catch (Throwable $e) {
    http_response_code(500);
    echo "Error MongoDB: " . $e->getMessage() . PHP_EOL;
}
