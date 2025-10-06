<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="css/index.css">
    <title><?= $title ?></title>
</head>

<body>
    <nav>
        <div class="nav-container">
            <a href="/" class="logo">Camagru</a>
            <ul class="nav-menu">
                <li><a href="/joke/list">Gallery</a></li>
                <!-- <li><a href="/joke/list">Editor</a></li>
                <li><a href="/joke/list">Profile</a></li> -->

                <!-- <li><a href="/joke/edit">Add a new Joke</a></li> -->

                <?php if ($loggedIn): ?>
                    <li class="logout"><a href="/login/logout">Log out</a></li>
                <?php else: ?>
                    <li class="login"><a href="/login/login">Log in</a></li>
                <?php endif; ?>

            </ul>
        </div>
    </nav>
    <main class="content">
        <?= $output ?>
    </main>
    <footer>
        <div class="footer-bottom">
            <p>&copy; 2025 Camagru. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>