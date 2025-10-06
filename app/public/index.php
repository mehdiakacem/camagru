<?php
$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASS');

try {
    $pdo = new PDO("mysql:dbname=$dbname;host=$host", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $query = $pdo->query('SHOW VARIABLES LIKE "version"');
    $row = $query->fetch();

    echo '✅ Connected successfully<br>';
    echo 'MySQL version: ' . htmlspecialchars($row['Value']);
} catch (PDOException $e) {
    echo '❌ Connection failed: ' . htmlspecialchars($e->getMessage());
}
