<?php

class User{
    private $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    // ==> UserLogin
    public function findByUsername($username){
        $sql = "SELECT * FROM users WHERE username = :username";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':username' => $username
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ==> UserRegister
    public function register($username, $password){
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':username' => $username,
            ':password' => $hash
        ]);

        return $this->pdo->lastInsertId();
    }
}