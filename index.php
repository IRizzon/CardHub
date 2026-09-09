<?php 
    session_start();

    require 'database.php';

    header('Content-Type: application/json');

    $method = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'];

    if ($method === 'GET' && $uri === '/') {
        $result = $pdo->query("SELECT id, username FROM users");
        $users = $result->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode ([
            'message' => "API Funcionando",
            'users' => $users
        ]);
    }

    elseif  ($method === 'GET' && $uri === '/api/cards') {

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);

            echo json_encode ([
                'message' => "Usuário não Logado"
            ]);

            exit;
        }
        $result = $pdo->query("SELECT * FROM cards");
        $cards = $result->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'cards' => $cards
        ]);
    } 

    elseif ($method === 'POST' && $uri === '/api/cards') {

        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (empty($data['nome']) || empty($data['jogo'])){
            http_response_code(400);

            echo json_encode([
                'message' => "preencha todos os campos"
            ]);
        } else {
            http_response_code(201);

            echo json_encode([
                'nome' => $data['nome'],
                'jogo' => $data['jogo']
            ]);
        }
    } elseif ($method === 'POST' && $uri === '/api/login') {

        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (empty($data['username']) || empty($data['password'])){
            http_response_code(400);

            echo json_encode([
                'message' => "Preencha todos os campos"
            ]);

            exit;
        }

        $sql = "SELECT * FROM users WHERE username = :username";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':username' => $data['username']
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($data['password'], $user['password'])) {
            http_response_code(401);

            echo json_encode([
                'message' => "Usuário ou senha inválidos"
            ]);

            exit;
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        echo json_encode([
            'message' => "Logado com Sucesso!",
            'user' => [
                'id' => $user['id'],
                'username' => $user['username']
            ]
        ]);
    } else {
        http_response_code(404);

        echo json_encode ([
            'message' => "Pagina nao encontrada"
        ]);
    }


