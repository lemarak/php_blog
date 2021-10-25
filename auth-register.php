<?php

const ERROR_REQUIRED = "Veuiilez renseigner ce champ";
$errors = [
    'firstname' => '',
    'lastname' => '',
    'email' => '',
    'password' => '',
    'confirmPassword' => '',
];



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty(array_filter($errors, fn ($e) => $e !== ''))) {
        header('Location: /');
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    require_once("./includes/head.php");
    ?>
    <link rel="stylesheet" href="./public/css/auth-register.css">
    <link rel="stylesheet" href="./public/css/style.css">
    <title>Inscription</title>
</head>

<body>
    <div class="container">
        <?php
        require_once("./includes/header.php");
        ?>
        <div class="content">
            <div class="block p-20 form-container">
                <h1>Inscription</h1>
                <form action="./auth-register.php" method="POST">
                    <div class="form-control">
                        <label for="firstname">Prénom</label>
                        <input type="text" name="firstname" id="firstname" value="<?= $firstname ?? '' ?>">
                        <?php if ($errors['firstname']) : ?>
                            <p class="text-error"><?= $errors['firstname'] ?></p>
                        <?php endif ?>
                    </div>
                    <div class="form-control">
                        <label for="lastname">Nom</label>
                        <input type="text" name="lastname" id="lastname" value="<?= $lastname ?? '' ?>">
                        <?php if ($errors['lastname']) : ?>
                            <p class="text-error"><?= $errors['lastname'] ?></p>
                        <?php endif ?>
                    </div>
                    <div class="form-control">
                        <label for="email">Email</label>
                        <input type="text" name="email" id="email" value="<?= $email ?? '' ?>">
                        <?php if ($errors['email']) : ?>
                            <p class="text-error"><?= $errors['email'] ?></p>
                        <?php endif ?>
                    </div>
                    <div class="form-control">
                        <label for="password">Mot de passe</label>
                        <input type="text" name="password" id="password">
                        <?php if ($errors['password']) : ?>
                            <p class="text-error"><?= $errors['password'] ?></p>
                        <?php endif ?>
                    </div>
                    <div class="form-control">
                        <label for="confirmPassword">Confirmation Mot de passe</label>
                        <input type="text" name="confirmPassword" id="confirmPassword">
                        <?php if ($errors['confirmPassword']) : ?>
                            <p class="text-error"><?= $errors['confirmPassword'] ?></p>
                        <?php endif ?>
                    </div>


                    <div class="form-action">
                        <a href="./" class="btn btn-secondary" type="button">Annuler</a>
                        <button class="btn btn-primary" type="submit">Valider</button>
                    </div>
                </form>

            </div>
        </div>
        <?php
        require_once("./includes/footer.php");
        ?>
    </div>
</body>

</html>