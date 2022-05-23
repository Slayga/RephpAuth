<!-- Everything is fictional, this doesn't represent any brand or company. 
The main purpose is to provide knowledge on how to: 
 - handle
 - process
 - develop 

 It will provide object oriented programming in PHP. Javascript for rendering gallery, next page, previous page, etc.
 Database will store information about the images. And the images will be stored in the folder "images/gallery/$username".
-->

<!-- User info that is stored in the database: 
1. uid
2. username
3. password
4. admin (0 or 1)
5. alias
6. suspended (0 or 1)

Note on the 23rd of May, 2022:
(this is now out of date, new list is: id, username, password, is_banned, is_admin, permissions) 
 -->

<!-- Image information that is stored in the database:
    1. uid
    2. image_name
    3. image_description
    4. image_date
    5. image_path ("images/gallery/$username)
    6. tags (tags that are associated with the image)
    7. rating (upvote and downvote)
    8. saves (number of times the image is saved)
    9. views (number of times the image is viewed)
    10. comments (number of comments)

Note on the 23rd of May, 2022:
(this is now out of date, new list is: name, description, is_public, posted_by, file_name) 
  -->

<!-- Server functionalities that are available:
    1. Login
    2. Logout
    3. Register
    4. Change password (account required)
    5. Change alias
    6. Change admin status (only admin can change admin status on users)
    7. Delete user (admin can delete any user, but only admin can delete himself, also user can delete but only suspends their account)
    9. View user (admin can view any user)
    10. Gallery post (account required; Title, description, image, tags)
    11. Gallery view (account required)
    12. Gallery delete (account required, admin can delete any post, users can delete their own posts)
    13. Gallery edit (account required, users can edit their own posts and change: title, description, tags)
    14. Gallery comment (account required, users can comment on any post, admin supervises comments)
    15. Gallery comment delete (account required, admin can delete any comment, users can delete their own comments)
    16. Gallery comment edit (account required, users can edit their own comments)
    17. Gallery comment reply (account required, users can reply to any comment, admin supervises replies)
    18. Gallery comment upvote (account required, users can upvote any comment)
    19. Gallery comment downvote (account required, users can downvote any comment)
    20. Gallery upvote (account required, users can upvote any post)
    21. Gallery downvote (account required, users can downvote any post)
    22. Gallery save post (account required, users can save their own posts)
    23. Gallery unsave post (account required, users can unsave their own posts)
    24. Gallery saved posts (account required, users can view their own saved posts)

Note: Database settings are set in the file "php/includes/config.php" in the array called "db_config". db_name, db_host, db_password, etc.

Note on the 23rd of May, 2022:
This is heavily outdated, the idea was there but was simply to complex to be implemented in the time window.
php/includes/config.php idea is still used, not in includes  but parent folder, and is a php file $config array with key values 
that define a config for specific stuff, for example $config["db"] returns an array of database values
-->


<?php 
// Setup basic auth
require_once __DIR__.'/php/includes/config.php';
require_once __DIR__.'/php/classes/dbConnection.cls.php';
require_once __DIR__.'/php/classes/auth.cls.php';

$db_conn = new dbConnection($db_config);
$auth = new Auth($db_conn);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["logout"])) {
        // Destroy html to be able to setcookie
        $auth->logout();
        header("Location: ./");
    }
    else if (isset($_POST["login"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];
        if ($auth->login($username, $password)) {
            echo "Login successful!" . "<br>";
        } else {
            die("Login failed <a href=''>Try again</a>");
        }
        
    } else if (isset($_POST["register"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $alias = $_POST["alias"];
        $auth->signup($username, $password, $alias);

    } 
}

echo "Start generating html" . "<br>";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>phpAuth and general testing</title>
</head>

<body>
    <!-- echo if session is running -->
    <?php if ($auth->isLoggedIn()){ 
        echo "Session is: " . $auth->getSession() ."<br>";
        echo "Logged in <br>";
        print_r($_SESSION);
        echo "<br>";
        echo "SessionHash: " . $auth->getSessionHash() . "<br>";
        echo "CookieHash: " . $auth->getCookieHash() . "<br>";
        echo "<br>". "HashSession:". hash('sha256', $auth->getSession()) . "<br>";
    } else {
        echo "Session is: " . $auth->getSession(). "<br>";
        echo "Not logged in <br>";
        // Print sessionHash and cookieHash
        echo "SessionHash: " . $auth->getSessionHash() . "<br>";
        echo "CookieHash: " . $auth->getCookieHash() . "<br>";
        echo "<br>". "HashSession:". hash('sha256', $auth->getSession()) . "<br>";
        }
    ?>
    <!-- Button to reset session -->
    <form action="" method="post">
        <input type="submit" name="logout" value="Logout">
    </form>

    <h1>phpAuth and general testing</h1>
    <h2>Login</h2>
    <form action="index.php" method="POST">
        <input type="text" name="username" placeholder="Username">
        <input type="password" name="password" placeholder="Password">
        <input type="submit" value="Login" name="login">
    </form>
    <h2>Register</h2>
    <form action="index.php" method="POST">
        <input type="text" name="username" placeholder="Username">
        <input type="password" name="password" placeholder="Password">
        <input type="text" name="alias" placeholder="Alias (surname, forename)">
        <input type="submit" value="Register" name="register">
    </form>




</body>

</html>