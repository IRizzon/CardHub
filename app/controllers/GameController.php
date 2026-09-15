<?php

class GameController{

    // ==> Get Editions

    public function getEditions($game){

        $file = __DIR__ . '/../../data/games/' . $game . '.json';

        if (!file_exists($file)){
            return false;
        }

        $json = file_get_contents($file);
        $data = json_decode($json, true);

        return $data['editions'];
    }

    // ==> Get Rarities

    public function getRarities($game){

        $file = __DIR__ . '/../../data/games/' . $game . '.json';

        if (!file_exists($file)){
            return false;
        }

        $json = file_get_contents($file);
        $data = json_decode($json, true);

        return $data['rarities'];
    }
}