<?php
if (isset($_GET["id"]) && (int)$_GET["id"] === (int)$_SESSION["user"]["id"]) {
    $filePath = $_GET['file'];

    if (file_exists($filePath)) {
        $connection->prepare(
            "DELETE FROM files WHERE id_user = :id_user AND path = :path"
        )->execute([
            ":id_user" => $_SESSION["user"]["id"],
            ":path" => addslashes($filePath)
        ]);
        unlink($filePath);
        $nameFile = pathinfo($filePath)["basename"];
        $_SESSION["flash"] = [
            "message" => "Delete File " . pathinfo($filePath, PATHINFO_BASENAME),
            "class" => "alert alert-danger d-flex align-items-center",
            "aria-label" => "Danger:",
            "xlink:href" => "#exclamation-triangle-fill"
            // Colour Red
        ];
        header("Location: .{$_SERVER['PHP_SELF']}?directory=" . urlencode($_SESSION['directoryPath']));
        exit();
    }

    http_response_code(404);
    echo "[x] File Not Found" . PHP_EOL;
    exit();
}
?>