<?php
require_once __DIR__ . '/../sql/mongo_logger.php';

if (isset($_GET["id"]) && (int)$_GET["id"] === (int)$_SESSION["user"]["id"]) {
    $filePath = $_GET['file'];

    // Normalizamos el nombre para logs
    $fileName = pathinfo($filePath, PATHINFO_BASENAME);

    if (file_exists($filePath)) {
        // Tomamos tamaño antes de borrar por si hace falta en auditoría
        $fileSize = filesize($filePath);
        $directoryPath = dirname($filePath);

        $stmt = $connection->prepare(
            "DELETE FROM files
             WHERE id_user = :id_user AND path = :path"
        );
        $stmt->execute([
            ":id_user" => $_SESSION["user"]["id"],
            ":path"    => addslashes($filePath),
        ]);

        unlink($filePath);

        // Log MongoDB: borrado de archivo correcto
        log_event('file_delete_success', [
            'file_name'      => $fileName,
            'file_path'      => $filePath,
            'file_size'      => $fileSize,
            'directory_path' => $directoryPath,
        ]);

        $_SESSION["flash"] = [
            "message" => "Delete File " . $fileName,
            "class" => "alert alert-danger d-flex align-items-center",
            "aria-label" => "Danger:",
            "xlink:href" => "#exclamation-triangle-fill"
        ];

        header("Location: .{$_SERVER['PHP_SELF']}?directory=" . urlencode($_SESSION['directoryPath']));
        exit();
    }

    // Log MongoDB: intento de borrado pero el archivo no existe
    log_event('file_delete_not_found', [
        'file_path' => $filePath,
    ]);

    http_response_code(404);
    echo "[x] File Not Found" . PHP_EOL;
    exit();
}

// Log MongoDB: intento de borrado no autorizado
log_event('file_delete_unauthorized', [
    'requested_user_id' => isset($_GET['id']) ? (int)$_GET['id'] : null,
    'session_user_id'   => $_SESSION['user']['id'] ?? null,
]);

http_response_code(401);
echo "[x] 401 Unauthorized" . PHP_EOL;
exit();
