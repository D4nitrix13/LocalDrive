<?php
session_start();

require_once __DIR__ . '/sql/mongo_logger.php';

if (!isset($_SESSION["user"])) {
    log_event('shared_file_download_unauthenticated', [
        'idGuest' => $_GET['idGuest'] ?? null,
        'idProperty' => $_GET['idProperty'] ?? null,
        'file_path' => $_GET['sharedFilePath'] ?? null,
        'reason' => 'Attempted file download without session'
    ]);

    header("Location: ./register.php");
    exit();
}

$sharedFilePath = "";

/**
 * Parámetros:
 * idGuest        -> usuario que descarga
 * idProperty     -> dueño del archivo
 * sharedFilePath -> ruta absoluta del archivo compartido
 */
if (
    isset($_GET['idGuest']) &&
    isset($_GET['idProperty']) &&
    isset($_GET['sharedFilePath']) &&
    (int)$_GET["idGuest"] === (int)$_SESSION["user"]["id"]
) {
    $connection = require "./sql/db.php";

    $idGuest = $_GET['idGuest'];
    $idProperty = $_GET['idProperty'];
    $sharedFilePath = urldecode($_GET['sharedFilePath']);

    $statement = $connection->prepare(
        "SELECT * FROM shared_file 
         WHERE id_user_guest = :id_user_guest  
         AND id_user_property = :id_user_property 
         AND path = :path 
         LIMIT 1"
    );
    $statement->execute([
        ":id_user_property" => $idProperty,
        ":id_user_guest" => $idGuest,
        ":path" => addslashes($sharedFilePath)
    ]);

    if ($statement->rowCount() === 0) {
        http_response_code(403);
        echo "[x] 403 Forbidden: You do not have permission to access this resource.";

        log_event('shared_file_download_unauthorized', [
            'guest_id' => $idGuest,
            'property_id' => $idProperty,
            'file_path' => $sharedFilePath,
            'reason' => 'Record not found or user is not allowed'
        ]);

        exit();
    }
}

// Archivo no existe físicamente
if (!file_exists($sharedFilePath)) {
    http_response_code(404);
    echo "Error Opening File: $sharedFilePath";

    log_event('shared_file_not_found', [
        'user_id' => $_SESSION["user"]["id"],
        'email' => $_SESSION["user"]["email"],
        'file_path' => $sharedFilePath,
        'reason' => 'File does not exist on disk'
    ]);

    exit();
}

log_event('shared_file_download_success', [
    'user_id' => $_SESSION["user"]["id"],
    'email' => $_SESSION["user"]["email"],
    'file_name' => basename($sharedFilePath),
    'file_path' => $sharedFilePath
]);

$name = basename($sharedFilePath);

header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=$name");
header("Content-Type: " . mime_content_type($sharedFilePath));
header("Content-Transfer-Encoding: binary");
header("Content-Length: " . filesize($sharedFilePath));

while (ob_get_level()) {
    ob_end_clean();
}

readfile($sharedFilePath);
exit();
