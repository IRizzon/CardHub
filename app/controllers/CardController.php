<?php

require_once __DIR__ . '/../models/Card.php';

class CardController{

    private $card;

    public function __construct($pdo){
        $this->card = new Card($pdo);
    }

    // ==> CardList
    public function index(){
        return $this->card->getAll();
    }

    // ==> CardInsert
    public function create($data){

        // ==> Verificar duplicidade
        $exists = $this->card->existsByNameAndEdition(
            $data['name_en'],
            $data['edition']
        );

        if ($exists) {
            return [
                'error' => true,
                'message' => 'Esta carta já existe nesta edição.'
            ];

        }

        return $this->card->create($data);
    }

    // ==> CardEdit
    public function update($id, $data){

        // ==> Verificar duplicidade
        $exists = $this->card->existsByNameAndEdition(
            $data['name_en'],
            $data['edition'],
            $id
        );

        if ($exists) {
            return [
                'error' => true,
                'message' => 'Esta carta já existe nesta edição.'
            ];
        }

        return $this->card->update($id, $data);
    }

    // ==> CardDelete
    public function delete($id){
        return $this->card->delete($id);
    }

}