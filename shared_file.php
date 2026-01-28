<?php
session_start();
$connection = require "./sql/db.php";
require_once "./utils/functions.php";
require_once "./sql/redis.php";

$appRootDirectory = realpath(dirname(__FILE__));
$directoryAppIcon = $appRootDirectory . "/static/icon/";

$redis = redis_client();
$userGuestId = $_SESSION["user"]["id"];

// =======================================================
// 1) Cachear lista de ficheros compartidos directamente (no desde directorio)
// =======================================================
$sharedFilesListCacheKey = sprintf(
    'user:%d:shared:files:direct',
    $userGuestId
);

$listSharedFile = [];

$cachedSharedFilesList = $redis->get($sharedFilesListCacheKey);
if ($cachedSharedFilesList !== false) {
    $listSharedFile = json_decode($cachedSharedFilesList, true) ?? [];
} else {
    $statement = $connection->prepare(
        "SELECT u.email,
                s.id_user_property AS id,
                s.path,
                f.name
         FROM shared_file s
         JOIN users u ON u.id = s.id_user_property
         JOIN files f ON f.path = s.path 
         WHERE s.id_user_guest = :id
           AND s.shared_from_a_directory = false"
    );
    $statement->execute([
        ":id" => $userGuestId
    ]);
    $listSharedFile = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Cacheamos la lista de ficheros compartidos (TTL 60s)
    $redis->setex($sharedFilesListCacheKey, 60, json_encode($listSharedFile));
}
?>

<?php require __DIR__ . "/partials/header.php" ?>

<body>
    <?php require __DIR__ . "/partials/navbar.php" ?>
    <div class="container mb-3 mt-4 pt-2 d-grid gap-2">
        <h2 class="justify-content-center align-items-center">Shared Files</h2>
        <hr>
        <div class="container mb-3 mt-4 pt-2 d-grid gap-2">
            <!-- Files -->
            <?php foreach ($listSharedFile as $key => $file): ?>
                <?php
                // =======================================================
                // 2) Cachear metadatos de cada fichero compartido
                // =======================================================

                // Usamos la ruta completa como parte de la clave para evitar colisiones
                $fileMetaCacheKey = sprintf(
                    'shared:file:owner:%d:%s:meta',
                    $file["id"],                         // id_user_property
                    hash('sha256', $file["path"])        // path único del fichero
                );

                $data = null;
                $cachedFileMeta = $redis->get($fileMetaCacheKey);

                if ($cachedFileMeta !== false) {
                    $data = json_decode($cachedFileMeta, true);
                } else {
                    $statement = $connection->prepare(
                        "SELECT name, type, path, size, file_creation_date
                         FROM files
                         WHERE id_user = :id
                           AND name = :name
                         LIMIT 1"
                    );

                    $statement->execute([
                        ":id" => $file["id"],       // propietario
                        ":name" => $file["name"],
                    ]);
                    $data = $statement->fetch(PDO::FETCH_ASSOC) ?: [];

                    if (!empty($data)) {
                        $redis->setex($fileMetaCacheKey, 60, json_encode($data));
                    }
                }
                ?>
                <div class="container pt-4 p-3">
                    <div class="row">
                        <div class="accordion"
                            id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button d-flex align-items-center"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse<?= $key ?>"
                                        aria-expanded="true"
                                        aria-controls="collapse<?= $key ?>">
                                        <span class="me-auto">
                                            <?= htmlspecialchars($file["name"]) ?>
                                        </span>
                                        <label class="me-2">Propietario:
                                            <?= htmlspecialchars($file["email"]) ?>
                                        </label>

                                        <?php
                                        $icon = getFileExtension(dirname(realpath($file["path"])), $file["name"]);
                                        ?>
                                        <img style="width: 2.0rem;"
                                            class="ms-auto"
                                            src="./static/icon/<?= detectExtension($icon); ?>.png"
                                            alt="Icon">
                                    </button>
                                </h2>
                                <div id="collapse<?= $key ?>"
                                    class="accordion-collapse collapse show"
                                    data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <div class="card-body">
                                            <h5 class="card-title"></h5>
                                        </div>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item m-1">Type:
                                                <?= isset($data["type"]) ? htmlspecialchars($data["type"]) : "" ?>
                                            </li>
                                            <li class="list-group-item m-1">Size:
                                                <?= isset($data["size"]) ? (int)$data["size"] . " bytes" : "" ?>
                                            </li>
                                            <li class="list-group-item m-1">Fecha Creacion Del Fichero:
                                                <?= isset($data["file_creation_date"])
                                                    ? date("Y-m-d H:i:s", strtotime($data["file_creation_date"]))
                                                    : "" ?>
                                            </li>
                                        </ul>
                                        <div class="card-body d-flex gap-3">
                                            <?php if (isset($data["path"])): ?>
                                                <?php
                                                $params = http_build_query([
                                                    "idProperty" => $file["id"],
                                                    "idGuest" => $_SESSION['user']['id'],
                                                    "sharedFilePath" => $data["path"]
                                                ]);
                                                ?>
                                                <a href="./download_shared_file.php?<?= $params ?>"
                                                    class="card-link">Descargar</a>
                                            <?php endif; ?>

                                            <?php
                                            require_once "./utils/list.php";
                                            if (
                                                isset($data["type"], $data["path"]) &&
                                                in_array($data["type"], $listContentType)
                                            ):
                                                $params = http_build_query([
                                                    "idProperty" => $file["id"],
                                                    "idGuest" => $_SESSION['user']['id'],
                                                    "sharedFilePath" => $data["path"]
                                                ]);
                                            ?>
                                                <a href="serve_shared_file.php?<?= $params ?>"
                                                    class="card-link">Ver Contenido</a>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
<?php require __DIR__ . "/partials/footer.php" ?>

</html>