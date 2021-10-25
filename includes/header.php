<?php
$currentUser = $currentUser ?? false;
?>

<header>
    <a href="./" class="logo">Dyma Blog</a>
    <ul class="header-menu">

        <?php if ($currentUser) : ?>
            <li class="<?= $_SERVER['REQUEST_URI'] === '/dyma/blog/form-article.php' ? 'active' : '' ?>">
                <a href="./form-article.php">Ecrire un article</a>
            </li>
            <li class="<?= $_SERVER['REQUEST_URI'] === '/dyma/blog/auth-logout.php' ? 'active' : '' ?>">
                <a href="./auth-logout.php">Déconnexion</a>

            </li>
            <li class="<?= $_SERVER['REQUEST_URI'] === '/dyma/blog/profile.php' ? 'active' : '' ?>">
                <a href="./profile.php">Ma page</a>
            </li>

        <?php else : ?>
            <li class="<?= $_SERVER['REQUEST_URI'] === '/dyma/blog/auth-register.php' ? 'active' : '' ?>">
                <a href="./auth-register.php">Inscription</a>
            </li>
            <li class="<?= $_SERVER['REQUEST_URI'] === '/dyma/blog/auth-login.php' ? 'active' : '' ?>">
                <a href="./auth-login.php">Connexion</a>
            </li>

        <?php endif ?>

    </ul>
</header>