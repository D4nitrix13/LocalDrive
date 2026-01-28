<?php

/**
 * Devuelve un Manager de MongoDB usando el driver nativo (sin Composer).
 */
function get_mongo_manager(): MongoDB\Driver\Manager
{
    static $manager = null;

    if ($manager === null) {
        $uri = getenv('MONGO_URI')
            ?: 'mongodb://mongo_user:mongo_password@mongo:27017/localdrive_logs?authSource=admin&authMechanism=SCRAM-SHA-1';

        $manager = new MongoDB\Driver\Manager($uri);
    }

    return $manager;
}

/**
 * Registra un evento en la colección localdrive_logs.events.
 *
 * $type    → tipo de evento (p.ej. "user_registered").
 * $payload → datos adicionales del evento.
 */
function log_event(string $type, array $payload = []): void
{
    try {
        $manager = get_mongo_manager();

        // Documento base con info común
        $doc = [
            'type'       => $type, // login, create_dir, delete_file, upload_file, user_registered, etc.
            'user_id'    => $_SESSION['user']['id']    ?? null,
            'email'      => $_SESSION['user']['email'] ?? null,
            'payload'    => $payload,
            'ip'         => $_SERVER['REMOTE_ADDR']     ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
        ];

        $bulk = new MongoDB\Driver\BulkWrite();
        $bulk->insert($doc);

        // BD.localdrive_logs, colección.events
        $manager->executeBulkWrite('localdrive_logs.events', $bulk);
    } catch (Throwable $e) {
        // Nunca romper la app por fallo de log
        error_log('[MONGO_LOG_ERROR] ' . $e->getMessage());
    }
}
