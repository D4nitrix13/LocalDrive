<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: ./register.php");
    exit();
}

require_once "./utils/functions.php";
$connection = require "./sql/db.php";
require_once "./sql/redis.php";
require_once "./utils/directory_size.php";

$redis = redis_client();

$appRootDirectory = realpath(dirname(__FILE__));

$directoryAppIcon = $appRootDirectory . "/static/icon/";

// =======================================================
// 1) Cachear lista de directorios compartidos para el invitado actual
// =======================================================

$sharedDirectoriesCacheKey = sprintf(
    'user:%d:shared:directories',
    $_SESSION['user']['id']
);

$dataSharedDirectory = [];

$cachedSharedDirectories = $redis->get($sharedDirectoriesCacheKey);
if ($cachedSharedDirectories !== false) {
    $dataSharedDirectory = json_decode($cachedSharedDirectories, true) ?? [];
} else {
    // * Other Query
    // SELECT data.*, u2.name AS "name guest" FROM (SELECT s.id_user_property AS "id property", u.name AS "name property", s.id_user_guest AS guest, s.path FROM shared_directory s INNER JOIN USERS u ON u.id = s.id_user_property WHERE s.id_user_guest = 1) AS data INNER JOIN users u2 ON u2.id = data.guest;

    $dataSharedDirectory = $connection->query(
        "SELECT s.id_user_property AS \"id property\",
                u1.email AS \"email property\",
                s.id_user_guest AS \"id guest\",
                u2.email AS \"email guest\",
                d.name AS \"name directory\",
                s.path AS \"path directory\"
        FROM shared_directory s
        INNER JOIN users u1 ON u1.id = s.id_user_property
        INNER JOIN users u2 ON u2.id = s.id_user_guest
        INNER JOIN directory d ON d.path = s.path
        WHERE s.id_user_guest = {$_SESSION['user']['id']}"
    )->fetchAll(PDO::FETCH_ASSOC);

    // Cacheamos la lista completa de directorios compartidos (TTL 60s)
    $redis->setex($sharedDirectoriesCacheKey, 60, json_encode($dataSharedDirectory));
}

$dataPropertyShared = null;
$existe = false;

// =======================================================
// 2) Resolver directorio o subdirectorio compartido solicitado
// =======================================================

if (isset($_GET["directory"]) && !isset($_GET["subdirectory"])) {
    $pathDirectory = urldecode($_GET["directory"]);

    // * Validamos si la ruta del directorio existe
    for ($i = 0; $i < count($dataSharedDirectory); $i++) {
        if ($pathDirectory !== $dataSharedDirectory[$i]["path directory"]) continue;
        $existe = true;
        // Este Array Asociativo O Diccionario Almacenar Al Informacion Del Propietario Del Directorio Y Fichero
        $dataPropertyShared = [
            "id" => $dataSharedDirectory[$i]["id property"],
            "email" => $dataSharedDirectory[$i]["email property"],
            "path directory" => $dataSharedDirectory[$i]["path directory"]
        ];
        break;
    }

    if (!$existe) {
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    }
} else if (!isset($_GET["directory"]) && isset($_GET["subdirectory"])) {
    $pathSubDirectory = urldecode($_GET["subdirectory"]);

    $directories = array();
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

        foreach ($allSubDirectory as $entry) {
            if ($entry->isDir()) {
                $directories[] = $entry->getPathname();
            }
        }

        if (in_array($pathSubDirectory, $directories)) {
            $dataPropertyShared = [
                "id" => $value["id property"],
                "email" => $value["email property"],
                "path directory" => $pathSubDirectory
            ];
            break;
        }
    }
}

$listFiles = [];

if ($dataPropertyShared !== null) {
    foreach (new DirectoryIterator($dataPropertyShared["path directory"]) as $index => $file) {
        if ($file->isDot()) continue;
        if ($file->isFile()) {
            $listFiles[] = $file->getFilename();
        }
    }
}

$listSubDirectory = [];
if ($dataPropertyShared !== null && is_dir($dataPropertyShared["path directory"])) {
    foreach (new DirectoryIterator($dataPropertyShared["path directory"]) as $index => $entry) {
        if ($entry->isDot()) continue;
        if ($entry->isDir()) {
            $listSubDirectory[] = $entry->getFilename();
        }
    }
}
?>

<?php require __DIR__ . "/partials/header.php" ?>

