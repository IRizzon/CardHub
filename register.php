<?php
    require 'database.php';

    $username = 'usuario';
    $password = '123!@#';

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':username' => $username,
        ':password' => $passwordHash
    ]);

    echo "Usuário criado com sucesso!";
