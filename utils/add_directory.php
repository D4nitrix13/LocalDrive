<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["inputNameDirectory"])) {
    $directoryActual = $_SESSION["directoryPath"];

    $newNameDirectory = $_POST["inputNameDirectory"];
    $newDirectoryPath = "$directoryActual" . DIRECTORY_SEPARATOR . "$newNameDirectory";

    // Verificar Si El Directory Tiene Characters Specials
    foreach (str_split('!"#$%&\'()*+,-./:;<=>?@[\]^_`{|}~') as $key => $charSpecials) {
        foreach (str_split($newNameDirectory) as $index => $char) {
            if ($char !== $charSpecials) continue;

            $_SESSION["flash"] = [
                "message" => "Directory Contains Characters Speciales $newNameDirectory Not Create",
                "class" => "alert alert-warning d-flex align-items-center",
                "aria-label" => "Warning:",
                "xlink:href" => "#check-circle-fill"
                // Colour Yellow
            ];

            header("Location: {$_SERVER['PHP_SELF']}?directory=$directoryActual");
            exit();
        }
    }

    // Verificar Si Existe Directory
    if (file_exists($newDirectoryPath)) {
        $_SESSION["flash"] = [
            "message" => "Directory Exists $newNameDirectory Not Create",
            "class" => "alert alert-warning d-flex align-items-center",
            "aria-label" => "Warning:",
            "xlink:href" => "#check-circle-fill"
            // Colour Yellow
        ];

        header("Location: {$_SERVER['PHP_SELF']}?directory=$directoryActual");
        exit();
    }
    $statement = $connection->prepare("INSERT INTO directory (id_user, name, path, parent_directory) VALUES (:id_user, :name, :path, :parent_directory)");
    $statement->bindParam(":id_user", $_SESSION["user"]["id"]);
    $statement->bindParam(":name", $newNameDirectory);
    $newDirectoryPath = addslashes($newDirectoryPath);
    $statement->bindParam(":path", $newDirectoryPath);
    $statement->bindParam(":parent_directory", $directoryActual);
    $statement->execute();
    mkdir($newDirectoryPath, 0777, true);

    $_SESSION["flash"] = [
        "message" => "Add Directory $newNameDirectory",
        "class" => "alert alert-primary d-flex align-items-center",
        "aria-label" => "Info:",
        "xlink:href" => "#info-fill"
        // Colour Blue
    ];
    header("Location: {$_SERVER['PHP_SELF']}?directory=$directoryActual");
    exit();
}
?>