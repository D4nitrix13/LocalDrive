<?php
require_once __DIR__ . '/../sql/mongo_logger.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["inputNameDirectory"])) {
    $directoryActual = $_SESSION["directoryPath"];

    $newNameDirectory = $_POST["inputNameDirectory"];
    $newDirectoryPath = "$directoryActual" . DIRECTORY_SEPARATOR . "$newNameDirectory";

    // Verificar si el nombre del directorio contiene caracteres especiales
    foreach (str_split('!"#$%&\'()*+,-./:;<=>?@[\]^_`{|}~') as $key => $charSpecials) {
        foreach (str_split($newNameDirectory) as $index => $char) {
            if ($char !== $charSpecials) {
                continue;
            }

            // Log en MongoDB: intento de crear directorio con caracteres no permitidos
            log_event('directory_create_invalid_chars', [
                'directory_name' => $newNameDirectory,
                'current_directory' => $directoryActual,
                'reason' => 'Directory name contains special characters'
            ]);

            $_SESSION["flash"] = [
                "message" => "Directory Contains Characters Speciales $newNameDirectory Not Create",
                "class" => "alert alert-warning d-flex align-items-center",
                "aria-label" => "Warning:",
                "xlink:href" => "#check-circle-fill"
            ];

            header("Location: {$_SERVER['PHP_SELF']}?directory=$directoryActual");
            exit();
        }
    }

    // Verificar si el directorio ya existe
    if (file_exists($newDirectoryPath)) {
        // Log en MongoDB: intento de crear un directorio que ya existe
        log_event('directory_create_already_exists', [
            'directory_name' => $newNameDirectory,
            'directory_path' => $newDirectoryPath,
            'current_directory' => $directoryActual
        ]);

        $_SESSION["flash"] = [
            "message" => "Directory Exists $newNameDirectory Not Create",
            "class" => "alert alert-warning d-flex align-items-center",
            "aria-label" => "Warning:",
            "xlink:href" => "#check-circle-fill"
        ];

        header("Location: {$_SERVER['PHP_SELF']}?directory=$directoryActual");
        exit();
    }

    $statement = $connection->prepare(
        "INSERT INTO directory (id_user, name, path, parent_directory) 
         VALUES (:id_user, :name, :path, :parent_directory)"
    );
    $statement->bindParam(":id_user", $_SESSION["user"]["id"]);
    $statement->bindParam(":name", $newNameDirectory);
    $newDirectoryPathEscaped = addslashes($newDirectoryPath);
    $statement->bindParam(":path", $newDirectoryPathEscaped);
    $statement->bindParam(":parent_directory", $directoryActual);
    $statement->execute();

    mkdir($newDirectoryPath, 0777, true);

    // Log en MongoDB: directorio creado correctamente
    log_event('directory_create_success', [
        'directory_name' => $newNameDirectory,
        'directory_path' => $newDirectoryPath,
        'current_directory' => $directoryActual
    ]);

    $_SESSION["flash"] = [
        "message" => "Add Directory $newNameDirectory",
        "class" => "alert alert-primary d-flex align-items-center",
        "aria-label" => "Info:",
        "xlink:href" => "#info-fill"
    ];

    header("Location: {$_SERVER['PHP_SELF']}?directory=$directoryActual");
    exit();
}
