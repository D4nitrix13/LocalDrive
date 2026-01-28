<?php
session_start();

require_once __DIR__ . '/sql/mongo_logger.php';

// Verificación de sesión
if (!isset($_SESSION["user"])) {
    // Registrar intento no autorizado de acceder al mensaje de error
    log_event('file_upload_size_error_unauthenticated', [
        'reason' => 'User attempted to access size error page without active session.',
    ]);

    header("Location: ./register.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {

    // Registrar evento de error por tamaño de archivo excedido
    log_event('file_upload_size_exceeded', [
        'user_id' => $_SESSION["user"]["id"] ?? null,
        'email'   => $_SESSION["user"]["email"] ?? null,
        'limit'   => '3GB',
        'message' => 'File exceeded maximum allowed upload size.'
    ]);

    // Flash message usado por home.php
    $_SESSION["flash"] = [
        "message"     => "El fichero supera el tamaño máximo permitido de 3GB.",
        "class"       => "alert alert-danger d-flex align-items-center",
        "aria-label"  => "Danger:",
        "xlink:href"  => "#exclamation-triangle-fill"
    ];

    header("Location: ./home.php");
    exit();
}
