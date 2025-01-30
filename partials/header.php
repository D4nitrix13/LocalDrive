<?php
if (!isset($_COOKIE["theme"])) {
    $_COOKIE["theme"] = "flatly";
    setcookie(
        "theme",
        $_COOKIE["theme"],
        [
            "expires" => time() + (60 * 60 * 24 * 30),
            "path" => "/",
            "secure" => false,         // Asegúrate de usar HTTPS si está activado
            "httponly" => true,       // Evita el acceso de JavaScript
            "samesite" => "Lax"       // O "None" si es necesario para contextos de terceros
        ]
    );
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["theme"])) {
    $_COOKIE["theme"] = $_POST["theme"];
    setcookie(
        "theme",
        $_COOKIE["theme"],
        [
            "expires" => time() + (60 * 60 * 24 * 30),
            "path" => "/",
            "secure" => false,
            "httponly" => true,
            "samesite" => "Lax"
        ]
    );
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">
    <title>Local Drive</title>
    <link rel="icon"
        type="image/png"
        href="./static/img/LogoApp.png">
    <?php if ($_COOKIE["theme"] === "darkly"): ?>
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/darkly/bootstrap.min.css"
            integrity="sha512-HDszXqSUU0om4Yj5dZOUNmtwXGWDa5ppESlX98yzbBS+z+3HQ8a/7kcdI1dv+jKq+1V5b01eYurE7+yFjw6Rdg=="
            crossorigin="anonymous"
            referrerpolicy="no-referrer" />
        <!-- En PHP, la forma correcta de escribir este tipo de condiciones dentro de una estructura alternativa de sintaxis (usada con : en lugar de {}) es utilizar elseif (sin espacio entre else e if). -->
    <?php elseif ($_COOKIE["theme"] === "flatly"): ?>
        <link rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/bootswatch/5.3.3/flatly/bootstrap.min.css"
            integrity="sha512-qoT4KwnRpAQ9uczPsw7GunsNmhRnYwSlE2KRCUPRQHSkDuLulCtDXuC2P/P6oqr3M5hoGagUG9pgHDPkD2zCDA=="
            crossorigin="anonymous"
            referrerpolicy="no-referrer" />
    <?php endif ?>

    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <!-- Static Content -->
    <link rel="stylesheet" href="./static/css/style.css">

    <?php $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    $listEndPoint = ["/login.php", "/register.php", "/credentials.php"];
    if (in_array($uri, $listEndPoint)): ?>
        <script defer
            src="./static/js/index.js">
        </script>
    <?php endif ?>

    <?php if ($_SERVER["PHP_SELF"] === "/home.php"): ?>
        <script defer
            src="./static/js/sizeFile.js">
        </script>
    <?php endif ?>

</head>