<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: ./register.php");
    exit();
}

// * sharedFilePath -> Ruta absoluta del fichero compartido
// * idGuest -> Es el id del user current
// * idProperty -> Es el id del user property del file
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

    $statement = $connection->prepare("SELECT * FROM shared_file WHERE id_user_guest = :id_user_guest AND id_user_property = :id_user_property AND path = :path LIMIT 1");
    $statement->execute([
        ":id_user_property" => $idProperty,
        ":id_user_guest" => $idGuest,
        ":path" => addslashes($sharedFilePath)
    ]);

    if ($statement->rowCount() === 0) {
        http_response_code(403);
        echo "[x] 403 Forbidden: You do not have permission to access this resource.";
        exit();
    }

    if (file_exists($sharedFilePath)) {
        $contentType = mime_content_type($sharedFilePath);
        header("Content-Type: $contentType");
        readfile($sharedFilePath);
        exit();
    }
} else if (
    isset($_GET['idGuest']) &&
    isset($_GET['idProperty']) &&
    isset($_GET['sharedFilePath']) &&
    (int)$_GET["idGuest"] !== (int)$_SESSION["user"]["id"]
) {
    http_response_code(401);
    echo "[x] Permiso Denegado." . PHP_EOL;
    exit();
}

http_response_code(400);
echo "No se especificó ningún fichero." . PHP_EOL;
exit();
