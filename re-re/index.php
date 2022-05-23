<?php 
// Sync autoload the authenticate class. And starts the session
require_once __DIR__ . './includes/sync.inc.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // Unique rand is used to prevent resubmission through refresh. Only affect login and signup and logout requests
    if (isset($_POST["unique_rand"]) && isset($_SESSION["unique_rand"]) && $_POST["unique_rand"] === $_SESSION["unique_rand"]) {
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
        } 
    }
    
    // Redirect to signup form
    if (isset($_POST["signup_redirect"])) {
        $_SESSION['current_form'] = "signup";
    
    // Redirect to login form
    } elseif (isset($_POST["login_redirect"])) {
        $_SESSION['current_form'] = "login";
    
    //  Redirect from posting and getting error return
    } else {
    $_SESSION['current_form'] = $_POST["current_form"] ?? "login";
    }
    
    if (isset($return) && $return["error"]) {
        // Return has "error" and "message" keys to give feedback to user
        // If ever wanted to do something  before the html is generated and an error is returned do it here...
    } 
}

// Generates unique identifier for forms
$_SESSION['unique_rand'] = strval(rand());
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Include main stylesheet -->
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/layout_grid.css">
    <link rel="stylesheet" href="styles/index.css">

    <title>Document</title>
</head>

<body>
    <header class="header navbar">
        <?php include_once __DIR__ . "./includes/templates/header.php"; ?>
    </header>

    <main class="main">

        <?php if(!$auth->is_logged) { 
            $current_form = $_SESSION['current_form'] ?? "login"; ?>
        <div class="form-wrap">
            <h2>Login</h2>
            <?php if ($current_form == "login") {include __DIR__ . "/includes/forms/login.inc.php";
                } elseif ($current_form == "signup") {include __DIR__ . "/includes/forms/signup.inc.php";}?>
            <p class="alert">
                <?php if (isset($return) && $return["error"]) {echo $return["message"];} ?>
            </p>
        </div>
        <?php } ?>

    </main>

    <footer class="footer"></footer>
</body>

</html>