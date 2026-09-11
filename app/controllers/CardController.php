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
        return $this->card->create($data);
    }

    // ==> CardEdit
    public function update($id, $data){
        return $this->card->update($id, $data);
    }

    // ==> CardDelete
    public function delete($id){
        return $this->card->delete($id);
    }

}