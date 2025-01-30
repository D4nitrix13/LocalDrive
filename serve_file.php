<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: ./register.php");
    exit();
}


if (isset($_GET['file']) && (int)$_GET["id"] === (int)$_SESSION["user"]["id"]) {
    $connection = require "./sql/db.php";

    $filePath = $_GET["file"];

    $dataPath = $connection->query(
        "SELECT path FROM files WHERE id_user = {$_SESSION['user']['id']} AND path = '$filePath' LIMIT 1"
    )->fetch(PDO::FETCH_ASSOC);

    if ($dataPath and file_exists($filePath)) {
        $contentType = mime_content_type($filePath);
        header("Content-Type: $contentType");
        readfile($filePath);
        exit();
    }

    http_response_code(401);
    echo "[x] Permiso Denegado." . PHP_EOL;
    exit();
} else if (isset($_GET['file']) && (int)$_GET["id"] !== (int)$_SESSION["user"]["id"]) {
    http_response_code(401);
    echo "[x] Permiso Denegado." . PHP_EOL;
    exit();
}

http_response_code(400);
echo "No se especificó ningún fichero." . PHP_EOL;
exit();
?>