<?php 
    header('Content-Type: application/json');

    $method = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'];

    if ($method === 'GET' && $uri === '/') {
       echo json_encode ([
        'message' => "API Funcionando"
       ]);
    }

    elseif  ($method === 'GET' && $uri === '/api/cards') {
        echo json_encode ([
            'message' => "Lista de Cartas"
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
    } else {
        http_response_code(404);

        echo json_encode ([
            'message' => "Pagina nao encontrada"
        ]);
    }


