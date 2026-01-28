<?php
function get_mongo_client(): MongoDB\Client
{
    static $client = null;

    if ($client === null) {
        $uri = getenv('MONGO_URI') ?: 'mongodb://mongo_user:mongo_password@mongo:27017/localdrive_logs?authSource=admin';
        $client = new MongoDB\Client($uri);
    }

    return $client;
}

function log_event(string $type, array $payload = []): void
{
    try {
        $client = get_mongo_client();
        $collection = $client->selectCollection('localdrive_logs', 'events');

        $doc = [
            'type'      => $type,              // login, create_dir, delete_file, upload_file, etc.
            'user_id'   => $_SESSION['user']['id'] ?? null,
            'email'     => $_SESSION['user']['email'] ?? null,
            'payload'   => $payload,
            'ip'        => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
        ];

        $collection->insertOne($doc);
    } catch (Throwable $e) {
        // En producción lo ideal es otro canal de error,
        // pero nunca romper la app por fallo de log.
        error_log('[MONGO_LOG_ERROR] ' . $e->getMessage());
    }
}
