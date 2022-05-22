<?php
// Sync the auth class through all files using $_SESSION["auth"]
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . "/../classes/auth.cls.php";

$auth = $_SESSION["auth"] ?? new ReAuthentication($config);