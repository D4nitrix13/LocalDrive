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
    $emailDestinatario = $_POST["email"];
    $message = $_POST["message"];
    $sharedFile = urlencode($_SESSION["sharedFile"]);

    $statement = $connection->prepare(
        "SELECT path
            FROM shared_directory
            WHERE id_user_guest = (
                SELECT id FROM users WHERE email = :email
            )"
    );
    $statement->execute([
        ":email" => $emailDestinatario
    ]);

    $listSharedDirectory = $statement->fetchAll(PDO::FETCH_ASSOC);

    $listFiles = array();
    $hasAlreadyBeenShared = false;
    foreach ($listSharedDirectory as $key => $value) {
        $allSubDirectory = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($value["path"]),
            RecursiveIteratorIterator::SELF_FIRST // Asegura Que Se Procesen Directorios Primero Osea Ignorela los Is Dot (., ..)
        );
        foreach ($allSubDirectory as $entry) if ($entry->isFile()) $listFiles[] = $entry->getPathname();
        if (in_array(urldecode($_SESSION["sharedFile"]), $listFiles)) {
            $hasAlreadyBeenShared = true;
            break;
        }
    }

    if ($hasAlreadyBeenShared) {
        $_SESSION["flash"] = [
            "message" => "The File You Want To Share Has Already Been Shared",
            "class" => "alert alert-danger d-flex align-items-center",
            "aria-label" => "Danger:",
            "xlink:href" => "#exclamation-triangle-fill"
            // Colour Red
        ];
        header("Location: ./home.php");
        exit();
    }

    // We Check If There Is A Record Where The File To Be Shared Exists To Avoid Duplicates 
    $statement = $connection->prepare("SELECT * FROM shared_file WHERE id_user_guest = (SELECT id FROM users WHERE email = :email) AND path = :path LIMIT 1");
    $statement->execute([
        ":email" => $emailDestinatario,
        ":path" => addslashes($_SESSION["sharedFile"])
    ]);


    $listFiles = array();
    // $hasAlreadyBeenShared = false;

    // * We Check If There Is A Record Where The File To Be Shared Exists To Avoid Duplicates 
    if ($statement->rowCount() > 0) {
        $_SESSION["flash"] = [
            "message" => "The File You Want To Share Has Already Been Shared",
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
            'sharedFile' => $sharedFile,
            'id' => $_SESSION["user"]["id"],
        ]);

        header("Location: {$_SERVER['PHP_SELF']}?$params");
        exit();
    }

    $statement = $connection->prepare("SELECT function_insert_data_notification(:id_user_remitente, :email, :motivo ,:message)");
    $statement->execute([
        ":id_user_remitente" => $_SESSION['user']['id'],
        ":email" => $emailDestinatario,
        ":motivo" => "Shared File " . urldecode($sharedFile),
        ":message" => $message
    ]);


    $pathFile = urldecode($sharedFile);

    // * Importante: Debido a la abstracción de la aplicación, en lugar de pasar el email del destinatario, se utiliza el ID del usuario destinatario. Sin embargo, para facilitar la lógica y la comprensión del programador, trabajamos con el email en lugar del ID.


    if ( is_file ( urldecode( $sharedFile ) ) ) {
        $pathFile = urldecode($sharedFile);
        $statement = $connection->prepare(
            "SELECT function_insert_data_shared_file(:id_user_property, :email_destinatario, :shared_from_a_directory, :path) AS value"
        );
        $statement->bindValue(":id_user_property", $_SESSION['user']['id'], PDO::PARAM_INT);
        $statement->bindValue(":email_destinatario", $emailDestinatario, PDO::PARAM_STR);
        $statement->bindValue(":shared_from_a_directory", false, PDO::PARAM_BOOL);
        $statement->bindValue(":path", addslashes($pathFile), PDO::PARAM_STR);
        $statement->execute();
        
    }

    $successfulConsultation = $statement->fetch(PDO::FETCH_ASSOC)["value"];

    if (!$successfulConsultation) {
        echo "[x] Error Query Consult";
        exit();
    }


    // Mensaje
    $_SESSION["flash"] = [
        "message" => "Shared File $pathFile",
        "class" => "alert alert-primary d-flex align-items-center",
        "aria-label" => "Info:",
        "xlink:href" => "#info-fill"
        // Colour Blue
    ];
    unset($_SESSION["sharedFile"]);


    header("Location: ./home.php");
    exit();
}

if (
    $_SERVER["REQUEST_METHOD"] === "GET" &&
    isset($_GET["id"]) &&
    (int)$_SESSION["user"]["id"] === (int)$_GET["id"] || (int)$_SESSION["user"]["id"] === (int)$_GET["id"] && isset($_GET["sharedFile"])
) {
    $sharedFile = urldecode($_GET["sharedFile"]);

    $fileUser = $connection->query("SELECT path FROM files WHERE id_user = {$_SESSION['user']['id']} LIMIT 1")->fetch(PDO::FETCH_ASSOC)["path"];

    $statement = $connection->prepare("SELECT path FROM files WHERE id_user = :id_user AND path = :path LIMIT 1");
    $statement->bindParam(":id_user", $_SESSION["user"]["id"]);
    $sharedFile = addslashes($sharedFile);
    $statement->bindParam(":path", $sharedFile);
    $statement->execute();

    $dataFilePath = $statement->fetch(PDO::FETCH_ASSOC)["path"];

    $emailsUser = $connection->query(
        "SELECT email FROM users WHERE id NOT IN ({$_SESSION['user']['id']})"
    )->fetchAll(PDO::FETCH_ASSOC);

    $listEmailsUsers = array();

    for ($i = 0; $i < count($emailsUser); $i++) $listEmailsUsers[] = $emailsUser[$i]["email"];

    $_SESSION["sharedFile"] = $sharedFile;
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
                        <label for="labelsharedFile"
                            class="col-sm-auto col-form-label">
                            Share file
                        </label>
                        <label for="labelsharedFile"
                            class="col-sm-auto col-form-label">
                            <strong>
                                <em>
                                    <?= $dataFilePath ?>
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