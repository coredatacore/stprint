<?php
// config.php - InfinityFree Database Configuration

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'st_print';


// $DB_HOST = 'sqlXXX.infinityfree.com';   // from IF MySQL page
// $DB_USER = 'if0_40456836';              // example only
// $DB_PASS = 'Stprint1604';               // your password
// $DB_NAME = 'if0_40456836_st_print';     // database name


// Strict SQL error mode
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function db_connect() {
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME;

    try {
        $db = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
        $db->set_charset('utf8mb4');
        return $db;

    } catch (Exception $e) {
        // Hide sensitive DB details, show safe message
        die("Database connection error. Please try again later.");
    }
}
?>
