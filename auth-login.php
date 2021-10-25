<?php
$pdo = require_once('./database/database.php');

const ERROR_REQUIRED = "Veuillez renseigner ce champ";
const ERROR_PASSWORD_TOO_SHORT = "Le mot de passe est trop court (6 caractères)";
const ERROR_EMAIL_INVALID = "L'email n'est pas valide";
const ERROR_BAD_PASSSWORD = "Le mot de passe n'est pas valide";
const ERROR_EMAIL_UNKOWN = "L'email n'est pas connu";

$errors = [
    'email' => '',
    'password' => '',
];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = filter_input_array(INPUT_POST, [
        'email' => FILTER_SANITIZE_EMAIL,
    ]);
    $email = $input['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Check input
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


    if (empty(array_filter($errors, function ($e) {
        return $e !== '';
    }))) {
        $statementUser = $pdo->prepare('
            SELECT * FROM user WHERE email=:email
        ');
        $statementUser->bindValue(':email', $email);
        $statementUser->execute();
        $user = $statementUser->fetch();

        if (!$user) {
            $errors['email'] = ERROR_EMAIL_UNKOWN;
        } else {
            if (!password_verify($password, $user['password'])) {
                $errors['password'] = ERROR_BAD_PASSSWORD;
            } else {
                $statementSession = $pdo->prepare('
                    INSERT INTO session VALUES (
                        DEFAULT,
                        :idUser
                        )
                ');
                $statementSession->bindValue(':idUser', $user['id']);
                $statementSession->execute();
                $sessionId = $pdo->lastInsertId();
                setCookie('session', $sessionId, time() + 3600 * 24 * 2, '', '', false, true);

                header('Location: ./');
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    require_once("./includes/head.php");
    ?>
    <link rel="stylesheet" href="./public/css/auth-login.css">
    <title>Connexion</title>
</head>

<body>
    <div class="container">
        <?php
        require_once("./includes/header.php");
        ?>
        <div class="content">
            <div class="block p-20 form-container">
                <h1>Connexion</h1>
                <form action="./auth-login.php" method="POST">
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

                    <div class="form-action">
                        <a href="./" class="btn btn-secondary" type="button">Annuler</a>
                        <button class="btn btn-primary" type="submit">Connexion</button>
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