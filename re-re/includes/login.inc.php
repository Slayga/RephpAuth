<!-- Login form -->
<form action="./" method="post">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="hidden" name="current_form" value="login">
    <input type="submit" name="login" value="login">
</form>
<!-- Register here redirect -->
<form action="./" method="post" id="signup_redirect">
    <input type="hidden" name="signup_redirect" value="signup_redirect">
    <a href="javascript:;" onclick="parentNode.submit();">Register here!</a>
</form>