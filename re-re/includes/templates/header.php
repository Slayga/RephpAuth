<h1>Re-Re Gallery</h1>
<nav>
    <a href="#">Home</a>
    <a href="#">About</a>
    <a href="#">Contact</a>
</nav>
<div class="header__form-wrap">
    <?php if ($auth->is_logged) {include_once __DIR__ . "/../profile.inc.php";} ?>
    <?php if($auth->is_logged) {include_once __DIR__ . "/../forms/logout.inc.php";} ?>
</div>

<style>
.header__form-wrap {
    display: flex;
    justify-content: center;
    align-items: center;
}
</style>