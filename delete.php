<?php
session_start();
// error_log(print_r($_GET, true));

if ( !isset($_GET["id"]) || (int)$_GET["id"] !== (int)$_SESSION["user"]["id"] ) {
    http_response_code(401);
    echo "[x] Unauthorized" . PHP_EOL;
    exit();
}

$connection = require "./sql/db.php";
$id = $_GET['id'];
// Delete Records From The Database Table And Delete The Associated Directory
$connection->query("SELECT function_delete_data_files($id) AS value");
$connection->query("SELECT function_delete_data_user($id) AS value");
session_unset();
session_destroy();
// system("rm -rf " . escapeshellarg("/App/LocalDrive/Directory" . basename($_GET['id'])));
header("Location: ./index.php");
exit();
?>