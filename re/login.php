<!-- Login form -->
<form action="./" method="post">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="submit" name="login" value="login">
</form>
<form action="./" method="post" id="reg">
    <input type="hidden" name="reg" value="reg">
    <a href="javascript:;" onclick="parentNode.submit();">Register here!</a>
</form>