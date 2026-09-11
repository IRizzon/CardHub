<?php

// ==> JSON Edition
class GameController{

    public function getEditions($game){
        $file = __DIR__ . '/../../data/games/' . $game . '.json';

        if (!file_exists($file)){
            return false;
        }

        $json = file_get_contents($file);

        return json_decode($json, true);
    }
}