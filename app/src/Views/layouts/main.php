<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="/css/index.css">
    <link rel="stylesheet" href="/css/auth.css">
    <title><?= $title ?></title>
</head>

<body>
    <nav>
        <div class="nav-container">
            <a href="/" class="logo">Camagru</a>
            <ul class="nav-menu">
                <li><a href="/gallery">Gallery</a></li>
                <?php if ($loggedIn): ?>
                    <li><a href="/profile/edit">Profile</a></li>
                    <li class="logout"><a href="/auth/logout">Log out</a></li>
                <?php else: ?>
                    <li class="login"><a href="/auth/login">Log in</a></li>
                <?php endif; ?>

            </ul>
        </div>
    </nav>
    <main class="content">
        <?= $output ?>
    </main>
    <footer>
        <div class="footer-bottom">
            <p>&copy; 2025 Camagru</p>
        </div>
    </footer>
</body>

</html>