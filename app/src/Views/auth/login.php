<?php
if (isset($errorMessage)):
    echo '<div class="errors">Sorry, your username and password could not be found.</div>';
endif;
?>
<form method="post" action="">
    <label for="username">Username</label>
    <input type="text" id="username" name="username">

    <label for="password">Password</label>
    <input type="password" id="password" name="password">

    <input type="submit" name="login" value="Log in">
</form>

<p><a class="register" href="/auth/signup">Sign up</a></p>