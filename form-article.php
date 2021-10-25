<?php
require __DIR__ . '/database/database.php';
require __DIR__ . '/database/security.php';

$currentUser = isLoggedIn();
if (!$currentUser) {
    header('Location: ./');
}

$articleDB = require __DIR__ . '/database/models/ArticleDB.php';


const ERROR_REQUIRED = "Veuiilez renseigner ce champ";
const ERROR_TITLE_TOO_SHORT = "Le titre est trop court";
const ERROR_CONTENT_TOO_SHORT = "L'article est trop court";
const IMAGE_URL = "L'image doit être une URL valide";

$category = '';

$errors = [
    'title' => '',
    'image' => '',
    'category' => '',
    'content' => ''
];


$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$id = $_GET['id'] ?? '';



if ($id) {
    $article = $articleDB->fetchOne($id);

    $title = $article['title'];
    $image = $article['image'];
    $category = $article['category'];
    $content = $article['content'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $_POST = filter_input_array(
        INPUT_POST,
        [
            'title' => FILTER_SANITIZE_STRING,
            'image' => FILTER_SANITIZE_URL,
            'category' => FILTER_SANITIZE_STRING,
            'content' => [
                'filter' => FILTER_SANITIZE_STRING | FILTER_SANITIZE_FULL_SPECIAL_CHARS,
                'flags' => FILTER_FLAG_NO_ENCODE_QUOTES
            ]
        ]
    );

    $title = $_POST['title'];
    $image = $_POST['image'];
    $category = $_POST['category'];
    $content = $_POST['content'];

    if (!$title) {
        $errors['title'] = ERROR_REQUIRED;
    } elseif (mb_strlen($title) < 5) {
        $errors['title'] = ERROR_TITLE_TOO_SHORT;
    }

    if (!$image) {
        $errors['image'] = ERROR_REQUIRED;
    } elseif (!filter_var($image, FILTER_VALIDATE_URL)) {
        $errors['image'] = IMAGE_URL;
    }

    if (!$category) {
        $errors['category'] = ERROR_REQUIRED;
    }

    if (!$content) {
        $errors['content'] = ERROR_REQUIRED;
    } elseif (mb_strlen($content) < 20) {
        $errors['content'] = ERROR_CONTENT_TOO_SHORT;
    }


    if (empty(array_filter($errors, function ($e) {
        return $e !== '';
    }))) {
        $article['title'] = $title;
        $article['image'] = $image;
        $article['category'] = $category;
        $article['content'] = $content;
        $article['author'] = $currentUser['id'];
        if ($id) {
            // Update
            $article['id'] = $id;
            $articleDB->updateOne($article);
        } else {
            // Create
            $articleDB->createOne($article);
        }
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

    <title><?= $id ? 'Modifier un article' : 'Créer mon article' ?></title>
</head>

<!-- title -->
<!-- image -->
<!-- category -->
<!-- content -->

<body>
    <div class="container">
        <?php
        require_once("./includes/header.php");
        ?>
        <div class="content">
            <div class="block p-20 form-container">
                <h1><?= $id ? 'Modifier un article' : 'Créer mon article' ?></h1>
                <form action="./form-article.php<?= $id ? "?id=$id" : '' ?>" method="POST">
                    <div class="form-control">
                        <label for="title">Titre</label>
                        <input type="text" name="title" id="title" value="<?= $title ?? '' ?>">
                        <?php if ($errors['title']) : ?>
                            <p class="text-error"><?= $errors['title'] ?></p>
                        <?php endif ?>
                    </div>
                    <div class="form-control">
                        <label for="image">Image</label>
                        <input type="text" name="image" id="image" value=<?= $image ?? '' ?>>
                        <?php if ($errors['image']) : ?>
                            <p class="text-error"><?= $errors['image'] ?></p>
                        <?php endif ?>
                    </div>
                    <div class="form-control">
                        <label for="category">Catégorie</label>
                        <select name="category" id="category">
                            <option <?= !$category || $category === 'technology' ? 'selected' : '' ?> value="technology">Technologie</option>
                            <option <?= $category === 'nature' ? 'selected' : '' ?> value="nature">Nature</option>
                            <option <?= $category === 'politic' ? 'selected' : '' ?> value="politic">Politique</option>

                        </select>
                        <?php if ($errors['category']) : ?>
                            <p class="text-error"><?= $errors['category'] ?></p>
                        <?php endif ?>
                    </div>
                    <div class="form-control">
                        <label for="content">Contenu</label>
                        <textarea name="content" id="content"><?= $content ?? '' ?></textarea>
                        <?php if ($errors['content']) : ?>
                            <p class="text-error"><?= $errors['content'] ?></p>
                        <?php endif ?>
                    </div>
                    <div class="form-action">
                        <a href="./" class="btn btn-secondary" type="button">Annuler</a>
                        <button class="btn btn-primary" type="submit"><?= $id ? 'Modifier' : 'Sauvegarder' ?></button>
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