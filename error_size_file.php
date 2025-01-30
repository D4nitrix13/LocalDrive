<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: ./register.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $_SESSION["flash"] = [
        "message" => "El Fichero Supera El Tamaño Máximo Permitido De 3GB.",
        "class" => "alert alert-danger d-flex align-items-center",
        "aria-label" => "Danger:",
        "xlink:href" => "#exclamation-triangle-fill"
        // Colour Red
    ];

    header("Location: ./home.php");
    exit();
}
?>