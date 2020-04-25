<?php

namespace GameTextBot;

class GameFactory
{
    const CURRENT_GAME = "current_game";

    public function createGame($uid)
    {
        $db = new DB($uid);
        $game_name = $db->get_data(GameFactory::CURRENT_GAME, "Quest1");

        switch ($game_name) {
            case 'Quest1':
                return new Quest1($uid);
            case 'RandomQuest':
                return new RandomQuest($uid);
        }
    }

    public function changeGame($uid, $game_name)
    {
        $db = new DB($uid);
        $db->put_data(GameFactory::CURRENT_GAME, $game_name);
        return $this->createGame($uid);
    }
}
