<!-- Logout button/form -->
<form action="./" method="post">
    <input type="hidden" name="unique_rand" value="<?php echo $_SESSION['unique_rand']; ?>">
    <input type="submit" name="logout" value="logout">
</form>