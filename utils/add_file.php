<?php
require_once __DIR__ . '/sql/mongo_logger.php';

// Devuelve true si el archivo no existe aún
if (!file_exists($pathFile)) {
    $directoryPathFile = dirname(addslashes($pathFile));

    // Verificamos si el directorio en el cual se agregará el fichero está siendo compartido
    $statement = $connection->prepare(
        "SELECT u.email
         FROM shared_directory s
         INNER JOIN users u ON u.id = s.id_user_guest
         WHERE s.id_user_property = :id_user_property
           AND s.path = :path"
    );
    $statement->bindParam(":id_user_property", $_SESSION['user']['id'], PDO::PARAM_INT);
    $statement->bindParam(":path", $directoryPathFile, PDO::PARAM_STR);
    $statement->execute();

    // Si la consulta devuelve registros, añadimos el registro a la tabla shared_file
    // indicando que proviene de un directorio compartido
    if ($statement->rowCount() > 0) {
        $emailGuests = $statement->fetchAll(PDO::FETCH_ASSOC);

        for ($i = 0; $i < count($emailGuests); $i++) {
            $statementInsertShared = $connection->prepare(
                "SELECT function_insert_data_shared_file(
                    :id_user_property,
                    :email_destinatario,
                    :shared_from_a_directory,
                    :path
                ) AS value"
            );
            $statementInsertShared->bindParam(":id_user_property", $_SESSION['user']['id'], PDO::PARAM_INT);
            $statementInsertShared->bindParam(":email_destinatario", $emailGuests[$i]["email"], PDO::PARAM_STR);

            $shared_from_a_directory = true;
            $pathFileEscaped = addslashes($pathFile);

            $statementInsertShared->bindParam(":shared_from_a_directory", $shared_from_a_directory, PDO::PARAM_BOOL);
            $statementInsertShared->bindParam(":path", $pathFileEscaped, PDO::PARAM_STR);

            $statementInsertShared->execute();
        }

        // Log MongoDB: archivo agregado en un directorio compartido con invitados
        log_event('file_upload_shared_propagation', [
            'file_name'         => $nameFile,
            'file_path'         => $pathFile,
            'directory_path'    => $directoryPathFile,
            'shared_with_emails' => array_column($emailGuests, 'email'),
        ]);
    }

    $statementInsertFile = $connection->prepare(
        "SELECT function_insert_data_files(
            :id_user,
            :name,
            :type,
            :path,
            :size,
            :file_creation_date
        ) AS value"
    );
    $statementInsertFile->execute([
        ":id_user"            => "{$_SESSION['user']['id']}",
        ":name"               => $nameFile,
        ":type"               => $typeFile,
        ":path"               => addslashes($pathFile),
        ":size"               => $sizeFile,
        ":file_creation_date" => $modificationDate
    ]);

    move_uploaded_file($tmpNameFile, $pathFile);

    // Log MongoDB: subida de archivo exitosa
    log_event('file_upload_success', [
        'file_name'      => $nameFile,
        'file_path'      => $pathFile,
        'file_size'      => $sizeFile,
        'file_type'      => $typeFile,
        'directory_path' => $directoryPathFile,
    ]);

    $_SESSION["flash"] = [
        "message" => "Add File $nameFile",
        "class" => "alert alert-primary d-flex align-items-center",
        "aria-label" => "Info:",
        "xlink:href" => "#info-fill"
    ];

    header("Location: .{$_SERVER['PHP_SELF']}?directory=" . urlencode($_SESSION['directoryPath']));
    exit();
}
