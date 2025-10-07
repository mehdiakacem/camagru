<h2>Register</h2>
<form action="" method="post">
    <label for="email">Email</label>
    <input name="user[email]" id="email" type="text" value="<?= $user['email'] ?? '' ?>">

    <label for="name">Username</label>
    <input name="user[name]" id="name" type="text" value="<?= $user['name'] ?? '' ?>">

    <label for="password">Password</label>
    <input name="user[password]" id="password" type="password" value="<?= $user['password'] ?? '' ?>">

    <input type="submit" name="submit" value="Register account">
    <?php
    if (!empty($errors)) :
    ?>
        <div class="errors">
            <ul>
                <?php
                foreach ($errors as $error) :
                ?>
                    <li><?= $error ?></li>
                <?php
                endforeach; ?>
            </ul>
        </div>
    <?php
    endif;
    ?>
</form>