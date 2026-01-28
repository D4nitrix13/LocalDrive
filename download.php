<?php
session_start();

require_once __DIR__ . '/sql/mongo_logger.php';

if (!isset($_SESSION["user"])) {
    // Log: intento de descarga sin sesión
    log_event('download_unauthenticated', [
        'requested_file' => $_GET['file'] ?? null,
        'requested_user_id' => $_GET['id'] ?? null,
        'reason' => 'User tried to download a file without an active session.'
    ]);

    header("Location: ./register.php");
    exit();
}

$connection = require "./sql/db.php";
$file = "";

if (isset($_GET["id"]) && isset($_GET["file"])) {
    $file = $_GET['file'];
    $id   = $_GET["id"];

    $statement = $connection->prepare(
        "SELECT * FROM files WHERE id_user = :id_user AND path = :path LIMIT 1"
    );
    $statement->execute([
        ":id_user" => $id,
        ":path"    => addslashes($file),
    ]);

    // ❌ No corresponde al usuario logueado o no existe el registro en BD
    if ((int)$id !== (int)$_SESSION["user"]["id"] || $statement->rowCount() === 0) {
        http_response_code(401);
        echo "[x] Unauthorized" . PHP_EOL;

        // Log: intento de descarga NO autorizado
        log_event('file_download_unauthorized', [
            'requested_file' => $file,
            'requested_user_id' => $id,
            'session_user_id' => $_SESSION["user"]["id"] ?? null,
            'session_email'    => $_SESSION["user"]["email"] ?? null,
            'reason' => 'User tried to download a file that is not theirs or not registered in DB.'
        ]);

        exit();
    }
}

// ! File Does Not Exist
if (!file_exists($file)) {
    http_response_code(404);
    // Log: archivo no encontrado en disco
    log_event('file_not_found', [
        'file'   => $file,
        'user_id' => $_SESSION["user"]["id"] ?? null,
        'email'   => $_SESSION["user"]["email"] ?? null,
        'reason'  => 'File path is registered but does not exist on disk.'
    ]);

    die("Error Opening File: $file");
}

// Descarga correcta
$name = basename($file);

// Log: descarga exitosa
log_event('file_downloaded', [
    'file'    => $file,
    'name'    => $name,
    'user_id' => $_SESSION["user"]["id"] ?? null,
    'email'   => $_SESSION["user"]["email"] ?? null,
    'size'    => @filesize($file),
]);

header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=$name");
header("Content-Type: " . mime_content_type($file));
header("Content-Transfer-Encoding: binary");
header('Content-Length: ' . filesize($file));

while (ob_get_level()) {
    ob_end_clean();
}

// * Read The File From Disk
readfile($file);
