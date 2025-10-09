<form method="post" action="">
    <label for="name">Username</label>
    <input type="text" id="name" name="name">

    <label for="password">Password</label>
    <input type="password" id="password" name="password">

    <input type="submit" name="login" value="Log in">
    <?php
    if (isset($errorMessage)):
    ?>
        <div class="errors"><?= $errorMessage ?></div>
    <?php
    endif;
    ?>
</form>

<p><a class="register" href="/auth/signup">Sign up</a></p>