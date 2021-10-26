<?php

$env = require(__DIR__ . '/../.env.php');
$dns = 'mysql:host=localhost;dbname=blog';
$userConn = $env['DB_USER'];
$pwd = $env['DB_PWD'];
// $pwd = getenv('DB_PWD');

try {
    $pdo = new PDO($dns, $userConn, $pwd, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    // echo "Connexion db blog Ok" . "<br>";
} catch (PDOException $e) {
    throw new Exception($e->getMessage());
}

return $pdo;
