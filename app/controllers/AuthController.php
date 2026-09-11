<?php

require_once __DIR__ . '/../models/User.php';

class AuthController{
    private $user;

    public function __construct($pdo){
        $this->user = new User($pdo);
    }

    // ==> UserLogin
    public function login($username, $password){
        $user = $this->user->findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])){
            return false;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        return true;
    }

    // ==> UserRegister

    public function register($username, $password){

        if ($this->user->findByUsername($username)){
            return false;
        }

        return $this->user->register($username, $password);
    }
}