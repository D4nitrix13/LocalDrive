<?php
// if ( isset($_FILES["uploadedFile"]) ){
//     var_dump($_FILES); 
//     die();
// }

if (isset($_FILES["uploadedFile"]) and $_FILES["uploadedFile"]["size"] > 3221225000) {
    $_SESSION["flash"] = [
        "message" => "Error File {$_FILES['uploadedFile']['name']} POST Content-Length Of {$_FILES['uploadedFile']['size']} Bytes Exceeds The Limit Of 3221225000 Bytes",
        "class" => "alert alert-danger d-flex align-items-center",
        "aria-label" => "Danger:",
        "xlink:href" => "#exclamation-triangle-fill"
        // Colour Red
    ];

    header("Location: {$_SERVER['PHP_SELF']}");
    exit();
}


/**
 * array(1) { 
 *  ["uploadedFile"]=> array(6) { 
 *     ["name"]=> string(7) "main.py"
 *     ["full_path"]=> string(7) "main.py"
 *     ["type"]=> string(13) "text/x-python"
 *     ["tmp_name"]=> string(27) "/tmp/php50bfeuogt9qmazNjjmL"
 *     ["error"]=> int(0) 
 *     ["size"]=> int(262) 
 *   } 
 * } 
 */

// Verificamos Si El fichero Fue Subido Correctamente Y Si Todos Los Datos Relacionados Con El fichero Están Disponibles

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_FILES["uploadedFile"]) &&
    empty($_FILES["uploadedFile"]["name"]) &&
    empty($_FILES["uploadedFile"]["type"]) &&
    empty($_FILES["uploadedFile"]["full_path"]) &&
    empty($_FILES["uploadedFile"]["tmp_name"]) &&
    empty($_FILES["uploadedFile"]["error"]) &&
    empty($_FILES["uploadedFile"]["size"])
) $error = "Select a File";

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    !isset($_POST["theme"]) &&
    isset($_FILES["uploadedFile"])
) {
    $nameFile = $_FILES["uploadedFile"]["name"];
    $typeFile = $_FILES["uploadedFile"]["type"];
    $fullPathFile = $_FILES["uploadedFile"]["full_path"];
    $tmpNameFile = $_FILES["uploadedFile"]["tmp_name"];
    $errorFile = $_FILES["uploadedFile"]["error"];
    $sizeFile = $_FILES["uploadedFile"]["size"];


    $pathFile = "{$_SESSION['directoryPath']}/$nameFile";
    $existingFile =  $connection->prepare("SELECT * FROM files WHERE id_user = :id_user AND path = :path");
    $existingFile->execute([
        ":id_user" => $_SESSION['user']['id'],
        ":path" => addslashes($pathFile)
    ]);
    $modificationDate = date("Y-m-d H:i:s", filemtime($tmpNameFile));

    if ($existingFile->rowCount() === 0) {
        // * Add File
        require_once "./utils/add_file.php";
    }


    // * Update File
    require_once "./utils/update_file.php";
}
?>