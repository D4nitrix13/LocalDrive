<?php
// * Devuelve True
if ( !file_exists($pathFile) ) {
    $directoryPathFile = dirname( addslashes( $pathFile ) );
    
    // Verificamos si el directorio en el cual se agregara el fichero esta siendo compartido 
    $statement = $connection->prepare(
        "SELECT u.email
        FROM shared_directory s
        INNER JOIN users u ON u.id = s.id_user_guest
        WHERE s.id_user_property = :id_user_property AND
        s.path = :path"
    );
    $statement->bindParam( ":id_user_property", $_SESSION['user']['id'], PDO::PARAM_INT );
    $statement->bindParam( ":path", $directoryPathFile, PDO::PARAM_STR );
    $statement->execute();

    // En dado caso que la consulta devuelva registros añadimos el registro ala tabla shared_file con el parametro (true)
    if ( $statement->rowCount() > 0 ) {

        $emailGuests = $statement->fetchAll( PDO::FETCH_ASSOC );

        for ($i = 0; $i < count( $emailGuests ); $i++) { 

            $statement = $connection->prepare(
                "SELECT function_insert_data_shared_file(
                    :id_user_property, :email_destinatario, :shared_from_a_directory, :path
                ) AS value
            ");
           $statement->bindParam(":id_user_property", $_SESSION['user']['id'], PDO::PARAM_INT);
           $statement->bindParam(":email_destinatario", $emailGuests[$i]["email"], PDO::PARAM_STR);
           
           $shared_from_a_directory = true;
           $pathFileEscaped = addslashes($pathFile);
           
           $statement->bindParam(":shared_from_a_directory", $shared_from_a_directory, PDO::PARAM_BOOL);
           $statement->bindParam(":path", $pathFileEscaped, PDO::PARAM_STR);
           
           $statement->execute();

        }
    }

    $statement = $connection->prepare(
        "SELECT function_insert_data_files(:id_user, :name, :type, :path, :size, :file_creation_date) AS value"
    );
    $statement->execute([
        ":id_user" => "{$_SESSION['user']['id']}",
        ":name" => $nameFile,
        ":type" => $typeFile,
        ":path" => addslashes( $pathFile ),
        ":size" => $sizeFile,
        ":file_creation_date" => $modificationDate
    ]);
    
    // touch($pathFile);

    move_uploaded_file( $tmpNameFile, $pathFile );

    $_SESSION["flash"] = [
        "message" => "Add File $nameFile",
        "class" => "alert alert-primary d-flex align-items-center",
        "aria-label" => "Info:",
        "xlink:href" => "#info-fill"
        // Colour Blue
    ];
    header( "Location: .{$_SERVER['PHP_SELF']}?directory=" . urlencode( $_SESSION['directoryPath'] ) );
    exit();
}
?>