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
        $return = $auth->signup($username, $password);
    } else if (isset($_POST["logout"])) {
        $return = $auth->logout();
    } if (isset($return["error"]) && $return["error"]) {
        print_r($return);
    }

}

if ($auth->is_logged) {
    echo "Welcome: " . $auth->username;
} else if ($auth->is_guest){
    echo "Welcome: Guest";
} else {
    header("Location: error.php?error=403");
    exit();
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
    <?php if ($auth->is_guest) { ?>
    <h1>Login</h1>
    <!-- Login -->
    <form action="index.php" method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" name="login" value="login">
    </form>
    <?php } ?>

    <?php if ($auth->is_guest) { ?>
    <h1>Signup</h1>
    <!-- Signup -->
    <form action="index.php" method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" name="signup" value="signup">
    </form>
    <?php } ?>

    <?php if($auth->is_logged){ ?>
    <h1>Logout</h1>
    <!-- Logout -->
    <form action="index.php" method="post">
        <input type="submit" name="logout" value="logout">
    </form>
    <?php } ?>
</body>

</html>