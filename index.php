<?php 

session_start();

require 'config/database.php';
require 'app/controllers/AuthController.php';
require 'app/controllers/CardController.php';
require 'app/controllers/GameController.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST' && isset($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'])) {
    $method = strtoupper($_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE']);
}

$uri = $_SERVER['REQUEST_URI'];

// ==> GET /api/cards <== \\ (Card/CardController)

if ($method === 'GET' && $uri === '/api/cards') {

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);

        echo json_encode ([
            'message' => "Usuário não Logado"
        ]);

        exit;
    }

    $cardController = new CardController($pdo);
    $cards = $cardController->index();

    echo json_encode([
        'cards' => $cards
    ]);
} 

// ==> POST /api/cards <== \\ (Card/CardController)

elseif ($method === 'POST' && $uri === '/api/cards') {

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);

        echo json_encode ([
            "message" => 'Usuário não Logado!'
        ]);

        exit;
    }

    $data = $_POST;

    if (empty($data['name_en']) || empty($data['game']) || empty($data['edition'])){
        http_response_code(400);

        echo json_encode([
            'message' => "Preencha os campos obrigatórios"
        ]);

        exit;
    } 

    $gameFile = __DIR__ . "/data/games/{$data['game']}.json";

    if (!file_exists($gameFile)) {
        http_response_code(400);

        echo json_encode([
            'message' => "jogo inválido"
        ]);

        exit;
    }

    // ==> Validar Edição
    $gameData = json_decode(file_get_contents($gameFile), true);
    $editionExists = false;

    foreach ($gameData['editions'] as $edition) {

        if ($edition['id'] === $data['edition']) {
            $editionExists = true;

            break;
        }
    }

    if (!$editionExists) {
        http_response_code(400);

        echo json_encode([
            'message' => "O jogo não possui essa edição"
        ]);

        exit;
    }

    // ==> Validar Raridade
    $rarityExists = false;

    foreach ($gameData['rarities'] as $rarity) {

        if ($rarity['id'] === $data['rarity']) {

            $rarityExists = true;
            break;
        }
    }

    if (!$rarityExists) {
        http_response_code(400);

        echo json_encode([
            'message' => 'O jogo não possui essa raridade'
        ]);

        exit;
    }

    // ==> Salvar Imagem
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/public/images/cards/';

        $fileName = basename($_FILES['image']['name']);
        $uploadPath = $uploadDir . $fileName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            http_response_code(500);
            echo json_encode([
                'message' => 'Erro ao salvar a imagem'
            ]);
            exit;
        }

        $data['image'] = '/public/images/cards/' . $fileName;
    }

    
    $cardController = new CardController($pdo);
    $result = $cardController->create($data);

    // ==> Verificar duplicidade
    if (is_array($result) && isset($result['error'])) {
        http_response_code(409);

        echo json_encode([
            'message' => $result['message']
        ]);

        exit;
    }

    http_response_code(201);

    echo json_encode([
        'message' => 'Carta cadastrada com sucesso!',
        'id' => $result
    ]);
} 

// ==> PUT /api/cards/{id} <== \\ (Card/CardController)

elseif ($method === 'PUT' && preg_match('#^/api/cards/(\d+)$#', $uri, $matches)) {

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);

        echo json_encode([
            'message' => 'Usuário não Logado!'
        ]);

        exit;
    }

    $id = $matches[1];
    $data = $_POST;

    if (empty($data)) {
        http_response_code(400);

        echo json_encode([
            'message' => 'Dados Inválidos'
        ]);
        exit;
    }

    if (empty($data['name_en']) || empty($data['game']) || empty($data['edition'])) {
        http_response_code(400);

        echo json_encode([
            'message' => 'Preencha os campos obrigatórios'
        ]);

        exit;
    }

    $gameFile = __DIR__ . "/data/games/{$data['game']}.json";

    if (!file_exists($gameFile)) {
        http_response_code(400);

        echo json_encode([
            'message' => 'Jogo inválido'
        ]);

        exit;
    }

    // ==> Validar Edição
    $gameData = json_decode(file_get_contents($gameFile), true);
    $editionExists = false;

    foreach ($gameData['editions'] as $edition) {
        if ($edition['id'] === $data['edition']) {
            $editionExists = true;

            break;
        }
    }

    if (!$editionExists) {
        http_response_code(400);

        echo json_encode([
            'message' => 'O jogo não possui essa edição'
        ]);

        exit;
    }

    // ==> Validar Raridade
    $rarityExists = false;

    foreach ($gameData['rarities'] as $rarity) {

        if ($rarity['id'] === $data['rarity']) {
            $rarityExists = true;

            break;
        }
    }

    if (!$rarityExists) {
        http_response_code(400);

        echo json_encode([
            'message' => 'O jogo não possui essa raridade'
        ]);

        exit;
    }

    // ==> Salvar Imagem

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

        $uploadDir = __DIR__ . '/public/images/cards/';

        $fileName = basename($_FILES['image']['name']);

        $uploadPath = $uploadDir . $fileName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            http_response_code(500);
            echo json_encode([
                'message' => 'Erro ao salvar a imagem'
            ]);
            exit;
        }

        $data['image'] = '/public/images/cards/' . $fileName;
    }
    
    $cardController = new CardController($pdo);
    $updated = $cardController->update($id, $data);

    // ==> Verificar duplicidade
    if (is_array($updated) && isset($updated['error'])) {
        http_response_code(409);

        echo json_encode([
            'message' => $updated['message']
        ]);

        exit;
    }

    // ==> Verificar se a carta existe
    if (!$updated){
        http_response_code(404);

        echo json_encode([
            'message' => 'Carta não encontrada'
        ]);

        exit;
    }

    echo json_encode([
        'message' => "Carta atualizada com sucesso!"
    ]);
} 


