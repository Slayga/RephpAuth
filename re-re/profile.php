<?php
require_once __DIR__ . './includes/sync.inc.php';
// Send away unhauthorized users
if (!$auth->is_logged) {
    header("Location: ./");
    exit();
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
    <header class="navbar">
        <?php
        include_once __DIR__ . "./includes/templates/header.php"; 
        
        ?>
    </header>
</body>

</html>