<?php
// https://www.phptutorial.net/php-pdo/pdo-connecting-to-postgresql/
// En PHP, require_once es una construcción del lenguaje utilizada para incluir y evaluar un fichero solo una vez en el ciclo de ejecución del script.
// Qué hace require_once:

// Incluye un fichero: Inserta el contenido del fichero especificado en el lugar donde se invoca la instrucción.
// Evita duplicación: Si el fichero ya ha sido incluido previamente, no lo vuelve a incluir, lo que ayuda a prevenir errores de redefinición de funciones, clases o constantes.

require_once "./sql/config.php";

function connect(string $dsn, string $host, string $port, string $db, string $user, string $password): PDO
{
    try {
        // * Make A Database Connection
        $pdo = new PDO(
            "pgsql:host={$host};port={$port};dbname={$db};",
            $user,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );

        if ($pdo) {
            // echo("[*] Connected To The $db Database Successfully!" . PHP_EOL);
            return $pdo;
        }
    } catch (PDOException $e) {
        echo ("[x] Error Database $e!" . PHP_EOL);
        die($e->getMessage());
    } finally {
        if ($pdo) {
            // * Force Close Connection Database
            // $pdo = null;
            // echo("[*] Close Connection To The $db Database!" . PHP_EOL);
        }
    }
}

return connect($dsn, $host, $port, $db, $user, $password);
?>