// ==> DELETE /api/cards/{id} <== \\ (Card/CardController)

elseif ($method === 'DELETE' && preg_match('#^/api/cards/([0-9]+)$#', $uri, $matches)) {

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);

        echo json_encode ([
            'message' => "Usuário não Logado"
        ]);

        exit;
    }

    $id = $matches[1];

    $cardController = new CardController($pdo);
    $deleted = $cardController->delete($id);

    if (!$deleted) {
        http_response_code(404);

        echo json_encode([
        'message' => 'Carta não encontrada'
            ]);

        exit;

    }

    echo json_encode ([
        'message' => "Carta Deletada!"
    ]);
} 

// ==> POST /api/login <== \\ (User/AuthController)

elseif ($method === 'POST' && $uri === '/api/login') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['username']) || empty($data['password'])){
        http_response_code(400);

        echo json_encode([
            'message' => "Preencha todos os campos"
        ]);

        exit;
    }

    $authController = new AuthController($pdo);
    $login = $authController->login(
        $data['username'],
        $data['password']
    );

    if (!$login){
        http_response_code(401);

        echo json_encode([
            'message' => 'Usuário ou senha inválidos'
        ]);

        exit;
    }

    echo json_encode([
        'message' => "Logado com Sucesso!",
    ]);
}

// ==> POST /api/logout <== \\ (User/AuthController)
elseif ($method === 'POST' && $uri === '/api/logout') {
    session_unset();
    session_destroy();

    echo json_encode([
        'message' => 'Logout realizado com sucesso!'
    ]);

    exit;
}

// ==> POST /api/register <== (User/AuthController)

elseif ($method === 'POST' && $uri === '/api/register') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['username']) || empty($data['password'])) {
        http_response_code(400);

        echo json_encode([
            'message' => 'Preencha todos os campos'
        ]);

        exit;
    }

    $authController = new AuthController($pdo);

    $register = $authController->register(
        $data['username'],
        $data['password']
    );

    if (!$register) {
        http_response_code(409);

        echo json_encode([
            'message' => 'Usuário já existe'
        ]);

        exit;
    }

    http_response_code(201);

    echo json_encode([
        'message' => 'Usuário cadastrado com sucesso!',
        'id' => $register
    ]);
}

// ==> GET /api/{game}/editions <== \\ (GameController)

elseif ($method === 'GET' && preg_match('#^/api/([a-z]+)/editions$#', $uri, $matches)) {

    $game = $matches[1];

    $gameController = new GameController();
    $editions = $gameController->getEditions($game);

    if ($editions === false) {
        http_response_code(404);

        echo json_encode([  
            'message' => 'Jogo não encontrado'
        ]);

        exit;
    }

    echo json_encode([
        'game' => $game,
        'editions' => $editions
    ]);
}

// ==> GET /api/{game}/rarities <== (GameController)

elseif ($method === 'GET' && preg_match('#^/api/([a-z]+)/rarities$#', $uri, $matches)) {

    $game = $matches[1];

    $gameController = new GameController();
    $rarities = $gameController->getRarities($game);

    if ($rarities === false) {
        http_response_code(404);

        echo json_encode([
            'message' => 'Jogo não encontrado'
        ]);

        exit;
    }

    echo json_encode([
        'game' => $game,
        'rarities' => $rarities
    ]);
}

// ==> ROTA NÃO ENCONTRADA <== \\

else {  
    http_response_code(404);

    echo json_encode ([
        'message' => "Pagina nao encontrada"
    ]);
}