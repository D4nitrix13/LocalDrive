<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: ./register.php");
    exit();
}

if ((int)$_SESSION["user"]["id"] !== (int)$_GET["id"]) {
    http_response_code(401);
    echo "[x] 401 Unauthorized";
    exit();
}

function removeDir(string $dir): void
{
    $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
    $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $file) {
        if ($file->isDir()) rmdir($file->getPathname());
        else unlink($file->getPathname());
    }
    rmdir($dir);
    return;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["directoryRemove"])) {
    $connection = require "./sql/db.php";
    $directoryRemove = $_POST["directoryRemove"];

    if ($directoryRemove) {
        removeDir($directoryRemove);

        $statement = $connection->prepare("SELECT function_delete_directory(:path) AS data");
        $statement->bindParam(":path", $directoryRemove);
        $statement->execute();

        $_SESSION["flash"] = [
            "message" => "Delete Directory " . $directoryRemove,
            "class" => "alert alert-danger d-flex align-items-center",
            "aria-label" => "Danger:",
            "xlink:href" => "#exclamation-triangle-fill"
            // Colour Red
        ];

        header("Location: ./home.php?directory=" . urlencode($_SESSION['directoryPath']));
        exit();
    }

    http_response_code(404);
    echo "[x] The $directoryRemove Directory Was Not Found";
    exit();
}
