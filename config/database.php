<?php
    $host = 'localhost';
    $db = 'cardhub';
    $user = 'cardhub_user';
    $password = 'LMCH!2026!';

    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $password);