<?php 
require_once __DIR__ . './config.php';
require_once __DIR__ . "./classes/auth.cls.php";

$auth = new ReAuthentication($config);

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // Login form submitted
    if (isset($_POST["login"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $return = $auth->login($username, $password);

    // Signup form submitted
    } else if (isset($_POST["signup"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $auto_login = $_POST["auto_login"] ?? false;
        $return = $auth->signup($username, $password, $auto_login);

    // Logout form submitted
    } else if (isset($_POST["logout"])) {
        $return = $auth->logout();

    // Redirect to signup form
    } 
    
    if (isset($_POST["signup_redirect"])) {
        $_SESSION['current_form'] = "signup";
    
    // Redirect to login form
    } elseif (isset($_POST["login_redirect"])) {
        $_SESSION['current_form'] = "login";
    } else {
    $_SESSION['current_form'] = $_POST["current_form"] ?? "login";
    }
    
    if (isset($return) && $return["error"]) {
        // Return has "error" and "message" keys to give feedback to user
    } 
    

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Include main stylesheet -->
    <link rel="stylesheet" href="styles/main.css">

    <title>Document</title>
</head>

<body>
    <header>
        <h1>Gallery</h1>
    </header>

    <main>
        <div class="form-wrap">
            <?php
            if (!$auth->is_logged) {
                $current_form = $_SESSION['current_form'] ?? "login";
                if ($current_form == "login") {
                    include_once __DIR__ . "/includes/login.inc.php";
                } else if ($current_form == "signup") {
                    include_once __DIR__ . "/includes/signup.inc.php";
                } if (isset($return) && $return["error"]) {
                    echo "<p class='alert'>{$return['message']}</p>";
                }
            } else {
                echo "<p>You are logged in as {$auth->username}</p>";
                include_once __DIR__ . "/includes/logout.inc.php";
            }
        ?>
        </div>
    </main>

</body>

</html>