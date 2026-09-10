<?php 

    session_start();

    require 'database.php';

    header('Content-Type: application/json');

    $method = $_SERVER['REQUEST_METHOD'];

    $uri = $_SERVER['REQUEST_URI'];

    // ==> Fetch Api <== \\ (Teste Api)

    if ($method === 'GET' && $uri === '/') {

        $result = $pdo->query("SELECT id, username FROM users");

        $users = $result->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode ([
            'message' => "API Funcionando",
            'users' => $users
        ]);
    } 

    // ==> GET /api/cards <== \\ (Gerar Lista)

    elseif ($method === 'GET' && $uri === '/api/cards') {

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

    // ==> POST /api/cards <== \\ (Cadastrar)

    elseif ($method === 'POST' && $uri === '/api/cards') {

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);

            echo json_encode ([
                "message" => 'Usuário não Logado!'
            ]);

            exit;
        }

        $input = file_get_contents('php://input');

        $data = json_decode($input, true);

        if (empty($data['name_en']) || empty($data['game']) || empty($data['edition'])){
            http_response_code(400);

            echo json_encode([
                'message' => "Preencha os campos obrigatórios"
            ]);

            exit;
        } 

        $file = "data/games/{$data['game']}.json";

        if (!file_exists($file)) {
            http_response_code(400);

            echo json_encode([
                'message' => "jogo inválido"
            ]);

            exit;
        }

        $editions = json_decode(file_get_contents($file), true);

        if (!in_array($data['edition'], $editions)) {
            http_response_code(400);

            echo json_encode([
                'message' => "O jogo não possui essa edição"
            ]);

            exit;
        }

        $sql = "INSERT INTO cards 
                (name_en, name_pt, game, edition, image, rarity)
                VALUES
                (:name_en, :name_pt, :game, :edition, :image, :rarity)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':name_en' => $data['name_en'],
            ':name_pt' => $data['name_pt'] ?? null,
            ':game' => $data['game'],
            ':edition' => $data['edition'],
            ':image' => $data['image'] ?? null,
            ':rarity' => $data['rarity'] ?? null
        ]);
        http_response_code(201);

        echo json_encode([
            'message' => 'Carta cadastrada com sucesso!',
            'id' => $pdo->lastInsertId()
        ]);
    } 

    // ==> PUT /api/cards/{id} <== \\ (Editar)

    elseif ($method === 'PUT' && preg_match('#^/api/cards/([0-9]+)$#', $uri, $matches)) {

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);

            echo json_encode([
                'message' => 'Usuário não Logado!'
            ]);

            exit;
        }

        $id = $matches[1];

        $stmt = $pdo->prepare('SELECT id FROM cards WHERE id = :id');

        $stmt->execute([
            ':id' => $id
        ]);

        if (!$stmt->fetch()) {
            http_response_code(404);

            echo json_encode ([
                'message' => "Carta não encontrada"
            ]);

            exit;
        }

        $input = file_get_contents('php://input');

        $data = json_decode($input, true);

        if (empty($data['name_en']) || empty($data['game']) || empty($data['edition'])) {
            http_response_code(400);

            echo json_encode([
                'message' => 'Preencha os campos obrigatórios'
            ]);

            exit;
        }

        $file = "data/games/{$data['game']}.json";

        if (!file_exists($file)) {

            http_response_code(400);

            echo json_encode([
                'message' => "Jogo inválido"
            ]);

            exit;
        }

        $editions = json_decode(file_get_contents($file), true);

        if (!in_array($data['edition'], $editions)) {

            http_response_code(400);

            echo json_encode([
                'message' => 'O jogo não possui essa edição'
            ]);

            exit;
        }

        $sql = "UPDATE cards SET
                name_en = :name_en,
                name_pt = :name_pt,
                game = :game,
                edition = :edition,
                image = :image,
                rarity = :rarity
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':name_en' => $data['name_en'],
            ':name_pt' => $data['name_pt'] ?? null,
            ':game' => $data['game'],
            ':edition' => $data['edition'],
            ':image' => $data['image'] ?? null,
            ':rarity' => $data['rarity'] ?? null,
            ':id' => $id
        ]);

        echo json_encode([
            'message' => "Carta atualizada com sucesso!"
        ]);
    } 


    // ==> DELETE /api/cards/{id} <== \\ (Excluir)

    elseif ($method === 'DELETE' && preg_match('#^/api/cards/([0-9]+)$#', $uri, $matches)) {

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);

            echo json_encode ([
                'message' => "Usuário não Logado"
            ]);

            exit;
        }

        $id = $matches[1];

        $stmt = $pdo->prepare('SELECT id FROM cards WHERE id = :id');

        $stmt->execute([
            ':id' => $id
        ]);

        if (!$stmt->fetch()) {
            http_response_code(404);

            echo json_encode ([
                'message' => "Carta não encontrada"
            ]);

            exit;
        }

        $sql = 'DELETE FROM cards WHERE id = :id';

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':id' => $id
        ]);

        echo json_encode ([
            'message' => "Carta Deletada!"
        ]);
    } 

    // ==> POST /api/login <== \\ (Login)

    elseif ($method === 'POST' && $uri === '/api/login') {

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
    } 

    // ==> IDENTIFICAR JSON <== \\

    elseif ($method === 'GET' && preg_match('#^/api/([a-z]+)/editions$#', $uri, $matches)) {

        $game = $matches[1];

        $file = "data/games/{$game}.json";

        if (!file_exists($file)) {
            http_response_code(404);

            echo json_encode([  
                'message' => 'Jogo não encontrado'
            ]);

            exit;
        }

        $editions = json_decode(file_get_contents($file), true);

        echo json_encode([
            'game' => $game,
            'editions' => $editions
        ]);
    }


    // ==> ROTA NÃO ENCONTRADA <== \\

    else {  
        http_response_code(404);

        echo json_encode ([
            'message' => "Pagina nao encontrada"
        ]);
    }