<body>
    <?php require __DIR__ . "/partials/navbar.php" ?>

    <div class="container mb-3 mt-4 pt-2 d-grid gap-2">
        <h2 class="justify-content-center align-items-center">Shared Directory</h2>
        <div class="container mb-3 mt-4 pt-2 d-grid gap-2">
            <hr>
            <?php if ($_SERVER["REQUEST_URI"] === $_SERVER["PHP_SELF"]): ?>
                <!-- Directories -->
                <?php for ($i = 0; $i < count($dataSharedDirectory); $i++): ?>
                    <?php
                    // Cachear tamaño de cada directorio compartido (del propietario) para el invitado
                    $sharedDirPath = $dataSharedDirectory[$i]["path directory"];

                    $sharedDirSizeCacheKey = sprintf(
                        'shared:userGuest:%d:dir:%s:size',
                        $_SESSION['user']['id'],
                        hash('sha256', $sharedDirPath)
                    );

                    $sharedDirSizeBytes = null;
                    $cachedSharedDirSize = $redis->get($sharedDirSizeCacheKey);

                    if ($cachedSharedDirSize !== false) {
                        $sharedDirSizeBytes = (int) $cachedSharedDirSize;
                    } else {
                        $sharedDirSizeBytes = folderSize($sharedDirPath);
                        $redis->setex($sharedDirSizeCacheKey, 60, (string) $sharedDirSizeBytes);
                    }
                    ?>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <?php
                        $params = http_build_query([
                            "directory" => $dataSharedDirectory[$i]["path directory"],
                        ]);
                        ?>
                        <!-- Botón grande -->
                        <a
                            href="<?= $_SERVER['PHP_SELF'] . "?" . $params ?>"
                            class="btn btn-outline-info d-flex align-items-center justify-content-between w-100">
                            <label class="me-2">
                                <?= htmlspecialchars($dataSharedDirectory[$i]["name directory"]) ?>
                            </label>
                            <span class="me-2">Propietario:
                                <?= htmlspecialchars($dataSharedDirectory[$i]["email property"]) ?>
                            </span>
                            <span class="me-2">Size:
                                <?= sizeFormat($sharedDirSizeBytes); ?>
                            </span>
                            <img style="width: 2.0rem;" src="./static/icon/directory.png" alt="Icon Directory">
                        </a>
                    </div>
                <?php endfor; ?>
            <?php else: ?>
                <!-- Button Parent Directory -->
                <?php if (true): ?>
                    <?php
                    $statement = $connection->prepare("SELECT * FROM directory WHERE path = :path LIMIT 1");
                    $statement->execute([
                        ":path" => addslashes($dataPropertyShared["path directory"])
                    ]);

                    $dataDirectoryShared = $statement->fetch(PDO::FETCH_ASSOC);
                    // * Directorio Principal Del User
                    $userRootDirectory = $appRootDirectory . DIRECTORY_SEPARATOR . "Directory" . $dataDirectoryShared["id_user"];

                    // *Con Esta Variable Verificamos Si El Directorio Padre Es Directorio Raiz Del Usuario Propietario De Ser Asi Lo Redirijamos A /shared_directory.php
                    $parentDirectoryIsRoot = false;

                    if ($userRootDirectory === $dataDirectoryShared["parent_directory"]) {
                        $parentDirectoryIsRoot = true;
                    }

                    $params = http_build_query([
                        "directory" => ($parentDirectoryIsRoot) ?  $_SERVER["PHP_SELF"] : $dataDirectoryShared["parent_directory"]
                    ]);
                    ?>
                    <a href="<?= $_SERVER['PHP_SELF'] . "?" . $params ?>"
                        class="btn btn-outline-info d-flex align-items-center justify-content-between w-100">
                        <label class="me-2">
                            Parent Directory (Retroceder Un Directorio)
                        </label>
                        <span class="me-2">Propietario:
                            <?= htmlspecialchars($dataPropertyShared["email"]) ?>
                        </span>
                        <img style="width: 2.0rem;" src="./static/icon/directory.png" alt="Icon Directory">
                    </a>
                <?php endif ?>

                <!-- SubDirectories -->
                <?php for ($i = 0; $i < count($listSubDirectory); $i++): ?>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <?php
                        $params = http_build_query([
                            "subdirectory" => urlencode($dataPropertyShared["path directory"] . DIRECTORY_SEPARATOR . $listSubDirectory[$i]),
                        ]);
                        ?>
                        <!-- Botón grande -->
                        <a
                            href="<?= $_SERVER['PHP_SELF'] . "?" . $params ?>"
                            class="btn btn-outline-info d-flex align-items-center justify-content-between w-100">
                            <label class="me-2">
                                <?= htmlspecialchars($listSubDirectory[$i]) ?>
                            </label>
                            <span class="me-2">Propietario:
                                <?= htmlspecialchars($dataPropertyShared["email"]) ?>
                            </span>
                            <img style="width: 2.0rem;" src="./static/icon/directory.png" alt="Icon Directory">
                        </a>
                    </div>
                <?php endfor; ?>
                <hr>

                <!-- Files -->
                <?php foreach ($listFiles as $indice => $file): ?>
                    <?php
                    // Cachear metadatos del fichero compartido (por propietario + path)
                    // Usamos path completo porque puede haber archivos con el mismo nombre en diferentes dirs
                    $fileFullPath = $dataPropertyShared["path directory"] . DIRECTORY_SEPARATOR . $file;

                    $fileMetaCacheKey = sprintf(
                        'shared:file:owner:%d:%s:meta',
                        $dataPropertyShared["id"],
                        hash('sha256', $fileFullPath)
                    );

                    $data = null;
                    $cachedFileMeta = $redis->get($fileMetaCacheKey);

                    if ($cachedFileMeta !== false) {
                        $data = json_decode($cachedFileMeta, true);
                    } else {
                        $statement = $connection->prepare(
                            "SELECT name, type, path, size, file_creation_date 
                             FROM files 
                             WHERE id_user = :id AND name = :name 
                             LIMIT 1"
                        );

                        $statement->execute([
                            ":id" => $dataPropertyShared["id"],
                            ":name" => $file,
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
                                            data-bs-target="#collapse<?= $indice ?>"
                                            aria-expanded="true"
                                            aria-controls="collapse<?= $indice ?>">
                                            <span class="me-auto">
                                                <?= $file ?>
                                            </span>
                                            <label class="me-2">Propietario:
                                                <?= $dataPropertyShared["email"] ?>
                                            </label>
                                            <?php $icon = getFileExtension($dataPropertyShared["path directory"], $file); ?>
                                            <img style="width: 2.0rem;"
                                                class="ms-auto"
                                                src="./static/icon/<?= detectExtension($icon); ?>.png"
                                                alt="Icon">
                                        </button>
                                    </h2>
                                    <div id="collapse<?= $indice ?>"
                                        class="accordion-collapse collapse show"
                                        data-bs-parent="#accordionExample">
                                        <div class="accordion-body">
                                            <div class="card-body">
                                                <h5 class="card-title"></h5>
                                            </div>
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item m-1">Type:
                                                    <?= isset($data["type"]) ? $data["type"] : "" ?>
                                                </li>
                                                <li class="list-group-item m-1">Size:
                                                    <?= isset($data["size"]) ? $data["size"] . " bytes" : "" ?>
                                                </li>
                                                <li class="list-group-item m-1">Fecha Creacion Del Fichero:
                                                    <?= isset($data["file_creation_date"])
                                                        ? date("Y-m-d H:i:s", strtotime($data["file_creation_date"]))
                                                        : "" ?>
                                                </li>
                                            </ul>
                                            <div class="card-body d-flex gap-3">
                                                <?php
                                                if (isset($data["path"])) {
                                                    $params = http_build_query([
                                                        "idProperty" => $dataPropertyShared["id"],
                                                        "idGuest" => $_SESSION['user']['id'],
                                                        "sharedFilePath" => $data["path"]
                                                    ]);
                                                ?>
                                                    <a href="./download_shared_file.php?<?= $params ?>"
                                                        class="card-link">Descargar</a>
                                                <?php } ?>

                                                <?php
                                                require_once "./utils/list.php";
                                                if (isset($data["type"]) && in_array($data["type"], $listContentType) && isset($data["path"])) {
                                                    $params = http_build_query([
                                                        "idProperty" => $dataPropertyShared["id"],
                                                        "idGuest" => $_SESSION['user']['id'],
                                                        "sharedFilePath" => $data["path"]
                                                    ]);
                                                ?>
                                                    <a href="serve_shared_file.php?<?= $params ?>"
                                                        class="card-link">Ver Contenido</a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>
        </div>
</body>
<?php require __DIR__ . "/partials/footer.php" ?>

</html>