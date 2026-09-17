<?php

class Card{
    private $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    // ==> CardList
    public function getAll(){
        $result = $this->pdo->query("SELECT * FROM cards");

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    // ==> CardInsert
    public function create($data){
        $sql = "INSERT INTO cards (name_en, name_pt, game, edition, image, rarity) 
        VALUES 
        (:name_en, :name_pt, :game, :edition, :image, :rarity)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':name_en' => $data['name_en'],
            ':name_pt' => $data ['name_pt'] ?? null,
            ':game' => $data['game'],
            ':edition' => $data['edition'],
            ':image' => $data['image'] ?? null,
            ':rarity' => $data['rarity'] ?? null
        ]);

        return $this->pdo->lastInsertId();
    }

    // ==> Verificar duplicidade
    public function existsByNameAndEdition($name_en, $edition, $id = null){

        $sql = "SELECT id
                FROM cards
                WHERE name_en = :name_en
                AND edition = :edition";

        if ($id !== null) {
            $sql .= " AND id != :id";
        }

        $stmt = $this->pdo->prepare($sql);

        $params = [
            ':name_en' => $name_en,
            ':edition' => $edition
        ];

        if ($id !== null) {
            $params[':id'] = $id;
        }

        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    // ==> CardEdit
    public function update($id, $data){
        $stmt = $this->pdo->prepare('SELECT id, image FROM cards WHERE id = :id');
        $stmt->execute([
            ':id' => $id
        ]);
        
        $card = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$card) {
            return false;
        }

        $image = $data['image'] ?? $card['image'];

        $sql = "UPDATE cards SET 
            name_en = :name_en,
            name_pt = :name_pt,
            game = :game,
            edition = :edition,
            image = :image,
            rarity = :rarity
            WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':name_en' => $data['name_en'],
            ':name_pt' => $data ['name_pt'] ?? null,
            ':game' => $data['game'],
            ':edition' => $data['edition'],
            ':image' => $image,
            ':rarity' => $data['rarity'] ?? null,
            ':id' => $id
        ]);

        return true;
    }

    // ==> CardDelete
    public function delete($id){
        $stmt = $this->pdo->prepare('SELECT id FROM cards WHERE id = :id');
        $stmt->execute([
            ':id' => $id
        ]);

        if (!$stmt->fetch()){
            return false;
        }
        $sql = "DELETE FROM cards WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => $id
        ]);
        return true;
    }
}