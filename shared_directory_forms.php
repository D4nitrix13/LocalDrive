<?php
# Bootstrap: https://getbootstrap.com/docs/5.3/forms/floating-labels/#textareas
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: ./register.php");
    exit();
}

$connection = require "./sql/db.php";

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["email"]) && isset($_POST["message"])
) {
    require_once "./utils/functions.php";
    $emailDestinatario = $_POST["email"];
    $message = $_POST["message"];
    $shareDirectory = urlencode($_SESSION["shareDirectory"]);

    // We Check If There Is A Record Where The Directory To Be Shared Exists To Avoid Duplicates 
    $statement = $connection->prepare("SELECT * FROM shared_directory WHERE id_user_guest = (SELECT id FROM users WHERE email = :email) AND path = :path LIMIT 1");
    $statement->execute([
        ":email" => $emailDestinatario,
        ":path" => addslashes($_SESSION["shareDirectory"])
    ]);

    $dataSharedDirectory = $connection->query(
        "SELECT s.id_user_property AS \"id property\",
            u1.email AS \"email property\",
            s.id_user_guest AS \"id guest\",
            u2.email AS \"email guest\",
            d.name AS \"name directory\",
            s.path \"path directory\"
            FROM shared_directory s
            INNER JOIN users u1 ON u1.id = s.id_user_property
            INNER JOIN users u2 ON u2.id = s.id_user_guest
            INNER JOIN directory d ON d.path = s.path
            WHERE s.id_user_property = {$_SESSION['user']['id']}"
    )->fetchAll(PDO::FETCH_ASSOC);

    $directories = array();
    $hasAlreadyBeenShared = false;
    // * El arreglo asociativo $dataSharedDirectory contiene información de los directorios principales compartidos,
    //   incluyendo detalles sobre sus propietarios (por ejemplo, email).
    // * El objetivo es iterar sobre cada uno de estos directorios principales para realizar lo siguiente:
    //   1. Obtener todos los subdirectorios que pertenecen a cada directorio principal.
    //   2. Verificar si el valor proporcionado en $_GET["subdirectory"] coincide con alguno de estos subdirectorios.
    //   3. Si hay una coincidencia, extraer la información del propietario (por ejemplo, su correo electrónico) 
    //      asociada con el directorio principal donde se encontró el subdirectorio correspondiente.

    foreach ($dataSharedDirectory as $key => $value) {
        $allSubDirectory = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($value["path directory"]),
            RecursiveIteratorIterator::SELF_FIRST // Asegura Que Se Procesen Directorios Primero Osea Ignorela los Is Dot (., ..)
        );
        foreach ($allSubDirectory as $entry) if ($entry->isDir()) $directories[] = $entry->getPathname();
        if (in_array($_SESSION["shareDirectory"], $directories)) {
            $hasAlreadyBeenShared = true;
            break;
        }
    }

    if ($statement->rowCount() > 0 || $hasAlreadyBeenShared) {
        $_SESSION["flash"] = [
            "message" => "The Directory You Want To Share Has Already Been Shared",
            "class" => "alert alert-danger d-flex align-items-center",
            "aria-label" => "Danger:",
            "xlink:href" => "#exclamation-triangle-fill"
            // Colour Red
        ];
        header("Location: ./home.php");
        exit();
    }
    $emailsUser = $connection->query(
        "SELECT email FROM users WHERE id NOT IN ({$_SESSION['user']['id']})"
    )->fetchAll(PDO::FETCH_ASSOC);

    $listEmailsUsers = array();

    for ($i = 0; $i < count($emailsUser); $i++) $listEmailsUsers[] = $emailsUser[$i]["email"];

    if (empty($emailDestinatario) || trim($message) === "" || !in_array($emailDestinatario, $listEmailsUsers)) {
        $_SESSION["flash"] = [
            "message" => "Complete All Fields Correctament",
            "class" => "alert alert-danger d-flex align-items-center",
            "aria-label" => "Danger:",
            "xlink:href" => "#exclamation-triangle-fill"
            // Colour Red
        ];
        $params = http_build_query([
            'shareDirectory' => $shareDirectory,
            'id' => $_SESSION["user"]["id"],
        ]);

        header("Location: {$_SERVER['PHP_SELF']}?$params");
        exit();
    }

    $statement = $connection->prepare("SELECT function_insert_data_notification(:id_user_remitente, :email, :motivo ,:message)");
    $statement->execute([
        ":id_user_remitente" => $_SESSION['user']['id'],
        ":email" => $emailDestinatario,
        ":motivo" => "Shared Directory " . urldecode($shareDirectory),
        ":message" => $message
    ]);


    $pathDirectory = urldecode($shareDirectory);

    // * Importante: Debido a la abstracción de la aplicación, en lugar de pasar el email del destinatario, se utiliza el ID del usuario destinatario. Sin embargo, para facilitar la lógica y la comprensión del programador, trabajamos con el email en lugar del ID.


    $statement = $connection->prepare(
        "SELECT function_insert_data_shared_directory(:id_user_property, :email_destinatario, :path) AS value"
    );
    $statement->execute([
        ":id_user_property" => $_SESSION['user']['id'],
        ":email_destinatario" => $emailDestinatario,
        ":path" => addslashes($pathDirectory)
    ]);

    $successfulConsultation = $statement->fetch(PDO::FETCH_ASSOC)["value"];

    // * Cuando se utiliza DirectoryIterator, puede haber problemas al manejar rutas que contienen caracteres especiales, como espacios, caracteres acentuados o símbolos no ASCII. Esto ocurre porque DirectoryIterator depende de que la ruta proporcionada sea válida y no siempre maneja correctamente codificaciones o caracteres no estándar.
    // * Por otro lado, scandir es una función más robusta para estos casos, ya que devuelve un arreglo simple con los nombres de los Ficheros y directorios dentro del directorio especificado, sin depender de objetos iterables. Esto permite evitar errores relacionados con caracteres especiales.

    // $directoryPath = scandir( urldecode( $_SESSION["shareDirectory"] ) );

    if ( !$successfulConsultation ) {
        echo "[x] Error Query Consult";
        exit();
    }

    // * Log all files in the shared directory (important: set files shared from a directory to true)
    $files = getDirContents( urldecode ( $_SESSION["shareDirectory"] ) );
    
    foreach ($files as $key => $value) {
        $statement = $connection->prepare(
            "SELECT function_insert_data_shared_file(:id_user_property, :email_destinatario, :shared_from_a_directory, :path) AS value"
        );
        $statement->execute([
            ":id_user_property" => $_SESSION['user']['id'],
            ":email_destinatario" => $emailDestinatario,
            ":shared_from_a_directory" => true,
            ":path" => $value
        ]);
    }
    
    unset($_SESSION["shareDirectory"]);

    // Mensaje
    $_SESSION["flash"] = [
        "message" => "Shared Directory $pathDirectory",
        "class" => "alert alert-primary d-flex align-items-center",
        "aria-label" => "Info:",
        "xlink:href" => "#info-fill"
        // Colour Blue
    ];

    header("Location: ./home.php");
    exit();
}

