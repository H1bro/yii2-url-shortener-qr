<?php

$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbPort = getenv('DB_PORT') ?: '3306';
$dbName = getenv('DB_NAME') ?: 'yii2_shortener';
$dbUser = getenv('DB_USER') ?: 'root';
$dbPassword = getenv('DB_PASSWORD') ?: '';

return [
    'class' => 'yii\db\Connection',
    'dsn' => sprintf('mysql:host=%s;port=%s;dbname=%s', $dbHost, $dbPort, $dbName),
    'username' => $dbUser,
    'password' => $dbPassword,
    'charset' => 'utf8mb4',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
