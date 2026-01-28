<?php
session_start();

require_once __DIR__ . '/sql/mongo_logger.php';

if (
    !isset($_GET["id"]) ||
    (int)$_GET["id"] !== (int)$_SESSION["user"]["id"]
) {
    http_response_code(401);
    echo "[x] Unauthorized" . PHP_EOL;

    // Log de intento de eliminación NO autorizado
    log_event('user_delete_unauthorized', [
        'requested_id' => $_GET["id"] ?? null,
        'session_user_id' => $_SESSION["user"]["id"] ?? null,
        'reason' => 'User tried to delete an account that is not theirs.'
    ]);

    exit();
}

$connection = require "./sql/db.php";
$id = (int)$_GET["id"];

// -- Capturar datos ANTES de eliminar para el log
$deletedUser = [
    'id'    => $_SESSION["user"]["id"] ?? null,
    'email' => $_SESSION["user"]["email"] ?? null,
];

// Ejecutar funciones de borrado (archivos y usuario)
$connection->query("SELECT function_delete_data_files($id) AS value");
$connection->query("SELECT function_delete_data_user($id) AS value");

// LOG en MongoDB: Usuario eliminado
log_event('user_deleted', [
    'user_id' => $deletedUser['id'],
    'email'   => $deletedUser['email'],
    'message' => 'User account and all associated records were deleted successfully.'
]);

// Eliminar sesión
session_unset();
session_destroy();

// Redirigir al index
header("Location: ./index.php");
exit();