if (
    $_SERVER["REQUEST_METHOD"] === "GET" &&
    isset($_GET["id"]) &&
    (int)$_SESSION["user"]["id"] === (int)$_GET["id"] || (int)$_SESSION["user"]["id"] === (int)$_GET["id"] && $_GET["shareDirectory"]
) {
    $shareDirectory = urldecode($_GET["shareDirectory"]);

    $directoryUser = $connection->query("SELECT path FROM directory WHERE id_user = {$_SESSION['user']['id']} LIMIT 1")->fetch(PDO::FETCH_ASSOC)["path"];

    if ($directoryUser === $shareDirectory or $directoryUser . DIRECTORY_SEPARATOR === $shareDirectory) {
        http_response_code(403);
        echo "[x] 403 Forbidden: You do not have permission to access this resource.";
        exit();
    }
    $statement = $connection->prepare("SELECT path FROM directory WHERE id_user = :id_user AND path = :path LIMIT 1");
    $statement->bindParam(":id_user", $_SESSION["user"]["id"]);
    $shareDirectory = addslashes($shareDirectory);
    $statement->bindParam(":path", $shareDirectory);
    $statement->execute();

    $dataDirectoryPath = $statement->fetch(PDO::FETCH_ASSOC)["path"];

    $emailsUser = $connection->query(
        "SELECT email FROM users WHERE id NOT IN ({$_SESSION['user']['id']})"
    )->fetchAll(PDO::FETCH_ASSOC);

    $listEmailsUsers = array();

    for ($i = 0; $i < count($emailsUser); $i++) $listEmailsUsers[] = $emailsUser[$i]["email"];

    $_SESSION["shareDirectory"] = $shareDirectory;
} else exit("[x] Metodo Http Incorrect");
?>
<?php require "./partials/header.php" ?>
<?php require "./partials/navbar.php" ?>
<?php if (isset($_SESSION["flash"])): ?>
    <svg xmlns="http://www.w3.org/2000/svg"
        class="d-none">
        <symbol id="check-circle-fill"
            viewBox="0 0 16 16">
            <path
                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
        </symbol>
        <symbol id="info-fill"
            viewBox="0 0 16 16">
            <path
                d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
        </symbol>
        <symbol id="exclamation-triangle-fill"
            viewBox="0 0 16 16">
            <path
                d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
        </symbol>
    </svg>

    <div class="container mt-4">
        <div class="<?= $_SESSION['flash']['class'] ?>"
            role="alert">
            <svg class="bi flex-shrink-0 me-2"
                role="img"
                width="26"
                height="26"
                aria-label="<?= $_SESSION['flash']['aria-label'] ?>">
                <use xlink:href="<?= $_SESSION['flash']['xlink:href'] ?>" />
            </svg>
            <div>
                <?= $_SESSION["flash"]["message"] ?>
                <?php unset($_SESSION["flash"]); ?>
            </div>
        </div>
    </div>
