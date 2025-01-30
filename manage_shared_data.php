<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: ./register.php");
    exit();
}

$connection = require "./sql/db.php";

// Get Data Shared Directory
$statement = $connection->prepare(
    "SELECT s.id_user_guest, d.name, d.path, u.email
    FROM shared_directory s
    INNER JOIN directory d ON s.path = d.path
    INNER JOIN users u ON u.id = s.id_user_guest
    WHERE s.id_user_property = :id"
);

$statement->bindParam( ":id", $_SESSION["user"]["id"], PDO::PARAM_INT );
$statement->execute();

$diccionarioSharedDirectory = $statement->fetchAll(PDO::FETCH_ASSOC);

$statement = $connection->prepare(
    "SELECT s.id_user_guest, d.name, d.path, u.email
    FROM shared_file s
    INNER JOIN files d ON s.path = d.path
    INNER JOIN users u ON u.id = s.id_user_guest
    WHERE s.id_user_property = :id AND s.shared_from_a_directory = false"
);
$statement->bindParam(":id", $_SESSION["user"]["id"]);
$statement->execute();

$diccionarioSharedFiles = $statement->fetchAll(PDO::FETCH_ASSOC);

if (
    $_SERVER["REQUEST_METHOD"] === "GET" and
    isset( $_GET["filePath"] ) and isset( $_GET["idGuest"] )
) {
    $existSharedFile = false;
    $filePath = urldecode($_GET["filePath"]);

    foreach ($diccionarioSharedFiles as $key => $value) {
        if ($filePath !== $value["path"]) continue;
        $existSharedFile = true;

        $statement = $connection->prepare("DELETE FROM shared_file WHERE id_user_property = :id AND path = :path AND id_user_guest = :id_user_guest");
        $statement->execute([
            ":path" => $value["path"],
            ":id" => $_SESSION["user"]["id"],
            ":id_user_guest" => $_GET["idGuest"]
        ]);

        break;
    }

    $filePathMessages = basename($filePath);
    if (!$existSharedFile) {
        $_SESSION["flash"] = [
            "message" => "Error The File $filePathMessages That You Want To Stop Sharing Does Not Belong To You Or Does Not Exist",
            "class" => "alert alert-danger d-flex align-items-center",
            "aria-label" => "Danger:",
            "xlink:href" => "#exclamation-triangle-fill"
            // Colour Red
        ];
        header("Location: ./manage_shared_data.php");
        exit();
    }

    $_SESSION["flash"] = [
        "message" => "You Have Stopped Sharing The File $filePathMessages",
        "class" => "alert alert-success d-flex align-items-center",
        "aria-label" => "Success:",
        "xlink:href" => "#check-circle-fill"
        // Colour Green
    ];

    header("Location: ./manage_shared_data.php");
    exit();
} else if (
    $_SERVER["REQUEST_METHOD"] === "GET" and
    isset( $_GET["dirPath"] ) and isset( $_GET["idGuest"] )
) {
    $dirPath = urldecode($_GET["dirPath"]);
    $existSharedDir = false;

    foreach ($diccionarioSharedDirectory as $key => $value) {
        if ($dirPath !== $value["path"]) continue;
        $existSharedDir = true;

        $statement = $connection->prepare("DELETE FROM shared_directory WHERE id_user_property = :id AND path = :path AND id_user_guest = :id_user_guest");
        $statement->execute([
            ":path" => $value["path"],
            ":id" => $_SESSION["user"]["id"],
            ":id_user_guest" => $_GET["idGuest"]
        ]);

        break;
    }

    $dirPathMessages = basename($dirPath);
    if (!$existSharedDir) {
        $_SESSION["flash"] = [
            "message" => "Error The Directory $dirPathMessages That You Want To Stop Sharing Does Not Belong To You Or Does Not Exist",
            "class" => "alert alert-danger d-flex align-items-center",
            "aria-label" => "Danger:",
            "xlink:href" => "#exclamation-triangle-fill"
            // Colour Red
        ];

        header("Location: ./manage_shared_data.php");
        exit();
    }

    $_SESSION["flash"] = [
        "message" => "You Have Stopped Sharing The Directory $dirPathMessages",
        "class" => "alert alert-success d-flex align-items-center",
        "aria-label" => "Success:",
        "xlink:href" => "#check-circle-fill"
        // Colour Green
    ];

    header("Location: ./manage_shared_data.php");
    exit();
}
?>

<?php require __DIR__ . "/partials/header.php" ?>

<body>
    <?php require __DIR__ . "/partials/navbar.php" ?>
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

    <div class="container mb-3 mt-4 pt-2 d-grid gap-2">
        <h2 class="justify-content-center align-items-center">Shared Directory</h2>
        <div class="container mb-3 mt-4 pt-2 d-grid gap-2">
            <ul>
                <?php foreach ($diccionarioSharedDirectory as $key => $value): ?>
                    <li>
                        <?php $params = http_build_query([
                            "dirPath" => urlencode( addslashes( $value["path"] ) ),
                            "idGuest" => $value["id_user_guest"]
                        ]); ?>
                        <div class="custom-flex">
                            <label>
                                <strong>
                                    <em>Name Directory: <?= $value["name"] ?> </em>
                                </strong>
                            </label>
                            <a type="submit" href="<?= $_SERVER['PHP_SELF'] . '?' . $params ?>" class="btn btn-primary m-3">Stop Sharing Directory (<?= $value["email"] ?>)</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <hr>

        <h2 class="justify-content-center align-items-center">Shared Files</h2>
        <div class="container mb-3 mt-4 pt-2 d-grid gap-2">
            <ul>
                <?php foreach ($diccionarioSharedFiles as $key => $value): ?>
                    <li>
                        <?php $params = http_build_query([
                            "filePath" => urlencode( addslashes( $value["path"] ) ),
                            "idGuest" => $value["id_user_guest"]
                        ]); ?>
                        <div class="custom-flex">
                            <label>
                                <strong>
                                    <em>Name File: <?= $value["name"] ?> </em>
                                </strong>
                            </label>
                            <a href="<?= $_SERVER['PHP_SELF'] . '?' . $params ?>" type="submit" class="btn btn-primary m-3">Stop Sharing File (<?= $value["email"] ?>)</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

    </div>
</body>
<?php require __DIR__ . "/partials/footer.php" ?>

</html>