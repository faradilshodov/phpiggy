<?php

$driver = 'mysql';
$config = http_build_query(data: [
    'host' => '127.0.0.1',
    'port' => '3306',
    'dbname' => 'phpiggy',
], arg_separator: ';');

$dsn = "{$driver}:{$config}";
$username = 'root';
$password = '';

try {
    $db = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    die("Connection failed");
}

echo "Connected to the database successfully!";
