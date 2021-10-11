<?php

$env = require_once('./.env.php');
$dns = 'mysql:host=localhost;dbname=blog';
$user = $env['DB_USER'];
$pwd = $env['DB_PWD'];
// $pwd = getenv('DB_PWD');

try {
    $pdo = new PDO($dns, $user, $pwd, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    // echo "Connexion db blog Ok" . "<br>";
} catch (PDOException $e) {
    echo "Error : " . $e->getMessage();
}

return $pdo;
