<?php 
require_once 'config.php';
require_once "auth.cls.php";

$auth = new ReAuthentication($config);
// Testing when guest is null / redirect to error
// $auth->is_guest = null;

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    if (isset($_POST["login"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $return = $auth->login($username, $password);

    } else if (isset($_POST["signup"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $auto_login = $_POST["auto_login"];
        $return = $auth->signup($username, $password, $auto_login);

    } else if (isset($_POST["logout"])) {
        $return = $auth->logout();
    } else if (isset($_POST["reg"])) {
        $_SESSION['current_form'] = "signup";
    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <header>
        <h1>Gallery</h1>
        <nav>
            <a href="#">Home</a>
            <a href="#">About</a>
            <a href="#">Contact</a>
            <a href="#">Gallery</a>
        </nav>
        <a href="#">Login</a>
    </header>
    <main>
        <?php 
            $current = $_SESSION['current_form'] ?? "login";
            if ($current == "login") {
                include_once "login.php";
            } else if ($current == "signup") {
                include_once "signup.php";
            }
        ?>
    </main>
</body>

</html>