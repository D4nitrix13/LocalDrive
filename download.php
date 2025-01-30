<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: ./register.php");
    exit();
}

$connection = require "./sql/db.php";
$file = "";

if (isset($_GET["id"]) and isset($_GET["file"])) {

    $file = $_GET['file'];
    $id = $_GET["id"];

    $statement = $connection->prepare("SELECT * FROM files WHERE id_user = :id_user AND path = :path LIMIT 1");
    $statement->execute([
        ":id_user" => $id,
        ":path" => addslashes($file)
    ]);

    if ((int)$_GET["id"] !== (int)$_SESSION["user"]["id"] || $statement->rowCount() === 0) {
        http_response_code(401);
        echo "[x] Unauthorized" . PHP_EOL;
        exit();
    }
}

// ! File Does Not Exist
if (!file_exists($file)) {
    http_response_code(404);
    die("Error Opening File: $file");
}

$name = basename($file);
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=$name");
header("Content-Type: " . mime_content_type($file));
header("Content-Transfer-Encoding: binary");
header('Content-Length: ' . filesize($file));
while (ob_get_level()) ob_end_clean();
// * Read The File From Disk
readfile($file);
?>