<?php

namespace GameTextBot;

class RandomQuest extends Quest
{
    function __construct($uid)
    {
        $this->name = "RandomQuest";
        $this->START_STATE_ID = 'hgMCD8Qc4Z5fNGhsjrsD-2';
        parent::__construct($uid);
    }

    protected  function Init()
    {
        parent::Init();

        $this->next_states['hgMCD8Qc4Z5fNGhsjrsD-9'] = [(object) ['id' => 'hgMCD8Qc4Z5fNGhsjrsD-2', 'name' => '']];
        $this->next_states['hgMCD8Qc4Z5fNGhsjrsD-2'] = [(object) ['id' => 'hgMCD8Qc4Z5fNGhsjrsD-4', 'name' => '1'], (object) ['id' => 'hgMCD8Qc4Z5fNGhsjrsD-7', 'name' => '2'], (object) ['id' => 'hgMCD8Qc4Z5fNGhsjrsD-9', 'name' => '3']];
        $this->next_states['hgMCD8Qc4Z5fNGhsjrsD-7'] = [(object) ['id' => 'hgMCD8Qc4Z5fNGhsjrsD-2', 'name' => '']];
        $this->next_states['hgMCD8Qc4Z5fNGhsjrsD-4'] = [(object) ['id' => 'hgMCD8Qc4Z5fNGhsjrsD-2', 'name' => '']];
        //-----------------------------
        $this->states['hgMCD8Qc4Z5fNGhsjrsD-9'] = (object) ['name' => '3', 'random' => false];
        $this->states['hgMCD8Qc4Z5fNGhsjrsD-2'] = (object) ['name' => 'Выбор', 'random' => true];
        $this->states['hgMCD8Qc4Z5fNGhsjrsD-7'] = (object) ['name' => '2', 'random' => false];
        $this->states['hgMCD8Qc4Z5fNGhsjrsD-4'] = (object) ['name' => '1', 'random' => false];

        Log::get('RandomQuest')->debug("Init");
    }
}
