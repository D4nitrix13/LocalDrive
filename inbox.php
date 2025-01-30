<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: ./register.php");
    exit();
}

$id_user = $_SESSION["user"]["id"];
$connection = require "./sql/db.php";

if (
    $_SERVER["REQUEST_METHOD"] === "GET" and
    isset($_GET["id_user"]) and
    isset($_GET["id_notification"]) and
    isset($_GET["leido"]) and
    (int)$_GET["id_user"] === (int)$id_user and
    in_array($_GET["leido"], [1, 0])
) {

    // * Verificamos si el valor de la key visto es true or false Significado (1 -> true, 0 -> false)
    $statement = $connection->prepare("SELECT * FROM notification WHERE id = :id LIMIT 1");
    $statement->execute([
        "id" => $_GET["id_notification"]
    ]);

    $existeIdNotification = (bool)$statement->fetch(PDO::FETCH_ASSOC);
    // * (bool) -> (boolean)
    if ( !$existeIdNotification ) {
        http_response_code(403);
        echo "[x] 403 Forbidden Acceso Denegado. No Tienes Permiso Para Acceder A Este Recurso.";
    }

    try {
        // Iniciar la transacción
        $connection->beginTransaction();

        // Preparar la consulta de actualización
        $statement = $connection->prepare("UPDATE notification SET visto = TRUE WHERE id_user_destinatario = :id_user AND id = :id_notification");
        $statement->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $statement->bindParam(':id_notification', $_GET["id_notification"], PDO::PARAM_INT);

        // Ejecutar la consulta de actualización
        $statement->execute();

        // Confirmar los cambios (COMMIT)
        $connection->commit();
    } catch (PDOException $e) {
        // Si hay un error, hacer rollback y mostrar el error
        $connection->rollBack();
        echo "[x] Error: " . $e->getMessage();
        exit();
    }

    $params = http_build_query([
        "id" => $id_user
    ]);
    header("Location: " . $_SERVER['PHP_SELF'] . "?" . $params);
    exit();
}

$quantityNotification = $connection->query("SELECT count(*) AS quantity_notification FROM notification WHERE id_user_destinatario = $id_user")->fetch(PDO::FETCH_ASSOC)["quantity_notification"];

$statement = $connection->prepare("SELECT * FROM notification WHERE id_user_destinatario = :id_user_destinatario");
$statement->bindParam(":id_user_destinatario", $id_user);
$statement->execute();

$listdataNotification = $statement->fetchAll(PDO::FETCH_ASSOC);

$register = $connection->query("SELECT * FROM notification WHERE id_user_destinatario = $id_user");
?>

<?php require __DIR__ . "/partials/header.php" ?>
<?php require __DIR__ . "/partials/navbar.php" ?>

<body>
    <div class="container pt-4 p-3">
        <div class="row">
            <?php if ($register->rowCount() === 0): ?>
                <div class="col-md-auto mx-auto">
                    <div class="card card-body text-center">
                        <p>
                            <strong>
                                <em>No notification Registered</em>
                            </strong>
                        </p>
                    </div>
                </div>
            <?php endif ?>
        </div>
    </div>
    <?php for ($i = 0; $i < $quantityNotification; $i++): ?>
        <div class="card container mt-4 pt-2 d-grid gap-2">
            <div class="card-header">
                <?php $statement = $connection->prepare("SELECT email FROM users WHERE id = :id_user_remitente LIMIT 1");
                $statement->execute([
                    ":id_user_remitente" => $listdataNotification[$i]["id_user_remitente"]
                ]);
                $emailRemitentes = $statement->fetch(PDO::FETCH_ASSOC)["email"];
                ?>
                Remitente <?= $emailRemitentes ?>
            </div>
            <div class="card-body">
                <h5 class="card-title"> <?= $listdataNotification[$i]["motivo"] ?> </h5>
                <p class="card-text"> <?= $listdataNotification[$i]["message"] ?> </p>
                <?php $params = http_build_query([
                    "id_user" => $_SESSION['user']['id'],
                    "leido" => $listdataNotification[$i]["visto"],
                    "id_notification" =>  $listdataNotification[$i]["id"]
                ]);
                ?>
                <a href="<?= $_SERVER['PHP_SELF'] . "?" . $params ?>" class="btn btn-<?= ($listdataNotification[$i]["visto"]) ? "success" : "danger" ?>">Marcar como leído</a>
            </div>
        </div>

    <?php endfor ?>

</body>
<?php require __DIR__ . "/partials/footer.php" ?>

</html>