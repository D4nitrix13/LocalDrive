<?php
$error = null;

require_once __DIR__ . '/sql/mongo_logger.php';

/**
 * Asegura que el usuario tenga su directorio creado en disco
 * y registrado en la tabla `directory`.
 *
 * - Si no existe el directorio en disco, lo crea.
 * - Si no existe el registro en BD, lo inserta.
 * - Si ya existe todo, no hace nada.
 */
function ensure_user_directory(PDO $connection, int $userId, string $appRootDirectory): string
{
    $nameDirectory = "Directory{$userId}";
    $pathDirectory = $appRootDirectory . DIRECTORY_SEPARATOR . $nameDirectory;

    // 1) Si el directorio NO existe en disco → crearlo
    if (!is_dir($pathDirectory)) {
        if (!mkdir($pathDirectory, 0777, true) && !is_dir($pathDirectory)) {
            throw new RuntimeException("[x] No se pudo crear el directorio $pathDirectory");
        }
    }

    // 2) Verificar si ya hay registro en la tabla directory
    $statement = $connection->prepare(
        "SELECT 1 FROM directory WHERE id_user = :id_user AND path = :path LIMIT 1"
    );
    $pathEscaped = addslashes($pathDirectory);
    $statement->execute([
        ":id_user" => $userId,
        ":path"    => $pathEscaped,
    ]);

    $row = $statement->fetch(PDO::FETCH_ASSOC);

    // 3) Si no hay registro en BD → insertarlo
    if (!$row) {
        $insert = $connection->prepare(
            "INSERT INTO directory (id_user, name, path, parent_directory)
             VALUES (:id_user, :name, :path, :parent_directory)"
        );
        $insert->execute([
            ":id_user"          => $userId,
            ":name"             => $nameDirectory,
            ":path"             => $pathEscaped,
            ":parent_directory" => $appRootDirectory,
        ]);
    }

    return $pathDirectory;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST["theme"])) {
    $name     = $_POST["name"]     ?? '';
    $email    = $_POST["email"]    ?? '';
    $password = $_POST["password"] ?? '';

    if (
        empty($name) ||
        empty($email) ||
        empty($password) ||
        !str_contains($email, "@") ||
        strlen($password) <= 7
    ) {
        // 400 Bad Request lógicamente, pero solo mostramos el mensaje al user
        $error = "Complete All Fields";
    } else {
        $connection = require './sql/db.php';

        $statement = $connection->prepare("SELECT email FROM users WHERE email = :email LIMIT 1");
        $statement->bindParam(":email", $email);
        $statement->execute();

        // No tiene que haber coincidencias, ya que estamos registrando un nuevo usuario.
        if ($statement->rowCount() > 0) {
            $error = "This Email Is Taken";
        } else {
            // Crear usuario
            $connection
                ->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)")
                ->execute([
                    ":name"     => $name,
                    ":email"    => $email,
                    ":password" => password_hash($password, PASSWORD_BCRYPT)
                ]);

            // Recuperar usuario recién creado
            $statement = $connection->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
            $statement->bindParam(":email", $email);
            $statement->execute();
            $user = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                // Algo muy raro: insertó pero no lo encuentra
                $error = "Internal error: user not found after insert.";
            } else {
                // Directorio raíz de la app (normalmente /var/www/html)
                $directoryRoot = realpath(dirname(__FILE__));

                // Asegurar directorio y registro en tabla `directory`
                $userDirectory = ensure_user_directory($connection, (int)$user["id"], $directoryRoot);

                // Sesión del usuario
                session_start();
                unset($user["password"]);
                $_SESSION["user"] = $user;

                // Log en MongoDB: registro de usuario
                log_event('user_registered', [
                    'user_id'     => (int) $user['id'],
                    'name'        => $user['name'],
                    'email'       => $user['email'],
                    'directory'   => $userDirectory,
                    'description' => 'Nuevo usuario registrado y directorio inicial creado',
                ]);

                header("Location: ./home.php");
                exit();
            }
        }
    }
}
?>
<?php require __DIR__ . "/partials/header.php" ?>

<body>
    <?php require __DIR__ . "/partials/navbar.php" ?>
    <?php if ($error): ?>
        <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
            <symbol id="check-circle-fill" viewBox="0 0 16 16">
                <path
                    d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
            </symbol>
            <symbol id="info-fill" viewBox="0 0 16 16">
                <path
                    d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z" />
            </symbol>
            <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
                <path
                    d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" />
            </symbol>
        </svg>

        <div class="container mt-4">
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="26" height="26" role="img" aria-label="Danger:">
                    <use xlink:href="#exclamation-triangle-fill" />
                </svg>
                <div>
                    <?= $error ?>
                </div>
            </div>
        </div>
    <?php endif ?>
    <div class="container pt-3">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <form class="p-4"
                        action="<?= $_SERVER["PHP_SELF"] ?>"
                        method="post">
                        <div class="row mb-3">
                            <label for="inputName"
                                class="col-sm-2 col-form-label">Name</label>
                            <div class="col-sm-10">
                                <input type="text"
                                    class="form-control"
                                    id="inputName"
                                    name="name"
                                    required
                                    placeholder="Daniel Benjamin Perez Morales"
                                    autocomplete="name"
                                    autofocus>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="inputEmail"
                                class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email"
                                    class="form-control"
                                    id="inputEmail"
                                    name="email"
                                    required
                                    placeholder="Daniel@gmail.com"
                                    autocomplete="email">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="inputPassword"
                                class="col-sm-2 col-form-label">Password</label>
                            <div class="col-sm-10">
                                <input type="password"
                                    name="password"
                                    class="form-control"
                                    id="inputPassword"
                                    required
                                    placeholder="********"
                                    minlength="8"
                                    maxlength="64"
                                    autocomplete="new-password">
                            </div>
                        </div>
                        <fieldset class="row mb-3">
                            <legend class="col-form-label col-sm-2 pt-0">Select</legend>
                            <div class="col-sm-10">
                                <div class="form-check">
                                    <input class="form-check-input"
                                        type="radio"
                                        data-credentials="true"
                                        name="passwordVisibility"
                                        id="gridRadios1"
                                        value="HiddenPassword"
                                        checked>
                                    <label class="form-check-label"
                                        for="gridRadios1">
                                        Hidden Password
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input"
                                        type="radio"
                                        data-credentials="true"
                                        name="passwordVisibility"
                                        id="gridRadios2"
                                        value="ViewPassword">
                                    <label class="form-check-label"
                                        for="gridRadios2">
                                        View Password
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                        <button id="submitButton" type="submit"
                            class="btn btn-primary">Sign in</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
<?php require __DIR__ . "/partials/footer.php" ?>

</html>