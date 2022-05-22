<!-- Signup form with login check box, with text -->
<form action="./" method="post">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="checkbox" name="auto_login" value="1"> Auto login
    <input type="hidden" name="current_form" value="signup">
    <input type="submit" name="signup" value="signup">
</form>
<form action="./" method="post" id="login_redirect">
    <input type="hidden" name="login_redirect" value="login_redirect">
    <a href="javascript:;" onclick="parentNode.submit();">Already registered?</a>
</form>