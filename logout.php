<?php
// Reanudamos la sesión existente para acceder a sus datos
session_start();
session_destroy();
header("Location: ./index.php");
exit();
?>