<?php endif ?>

<div class="container pt-3">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <form class="p-4" action="<?= $_SERVER["PHP_SELF"] ?>?id=<?= $_SESSION['user']['id'] ?>"
                    method="post">
                    <div class="row mb-3">
                        <label for="labelShareDirectory"
                            class="col-sm-auto col-form-label">
                            Share directory
                        </label>
                        <label for="labelShareDirectory"
                            class="col-sm-auto col-form-label">
                            <strong>
                                <em>
                                    <?= $dataDirectoryPath ?>
                                </em>
                            </strong>
                        </label>
                    </div>
                    <div class="row mb-3">
                        <label for="labelEmail"
                            class="col-sm-2 col-form-label">Email
                        </label>
                        <div class="col-sm-10">
                            <div class="form-floating">
                                <select class="form-select"
                                    id="floatingSelect"
                                    name="email"
                                    aria-label="Floating label select example">
                                    <option selected>Select the user's e-mail address </option>
                                    <?php foreach ($listEmailsUsers as $index => $value): ?>
                                        <option value="<?= $value ?>">
                                            <?= $index . ". " . $value ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                                <label style="color: #888;" for="floatingSelect">Works with selects</label>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputMessage"
                            class="col-sm-2 col-form-label">Message</label>
                        <div class="col-sm-10">
                            <div class="form-floating">
                                <textarea class="form-control"

                                    autocomplete="additional-name"
                                    autofocus
                                    placeholder="Leave a comment here"
                                    id="floatingTextarea"
                                    name="message"
                                    style="height: 100px">
                                </textarea>
                                <label for="floatingTextarea">Comments</label>
                            </div>
                        </div>
                    </div>
                    <button id="submitButtonEnviar"
                        type="submit"
                        class="btn btn-primary">Enviar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require "./partials/footer.php"; ?>