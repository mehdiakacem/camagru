<?php
if (isset($errorMessage)):
    echo '<div class="errors">Sorry, your username and password could not be found.</div>';
endif;
?>
<link rel="stylesheet" href="/css/auth.css">
<h2>Login</h2>
<form method="post" action="">
    <label for="username">Username</label>
    <input type="text" id="username" name="username">

    <label for="password">Password</label>
    <input type="password" id="password" name="password">

    <input type="submit" name="login" value="Log in">
</form>

<p>Don't have an account? <a class="register" href="/auth/register">Click here to register</a></p>