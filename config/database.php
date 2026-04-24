<?php
declare(strict_types=1);

$host = '10.35.233.136';
$port = 3306;
$db = 'k275333_S-Art';
$user = 'k275333_Maxim';
$pass = getenv('DB_PASS') ?: 'CHANGE_ME';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo 'DB connected successfully';
} catch (PDOException $e) {
    error_log($e->getMessage());
    die('Database connection failed.');
}
