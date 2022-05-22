<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Page</title>
</head>

<body>
    <h1>Error</h1>
    <p>
        <?php
        if (isset($_GET['error'])) {
            $error = $_GET['error'];
            if ($error == 403) {
                echo "You are not allowed to access this page.";
            } else if ($error == 404) {
                echo "The page you are looking for does not exist.";
            } else {
                echo "Unknown error.";
            }
        } else {
            echo "Unknown error.";
        }
        ?>
    </p>
</body>

</html>