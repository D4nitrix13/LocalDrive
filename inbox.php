<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("Location: ./register.php");
    exit();
}

$id_user    = (int) $_SESSION["user"]["id"];
$connection = require "./sql/db.php";
require_once "./sql/redis.php";

$redis = redis_client();
$cacheKeyNotifications = sprintf('user:%d:notifications', $id_user);

// =======================================
// Marcar notificación como leída + cache
// =======================================
if (
    $_SERVER["REQUEST_METHOD"] === "GET" &&
    isset($_GET["id_user"]) &&
    isset($_GET["id_notification"]) &&
    isset($_GET["leido"]) &&
    (int) $_GET["id_user"] === $id_user &&
    in_array((int) $_GET["leido"], [0, 1], true)
) {

    // Verificamos si la notificación existe
    $statement = $connection->prepare(
        "SELECT 1 FROM notification WHERE id = :id LIMIT 1"
    );
    $statement->execute([
        ":id" => (int) $_GET["id_notification"],
    ]);

    $existeIdNotification = (bool) $statement->fetch(PDO::FETCH_ASSOC);

    if (!$existeIdNotification) {
        http_response_code(403);
        echo "[x] 403 Forbidden Acceso Denegado. No Tienes Permiso Para Acceder A Este Recurso.";
        exit();
    }

    try {
        // Iniciar la transacción
        $connection->beginTransaction();

        // Actualizar notificación como vista
        $statement = $connection->prepare(
            "UPDATE notification
             SET visto = TRUE
             WHERE id_user_destinatario = :id_user
               AND id = :id_notification"
        );
        $statement->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $statement->bindParam(':id_notification', $_GET["id_notification"], PDO::PARAM_INT);

        $statement->execute();

        // Confirmar los cambios (COMMIT)
        $connection->commit();

        // Invalidar cache de notificaciones de este usuario
        $redis->del($cacheKeyNotifications);
    } catch (PDOException $e) {
        // Si hay un error, hacer rollback y mostrar el error
        $connection->rollBack();
        echo "[x] Error: " . $e->getMessage();
        exit();
    }

    $params = http_build_query([
        "id" => $id_user,
    ]);
    header("Location: " . $_SERVER['PHP_SELF'] . "?" . $params);
    exit();
}

// =======================================
// Obtener notificaciones (con cache Redis)
// =======================================

$listdataNotification = [];

$cached = $redis->get($cacheKeyNotifications);
if ($cached !== false) {
    $decoded = json_decode($cached, true);
    if (is_array($decoded)) {
        $listdataNotification = $decoded;
    }
}

if (!$listdataNotification) {
    // Traemos todo de una vez, ya con email del remitente
    $statement = $connection->prepare(
        "SELECT n.*,
                u.email AS email_remitente
         FROM notification n
         INNER JOIN users u ON u.id = n.id_user_remitente
         WHERE n.id_user_destinatario = :id_user_destinatario
         ORDER BY n.id DESC"
    );
    $statement->bindParam(":id_user_destinatario", $id_user, PDO::PARAM_INT);
    $statement->execute();

    $listdataNotification = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Guardar en cache (TTL 60s)
    $redis->setex($cacheKeyNotifications, 60, json_encode($listdataNotification));
}

$quantityNotification = count($listdataNotification);
$hasNotifications     = $quantityNotification > 0;

?>
<?php require __DIR__ . "/partials/header.php" ?>
<?php require __DIR__ . "/partials/navbar.php" ?>

<body>
    <div class="container pt-4 p-3">
        <div class="row">
            <?php if (!$hasNotifications): ?>
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
        <?php $notification = $listdataNotification[$i]; ?>
        <div class="card container mt-4 pt-2 d-grid gap-2">
            <div class="card-header">
                Remitente <?= htmlspecialchars($notification["email_remitente"]) ?>
            </div>
            <div class="card-body">
                <h5 class="card-title">
                    <?= htmlspecialchars($notification["motivo"]) ?>
                </h5>
                <p class="card-text">
                    <?= nl2br(htmlspecialchars($notification["message"])) ?>
                </p>
                <?php
                $params = http_build_query([
                    "id_user"        => $_SESSION['user']['id'],
                    "leido"          => (int) $notification["visto"],
                    "id_notification" => (int) $notification["id"],
                ]);
                ?>
                <a href="<?= $_SERVER['PHP_SELF'] . "?" . $params ?>"
                    class="btn btn-<?= $notification["visto"] ? "success" : "danger" ?>">
                    Marcar como leído
                </a>
            </div>
        </div>
    <?php endfor ?>

</body>
<?php require __DIR__ . "/partials/footer.php" ?>

</html>