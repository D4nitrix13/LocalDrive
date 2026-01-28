<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: ./register.php");
    exit();
}
$error = null;

$connection = require_once "./sql/db.php";
require_once "./sql/redis.php";

require_once "./utils/functions.php";
require_once "./utils/delete_file.php";
require_once "./utils/upload_file.php";
require_once "./utils/add_directory.php";
require_once "./utils/directory_size.php";

$redis = redis_client();

$statement = $connection->prepare("SELECT * FROM directory WHERE id_user = :id_user");
$statement->execute([
    ":id_user" => $_SESSION["user"]["id"]
]);

// * Lista De Directorios Existentes Para El User Actual
$listExistingDirectory = $statement->fetchAll(PDO::FETCH_ASSOC);
$directoryPrincipal = "{$listExistingDirectory[0]["path"]}";

// List of valid directories
$listOfValidDirectories = array();
for ($i = 0; $i < count($listExistingDirectory); $i++) {
    $listOfValidDirectories[] = addslashes($listExistingDirectory[$i]["path"]);
}

if ($_SERVER["REQUEST_METHOD"] === "GET" && !isset($_SERVER["QUERY_STRING"])) {
    header("Location: .{$_SERVER['PHP_SELF']}?directory=$directoryPrincipal");
    exit();
}

$index = null;

// * Esta Array Asociativo Almacena Los Query Params Con Su Key => Value Para Luego Verificar Que El Valor De La Key directory Es La Correspondiente
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_SERVER["QUERY_STRING"])) {
    $listQueryStringDirectory = array();
    parse_str($_SERVER["QUERY_STRING"], $listQueryStringDirectory);

    if (
        !in_array("directory", array_keys($listQueryStringDirectory)) ||
        !in_array($listQueryStringDirectory["directory"], $listOfValidDirectories)
    ) {
        header("Location: .{$_SERVER['PHP_SELF']}?directory=$directoryPrincipal");
        exit();
    }

    $index = array_search($listQueryStringDirectory["directory"], $listOfValidDirectories, true);

    if ($index === false) {
        echo "[x] Error El Directory {$listQueryStringDirectory["directory"]} Not Exist";
        exit();
    }

    $_SESSION["directoryPath"] = "$listOfValidDirectories[$index]";
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION["directoryPath"])) {
    $directoryActual = $_SESSION["directoryPath"];

    if (!in_array($directoryActual, $listOfValidDirectories)) {
        header("Location: .{$_SERVER['PHP_SELF']}?directory=$directoryPrincipal");
        exit();
    }

    $index = array_search($directoryActual, $listOfValidDirectories, true);

    if ($index === false) {
        echo "[x] Error El Directory $directoryActual Not Exist";
        exit();
    }

    $_SESSION["directoryPath"] = "$listOfValidDirectories[$index]";
}

// En esta variable almacenaramos los subdirectorios
$listSubDirectory = array();
$directoryUser = $listOfValidDirectories[$index];

// Cachear tamaño del directorio principal en Redis con clave robusta
$directorySizeCacheKey = sprintf(
    'user:%d:directory:%s:size',
    $_SESSION['user']['id'],
    hash('sha256', $directoryUser) // hash robusto del path
);

$directorySizeBytes = null;

$cachedDirectorySize = $redis->get($directorySizeCacheKey);
if ($cachedDirectorySize !== false) {
    $directorySizeBytes = (int) $cachedDirectorySize;
} else {
    $directorySizeBytes = folderSize($directoryUser);
    // Cache con TTL de 60 segundos (ajusta a gusto)
    $redis->setex($directorySizeCacheKey, 60, (string) $directorySizeBytes);
}

// * Obtenemos El Directorio Padre Filtrando Por El Directorio Actual Del User
$statement = $connection->prepare("SELECT parent_directory FROM directory WHERE id_user = :id_user AND path = :path LIMIT 1");
$statement->execute([
    ":id_user" => $_SESSION["user"]["id"],
    ":path" => addslashes($directoryUser)
]);

