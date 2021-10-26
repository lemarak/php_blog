<?php
require __DIR__ . '/database/database.php';
$authDB = require_once('./database/security.php');

$currentUser = $authDB->isLoggedIn();

$articleDB = require('./database/models/ArticleDB.php');

$articles = $articleDB->fetchAll();

$categories = [];

$_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$selectedCat = $_GET['cat'] ?? '';


if (count($articles)) {
    $cat_temp = array_map(function ($a) {
        return $a['category'];
    }, $articles);
    $categories = array_reduce(
        $cat_temp,
        function ($acc, $cat) {
            if (isset($acc[$cat])) {
                $acc[$cat]++;
            } else {
                $acc[$cat] = 1;
            }
            return $acc;
        },
        []
    );
    $articlePerCategories = array_reduce(
        $articles,
        function ($acc, $article) {
            if (isset($acc[$article['category']])) {
                $acc[$article['category']] = [...$acc[$article['category']], $article];
            } else {
                $acc[$article['category']] = [$article];
            }
            return $acc;
        },
        []
    );

    // echo "<pre>";
    // print_r($articlePerCategories);
    // echo "</pre>";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    require_once("./includes/head.php");
    ?>
    <link rel="stylesheet" href="./public/css/index.css">
    <title>Mon blog</title>
</head>

<body>
    <div class="container">
        <?php
        require_once("./includes/header.php");
        ?>
        <div class="content">
            <div class="newsfeed-container">
                <ul class="category-container">
                    <li class="<?= $selectedCat ? '' : 'cat-active' ?>"><a href="./"> Tous les articles <span class="small">(<?= count($articles) ?>)</span></a></li>
                    <?php foreach ($categories as $category => $nbInCat) : ?>

                        <li class="<?= $selectedCat === $category ? 'cat-active' : '' ?>"><a href="./?cat=<?= $category ?>"><?= $category ?> <span class="small">(<?= $nbInCat ?>)</span>
                            </a></li>
                    <?php endforeach ?>
                </ul>
                <div class="newsfeed-content">
                    <?php if (!$selectedCat) : ?>
                        <?php foreach ($categories as $category => $nbInCat) : ?>
                            <h2><?= $category  ?></h2>
                            <div class="articles-container">
                                <?php foreach ($articlePerCategories[$category] as $article) : ?>
                                    <a href="./show-article.php?id=<?= $article['id'] ?>" class="article block">
                                        <div class="overflow">
                                            <div class="img-container" style="background-image:url(<?= $article['image'] ?>)"></div>
                                        </div>
                                        <h3><?= $article['title'] ?></h3>
                                        <?php if ($article['author']) : ?>
                                            <div class="article-author">
                                                <p><?= $article['firstname'] ?> <?= $article['lastname'] ?></p>
                                            </div>
                                        <?php endif ?>
                                    </a>
                                <?php endforeach ?>
                            </div>
                        <?php endforeach ?>
                    <?php else : ?>
                        <h2><?= $selectedCat ?></h2>
                        <div class="articles-container">
                            <?php foreach ($articlePerCategories[$selectedCat] as $article) : ?>
                                <a href="./show-article.php?id=<?= $article['id'] ?>" class="article block">
                                    <div class="overflow">
                                        <div class="img-container" style="background-image:url(<?= $article['image'] ?>)"></div>
                                    </div>
                                    <h3><?= $article['title'] ?></h3>
                                    <?php if ($article['author']) : ?>
                                        <div class="article-author">
                                            <p><?= $article['firstname'] ?> <?= $article['lastname'] ?></p>
                                        </div>
                                    <?php endif ?>
                                </a>
                            <?php endforeach ?>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
        <?php
        require_once("./includes/footer.php");
        ?>
    </div>
</body>

</html>