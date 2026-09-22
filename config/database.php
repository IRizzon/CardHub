<?php
    $host = 'localhost';
    $db = 'cardhub';
    $user = 'cardhub_user';
    $password = 'DATABASE_PASSWORD';

    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $password);