<?php
session_start();

require_once __DIR__ . '/sql/mongo_logger.php';

// Verificar si hay usuario logueado
if (!isset($_SESSION["user"])) {

    // Log: intento de logout sin sesión
    log_event('logout_no_session', [
        'message' => 'Logout attempted without an active session.'
    ]);

    header("Location: ./index.php");
    exit();
}

// Guardamos datos antes de destruir la sesión
$userData = [
    'user_id' => $_SESSION["user"]["id"] ?? null,
    'email'   => $_SESSION["user"]["email"] ?? null
];

// Log: usuario cerró sesión
log_event('user_logged_out', [
    'user_id' => $userData['user_id'],
    'email'   => $userData['email'],
    'message' => 'User logged out successfully.'
]);

// Destruir sesión
session_unset();
session_destroy();

// Redirigir al login
header("Location: ./index.php");
exit();
