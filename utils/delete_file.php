<?php
require_once __DIR__ . '/../sql/mongo_logger.php';

/**
 * Este script se comporta como "utilidad":
 * - Si NO vienen los parámetros necesarios para borrar (file, id),
 *   simplemente no hace nada y retorna.
 * - Si vienen, valida permisos y ejecuta el borrado.
 */

// Si no es una petición con parámetros de borrado, no hacemos nada.
// Esto permite incluir este archivo en home.php sin romper la página.
if (
    $_SERVER['REQUEST_METHOD'] !== 'GET' ||
    !isset($_GET['file'], $_GET['id'])
) {
    return;
}

// Seguridad extra: asegurarnos de que la sesión existe
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Si no hay usuario en sesión -> intento no autorizado
if (!isset($_SESSION['user'])) {
    log_event('file_delete_unauthorized', [
        'reason'           => 'no_session_user',
        'requested_user_id' => (int) ($_GET['id'] ?? 0),
        'session_user_id'  => null,
    ]);

    http_response_code(401);
    echo "[x] 401 Unauthorized" . PHP_EOL;
    exit();
}

// Validar que el id del query coincide con el user logueado
if ((int)$_GET["id"] !== (int)$_SESSION["user"]["id"]) {
    log_event('file_delete_unauthorized', [
        'reason'           => 'mismatched_user_id',
        'requested_user_id' => (int) $_GET['id'],
        'session_user_id'  => (int) $_SESSION['user']['id'],
    ]);

    http_response_code(401);
    echo "[x] 401 Unauthorized" . PHP_EOL;
    exit();
}

$filePath = $_GET['file'];
$fileName = pathinfo($filePath, PATHINFO_BASENAME);

if (file_exists($filePath)) {
    $fileSize      = filesize($filePath);
    $directoryPath = dirname($filePath);

    // IMPORTANTE: asumimos que $connection ya existe porque home.php
    // hizo: $connection = require "./sql/db.php";
    /** @var PDO $connection */

    $stmt = $connection->prepare(
        "DELETE FROM files
         WHERE id_user = :id_user AND path = :path"
    );
    $stmt->execute([
        ":id_user" => $_SESSION["user"]["id"],
        ":path"    => addslashes($filePath),
    ]);

    unlink($filePath);

    // Log MongoDB: borrado correcto
    log_event('file_delete_success', [
        'file_name'      => $fileName,
        'file_path'      => $filePath,
        'file_size'      => $fileSize,
        'directory_path' => $directoryPath,
        'user_id'        => (int) $_SESSION['user']['id'],
    ]);

    $_SESSION["flash"] = [
        "message"    => "Delete File " . $fileName,
        "class"      => "alert alert-danger d-flex align-items-center",
        "aria-label" => "Danger:",
        "xlink:href" => "#exclamation-triangle-fill"
    ];

    // Redirigir de vuelta al directorio actual
    header("Location: .{$_SERVER['PHP_SELF']}?directory=" . urlencode($_SESSION['directoryPath']));
    exit();
}

// Log MongoDB: el archivo no existe
log_event('file_delete_not_found', [
    'file_path'     => $filePath,
    'user_id'       => (int) $_SESSION['user']['id'],
]);

http_response_code(404);
echo "[x] File Not Found" . PHP_EOL;
exit();
