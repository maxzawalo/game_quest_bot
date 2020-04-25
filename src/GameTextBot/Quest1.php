<?php

namespace GameTextBot;

class Quest1 extends Quest
{
    function __construct($uid)
    {
        $this->name = "Quest1";
        $this->START_STATE_ID = 'OgcykvqoKtf-7uEIAw74-2';
        parent::__construct($uid);
    }

    protected  function Init()
    {
        parent::Init();

        $this->next_states['OgcykvqoKtf-7uEIAw74-5'] = [(object) ['id' => 'OgcykvqoKtf-7uEIAw74-11', 'name' => '']];
        $this->next_states['OgcykvqoKtf-7uEIAw74-2'] = [(object) ['id' => 'OgcykvqoKtf-7uEIAw74-5', 'name' => 'Локация 2'], (object) ['id' => 'OgcykvqoKtf-7uEIAw74-7', 'name' => 'Локация 3']];
        $this->next_states['OgcykvqoKtf-7uEIAw74-11'] = [(object) ['id' => 'OgcykvqoKtf-7uEIAw74-7', 'name' => 'Локация 3'], (object) ['id' => 'OgcykvqoKtf-7uEIAw74-2', 'name' => 'Локация 1']];
        $this->next_states['OgcykvqoKtf-7uEIAw74-7'] = [(object) ['id' => 'OgcykvqoKtf-7uEIAw74-5', 'name' => '']];
        //-----------------------------
        $this->states['OgcykvqoKtf-7uEIAw74-5'] = (object) ['name' => 'Локация 2', 'random' => false];
        $this->states['OgcykvqoKtf-7uEIAw74-2'] = (object) ['name' => 'Локация 1', 'random' => false];
        $this->states['OgcykvqoKtf-7uEIAw74-11'] = (object) ['name' => 'Локация 4', 'random' => false];
        $this->states['OgcykvqoKtf-7uEIAw74-7'] = (object) ['name' => 'Локация 3', 'random' => false];

        Log::get('Quest1')->debug("Init");
    }
}
