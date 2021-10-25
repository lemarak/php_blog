<?php
$pdo = require_once('./database/database.php');

const ERROR_REQUIRED = "Veuillez renseigner ce champ";
const ERROR_TOO_SHORT = "Ce champ est trop court";
const ERROR_PASSWORD_TOO_SHORT = "Le mot de passe est trop court (6 caractères)";
const ERROR_EMAIL_INVALID = "l'email n'est pas valide";
const ERROR_PASSWORD_NOT_CONFIRMED = "Les mots de passe sont différents";

$errors = [
    'firstname' => '',
    'lastname' => '',
    'email' => '',
    'password' => '',
    'confirmPassword' => '',
];



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = filter_input_array(INPUT_POST, [
        'firstname' => FILTER_SANITIZE_SPECIAL_CHARS,
        'lastname' => FILTER_SANITIZE_SPECIAL_CHARS,
        'email' => FILTER_SANITIZE_EMAIL,
    ]);
    $firstname = $input['firstname'] ?? '';
    $lastname = $input['lastname'] ?? '';
    $email = $input['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Check input
    if (!$firstname) {
        $errors['firstname'] = ERROR_REQUIRED;
    } elseif (mb_strlen($firstname) < 3) {
        $errors['firstname'] = ERROR_TOO_SHORT;
    }
    if (!$lastname) {
        $errors['lastname'] = ERROR_REQUIRED;
    } elseif (mb_strlen($firstname) < 3) {
        $errors['lastname'] = ERROR_TOO_SHORT;
    }
    if (!$email) {
        $errors['email'] = ERROR_REQUIRED;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = ERROR_EMAIL_INVALID;
    }
    if (!$password) {
        $errors['password'] = ERROR_REQUIRED;
    } elseif (mb_strlen($password) < 6) {
        $errors['password'] = ERROR_TOO_SHORT;
    }
    if (!$confirmPassword) {
        $errors['confirmPassword'] = ERROR_REQUIRED;
    } elseif ($password !== $confirmPassword) {
        $errors['confirmPassword'] = ERROR_PASSWORD_NOT_CONFIRMED;
    }

    if (empty(array_filter($errors, function ($e) {
        return $e !== '';
    }))) {
        $statement = $pdo->prepare('INSERT INTO user VALUES (
            DEFAULT,
            :firstname,
            :lastname,
            :email,
            :password
            )');
        $hashPassword = password_hash($password, PASSWORD_ARGON2I);
        $statement->bindValue(':firstname', $firstname);
        $statement->bindValue(':lastname', $lastname);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':password', $password);
        $statement->execute();

        header('Location: ./');
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
                        <input type="password" name="password" id="password">
                        <?php if ($errors['password']) : ?>
                            <p class="text-error"><?= $errors['password'] ?></p>
                        <?php endif ?>
                    </div>
                    <div class="form-control">
                        <label for="confirmPassword">Confirmation Mot de passe</label>
                        <input type="password" name="confirmPassword" id="confirmPassword">
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