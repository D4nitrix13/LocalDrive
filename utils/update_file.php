<?php
require_once __DIR__ . '/sql/mongo_logger.php';

move_uploaded_file($tmpNameFile, $pathFile);

$statement = $connection->prepare(
    "SELECT function_update_data_files(:id_user, :name, :type, :size, :file_creation_date) AS value"
);
$statement->execute([
    ":id_user"            => "{$_SESSION['user']['id']}",
    ":name"               => $nameFile,
    ":type"               => $typeFile,
    ":size"               => $sizeFile,
    ":file_creation_date" => $modificationDate
]);

$result = $statement->fetch(PDO::FETCH_ASSOC);
$ok = isset($result['value']) ? (bool)$result['value'] : false;

if ($ok === true) {
    // Log MongoDB: actualización correcta
    log_event('file_update_success', [
        'file_name' => $nameFile,
        'file_path' => $pathFile,
        'file_size' => $sizeFile,
        'file_type' => $typeFile,
    ]);

    $_SESSION["flash"] = [
        "message" => "Update File $nameFile",
        "class" => "alert alert-warning d-flex align-items-center",
        "aria-label" => "Warning:",
        "xlink:href" => "#check-circle-fill"
        // Colour Yellow
    ];

    header("Location: .{$_SERVER['PHP_SELF']}?directory=" . urlencode($_SESSION['directoryPath']));
    exit();
}

// Log MongoDB: fallo al actualizar en BD
log_event('file_update_failed', [
    'file_name'  => $nameFile,
    'file_path'  => $pathFile,
    'file_size'  => $sizeFile,
    'file_type'  => $typeFile,
    'db_value'   => $result['value'] ?? null,
]);

echo '[x] Error Query Consult';
exit();