$parentDirectoryActual = $statement->fetch(PDO::FETCH_ASSOC)["parent_directory"];

if (is_dir($directoryUser)) {
    foreach (new DirectoryIterator($directoryUser) as $index => $entry) {
        if ($entry->isDot()) continue;
        if ($entry->isDir()) {
            $listSubDirectory[] = $entry->getFilename();
        }
    }
} else {
    die("[x] El Directory No Existe: $directoryUser" . PHP_EOL);
}

$appRootDirectory = realpath(dirname(__FILE__));

$directoryAppIcon = $appRootDirectory . "/static/icon/";

$listFiles = [];

foreach (new DirectoryIterator($directoryUser) as $indice => $file) {
    if ($file->isDot()) continue;
    if ($file->isFile()) {
        $listFiles[] = $file->getFilename();
    }
}
?>
<?php require __DIR__ . "/partials/header.php" ?>

<body>
    <?php require __DIR__ . "/partials/navbar.php" ?>
    <?php if ($error): ?>
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
            <div class="alert alert-danger d-flex align-items-center"
                role="alert">
                <svg class="bi flex-shrink-0 me-2"
                    width="26"
                    height="26"
                    role="img"
                    aria-label="Danger:">
                    <use xlink:href="#exclamation-triangle-fill" />
                </svg>
                <div>
                    <?= $error ?>
                </div>
            </div>
        </div>
    <?php endif ?>
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

    <!-- 0 - 2 -> verde, 3 -> 5 azul , 6 -> 8 amarillo 9 -> 10 rojo -->
    <div class="container mb-3 mt-4 pt-2 d-grid gap-2">
        <h1 class="justify-content-center align-items-center">Espacio Disponible</h1>

        <?php
        // Usamos el valor cacheado del tamaño
        $opcion = availableSpaceGb($directorySizeBytes);
        // Calcula el porcentaje de espacio utilizado
        $usedPercentage = 100 - ($opcion * 10);
        ?>
        <!-- Barra Red -->
        <?php if ($opcion <= 0 && $opcion <= 2): ?>
            <div class="progress"
                role="progressbar"
                aria-label="Danger example"
                aria-valuenow="100"
                aria-valuemin="0"
                aria-valuemax="100">
                <div class="progress-bar bg-danger"
                    style="width: <?= $usedPercentage ?>%"></div>
            </div>
            <!-- Barra Amarillo -->
        <?php elseif ($opcion >= 3 && $opcion <= 5): ?>
            <div class="progress"
                role="progressbar"
                aria-label="Warning example"
                aria-valuenow="75"
                aria-valuemin="0"
                aria-valuemax="100">
                <div class="progress-bar bg-warning"
                    style="width: <?= $usedPercentage ?>%"></div>
            </div>
            <!-- Barra Amarillo -->
        <?php elseif ($opcion >= 6 && $opcion <= 8): ?>
            <div class="progress"
                role="progressbar"
                aria-label="Info example"
                aria-valuenow="50"
                aria-valuemin="0"
                aria-valuemax="100">
                <div class="progress-bar bg-info"
                    style="width: <?= $usedPercentage ?>%"></div>
            </div>
        <?php else: ?>
            <div class="progress"
                role="progressbar"
                aria-label="Success example"
                aria-valuenow="25"
                aria-valuemin="0"
                aria-valuemax="100">
                <div class="progress-bar bg-success"
                    style="width: <?= $usedPercentage ?>%"></div>
            </div>
        <?php endif; ?>
    </div>
    <!-- 10 GB -> Bytes -> 10737418240   -->
    <?php if ($directorySizeBytes < 10737418240): ?>
        <div class="container mb-3 mt-4 pt-2 d-grid gap-2">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Hi <?= $_SESSION["user"]["email"] ?>!</strong>
                <br>
                <em>
                    Tienes <?= availableSpaceMb($directorySizeBytes); ?> Mb De Espacio Disponible.
                    Puedes Subir Ficheros Con Un Peso Maximo De 3221225000 Bytes O Crear Directorios
                </em>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>

        <form action="<?= $_SERVER["PHP_SELF"] ?>" method="post" enctype="multipart/form-data">
            <div class="container mb-3 mt-4">
                <label for="formInput" class="form-label">Seleccione Un Fichero</label>
                <input class="form-control" required autofocus type="file" id="formInput" name="uploadedFile">
                <button type="submit" id="buttonUploadForm" class="btn btn-primary mt-2" style="width: auto;">Subir Fichero</button>
            </div>
        </form>
    <?php else: ?>
        <div class="container mb-3 mt-4 pt-2 d-grid gap-2">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Hi <?= $_SESSION["user"]["email"] ?>!</strong>
                <br>
                <em>Te Has Quedado Sin Espacio. Has Ocupado Tus 10 Gb De Espacio Disponible. Elimina Algunos Ficheros O Directorios Para Obtener Más Espacio.</em>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    <?php endif; ?>
    <div class="container mb-3 mt-4 pt-2 d-grid gap-2">
        <hr>
        <!-- 10 GB -> Bytes -> 10737418240   -->
        <?php if ($directorySizeBytes < 10737418240): ?>
            <form class="row g-3" method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                <div class="col-auto">
                    <input type="text"
                        class="form-control"
                        name="inputNameDirectory"
                        id="inputNameDirectory"
                        placeholder="Directory Name"
                        autofocus
                        required
                        autocomplete="additional-name">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mb-3">Create Directory</button>
                </div>
            </form>
        <?php endif; ?>
        <?php if ($parentDirectoryActual !== $appRootDirectory): ?>
            <?php
            $params = http_build_query([
                "directory" => $parentDirectoryActual,
            ]);
            ?>
            <a href="<?= $_SERVER['PHP_SELF'] . "?" . $params ?>"
                class="btn btn-outline-info d-flex align-items-center justify-content-between w-100">
                <label class="me-2">
                    Parent Directory (Retroceder Un Directorio)
                </label>
                <span class="me-2">Propietario:
                    <?= htmlspecialchars($_SESSION['user']['email']) ?>
                </span>
                <img style="width: 2.0rem;" src="./static/icon/directory.png" alt="Icon Directory">
            </a>
        <?php endif ?>
        <?php for ($i = 0; $i < count($listSubDirectory); $i++): ?>
            <div class="d-flex align-items-center justify-content-between mb-2">
                <?php
                $params = http_build_query([
                    "directory" => addslashes($directoryUser . DIRECTORY_SEPARATOR . $listSubDirectory[$i]),
                ]);

                // Cachear tamaño de cada subdirectorio
                $subDirPath = $directoryUser . DIRECTORY_SEPARATOR . $listSubDirectory[$i];

                $subDirSizeCacheKey = sprintf(
                    'user:%d:subdir:%s:size',
                    $_SESSION['user']['id'],
                    hash('sha256', $subDirPath)
                );

                $subDirSizeBytes = null;
                $cachedSubDirSize = $redis->get($subDirSizeCacheKey);

                if ($cachedSubDirSize !== false) {
                    $subDirSizeBytes = (int) $cachedSubDirSize;
                } else {
                    $subDirSizeBytes = folderSize($subDirPath);
                    $redis->setex($subDirSizeCacheKey, 60, (string) $subDirSizeBytes);
                }
                ?>
                <!-- Botón grande -->
                <a
                    href="<?= $_SERVER['PHP_SELF'] . "?" . $params ?>"
                    class="btn btn-outline-info d-flex align-items-center justify-content-between w-100">
                    <label class="me-2">
                        <?= htmlspecialchars($listSubDirectory[$i]) ?>
                    </label>
                    <span class="me-2">Propietario:
                        <?= htmlspecialchars($_SESSION['user']['email']) ?>
                    </span>
                    <span class="me-2">Size:
                        <?= sizeFormat($subDirSizeBytes); ?>
                    </span>
                    <img style="width: 2.0rem;" src="./static/icon/directory.png" alt="Icon Directory">
                </a>

                <form
                    method="post"
                    action="./delete_directory.php?id=<?= $_SESSION['user']['id'] ?>">
                    <button class="ms-2 btn btn-outline-danger" name="directoryRemove" autofocus value="<?= $directoryUser . '/' . $listSubDirectory[$i] ?>">
                        <img style="width: 2.0rem; cursor: pointer;"
                            src="./static/icon/removeDirectory.png"
                            alt="Remove Directory">
                    </button>
                </form>
                <?php
                $params = http_build_query([
                    "shareDirectory" => urlencode($directoryUser . '/' . $listSubDirectory[$i]),
                    "id" => $_SESSION['user']['id']
                ]);
                ?>
                <a href="./shared_directory_forms.php?<?= $params ?>" class="ms-2 btn btn-outline-primary" name="directoryRemove" autofocus>
                    <img style="width: 2.0rem; cursor: pointer;"
                        src="./static/icon/compartir.png"
                        alt="icon">
                </a>
            </div>
        <?php endfor; ?>

        <hr>
    </div>

    <?php foreach ($listFiles as $indice => $file): ?>
        <?php
        // Cachear metadatos del fichero
        $fileMetaCacheKey = sprintf(
            'user:%d:file:%s:meta',
            $_SESSION['user']['id'],
            hash('sha256', $file)
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
                ":id" => $_SESSION['user']['id'],
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
                                    <?= $_SESSION["user"]["email"] ?>
                                </label>
                                <?php $icon = getFileExtension($directoryUser, $file); ?>
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
                                        <?php if (isset($data["type"])): ?>
                                            <?= $data["type"] ?>
                                        <?php endif; ?>
                                    </li>
                                    <li class="list-group-item m-1">Size:
                                        <?php if (isset($data["size"])): ?>
                                            <?= $data["size"] ?> bytes
                                        <?php endif; ?>
                                    </li>
                                    <li class="list-group-item m-1">Fecha Creacion Del Fichero:
                                        <?php if (isset($data["file_creation_date"])): ?>
                                            <?= date("Y-m-d H:i:s", strtotime($data["file_creation_date"])) ?>
                                        <?php endif; ?>
                                    </li>
                                </ul>
                                <div class="card-body d-flex gap-3">
                                    <?php if (isset($data["path"])): ?>
                                        <a href="./download.php?file=<?= urlencode($data['path']) ?>&id=<?= $_SESSION['user']['id'] ?>"
                                            class="card-link">Descargar</a>
                                    <?php endif; ?>

                                    <?php if (isset($data["type"])): ?>
                                        <?php
                                        require_once "./utils/list.php";
                                        if (in_array($data["type"], $listContentType)): ?>
                                            <a href="serve_file.php?file=<?= urlencode($data['path']) ?>&id=<?= $_SESSION['user']['id'] ?>"
                                                class="card-link">Ver Contenido</a>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if (isset($data["path"])): ?>
                                        <a href="<?= $_SERVER['PHP_SELF'] ?>?file=<?= urlencode($data['path']) ?>&id=<?= $_SESSION['user']['id'] ?>"
                                            class="card-link">Eliminar</a>
                                    <?php endif; ?>

                                    <?php
                                    if (isset($data["path"])) {
                                        $params = http_build_query([
                                            "sharedFile" => urlencode($data["path"]),
                                            "id" => $_SESSION['user']['id']
                                        ]);
                                    }
                                    ?>

                                    <a href="shared_file_forms.php?<?= $params ?>"
                                        class="card-link">Shared File</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

</body>
<?php require __DIR__ . "/partials/footer.php" ?>

</html>