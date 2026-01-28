<?php
session_start();

require_once __DIR__ . '/sql/mongo_logger.php';

if (!isset($_SESSION["user"])) {
    log_event('directory_delete_unauthenticated', [
        'requested_dir' => $_POST['directoryRemove'] ?? null,
        'requested_user_id' => $_GET['id'] ?? null,
        'reason' => 'User attempted to delete directory without active session.'
    ]);

    header("Location: ./register.php");
    exit();
}

if (!isset($_GET["id"]) || (int)$_SESSION["user"]["id"] !== (int)$_GET["id"]) {
    http_response_code(401);
    echo "[x] 401 Unauthorized";

    log_event('directory_delete_unauthorized', [
        'requested_dir'      => $_POST['directoryRemove'] ?? null,
        'requested_user_id'  => $_GET['id'] ?? null,
        'session_user_id'    => $_SESSION["user"]["id"] ?? null,
        'session_email'      => $_SESSION["user"]["email"] ?? null,
        'reason'             => 'User tried to delete a directory that does not belong to them or id mismatch.'
    ]);

    exit();
}

function removeDir(string $dir): void
{
    $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

    foreach ($files as $file) {
        if ($file->isDir()) {
            rmdir($file->getPathname());
        } else {
            unlink($file->getPathname());
        }
    }

    rmdir($dir);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["directoryRemove"])) {
    $connection      = require "./sql/db.php";
    $directoryRemove = $_POST["directoryRemove"];

    if ($directoryRemove) {
        if (!is_dir($directoryRemove)) {
            http_response_code(404);
            echo "[x] The {$directoryRemove} directory was not found";

            log_event('directory_delete_path_not_found', [
                'directory' => $directoryRemove,
                'user_id'   => $_SESSION["user"]["id"] ?? null,
                'email'     => $_SESSION["user"]["email"] ?? null,
                'reason'    => 'Requested directory does not exist on disk before delete.'
            ]);

            exit();
        }

        removeDir($directoryRemove);

        $statement = $connection->prepare("SELECT function_delete_directory(:path) AS data");
        $statement->bindParam(":path", $directoryRemove);
        $statement->execute();

        log_event('directory_deleted', [
            'directory' => $directoryRemove,
            'user_id'   => $_SESSION["user"]["id"] ?? null,
            'email'     => $_SESSION["user"]["email"] ?? null
        ]);

        $_SESSION["flash"] = [
            "message"     => "Delete Directory " . $directoryRemove,
            "class"       => "alert alert-danger d-flex align-items-center",
            "aria-label"  => "Danger:",
            "xlink:href"  => "#exclamation-triangle-fill"
        ];

        header("Location: ./home.php?directory=" . urlencode($_SESSION['directoryPath']));
        exit();
    }

    http_response_code(404);
    echo "[x] The directory was not provided or is empty";

    log_event('directory_delete_invalid_request', [
        'directory' => $directoryRemove,
        'user_id'   => $_SESSION["user"]["id"] ?? null,
        'email'     => $_SESSION["user"]["email"] ?? null,
        'reason'    => 'directoryRemove is missing or empty in POST.'
    ]);

    exit();
}

http_response_code(405);
echo "[x] Method Not Allowed";
log_event('directory_delete_invalid_method', [
    'method'  => $_SERVER['REQUEST_METHOD'] ?? null,
    'user_id' => $_SESSION["user"]["id"] ?? null,
    'email'   => $_SESSION["user"]["email"] ?? null
]);
exit();
