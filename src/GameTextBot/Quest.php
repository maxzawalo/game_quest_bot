<?php

namespace GameTextBot;

class Quest
{
    protected $START_STATE_ID; //Первое состояние - Start проскакиваем
    protected $name; //Имя игры
    protected $next_states = array();
    protected $states = array();
    protected $current_state_id = 2;
    protected $uid = 0;
    protected $db;

    function __construct($uid)
    {
        try {
            $this->uid = $uid;
            $this->Init();
        } catch (\Exception $e) {
            Log::get('Quest')->error($e->getMessage());
        }
    }

    public function Start()
    {
        $this->LoadCurrentState(); 
    }

    public function LoadCurrentState()
    {
        try {
            $this->current_state_id = $this->get_data('current_state_id', $this->START_STATE_ID);
        } catch (\Exception $e) {
            Log::get('Quest')->error($e->getMessage());
        }
    }

    public function CheckCurrentState($next_id)
    {
        $random = false;
        if (strpos($next_id, '_random') !== false) {
            $next_id = str_replace('_random', '', $next_id);
            $random = true;
        }
        $next_id =  str_replace('moveto_', '', $next_id);

        if ($random) {
            //Не даем нажать на старую кнопку
            if ($this->current_state_id != $next_id)
                return false;
            //Сразу прыгаем на следующий
            $ind = random_int(0, count($this->next_states[$this->current_state_id]) - 1);
            $this->current_state_id = $this->next_states[$this->current_state_id][$ind]->id;
        } else {
            //Не даем нажать на старую кнопку
            $in_array = false;
            foreach ($this->next_states[$this->current_state_id] as $ns) {
                if ($ns->id == $next_id) {
                    $in_array = true;
                    break;
                }
            }
            if (!$in_array)
                return false;

            $this->current_state_id = $next_id;
        }

        $this->put_data('current_state_id', $this->current_state_id);
        return true;
    }

    public function GetText()
    {
        return $this->states[$this->current_state_id]->name;
    }

    public function GetImg()
    {
        return CDN_IMG_URL . $this->current_state_id . ".jpg";
    }

    protected  function Init()
    {
        $this->db = new DB($this->uid);
    }

    public function get_inline_keyboard()
    {
        $keyboard = array();
        if ($this->states[$this->current_state_id]->random) {
            $keyboard[] =  [
                "text" => $this->GetNextBtnText(),
                "callback_data" => "moveto_" . $this->current_state_id . '_random'
            ];
        } else {
            if (count($this->next_states[$this->current_state_id]) == 1) {
                //Если путь 1, то просто кнопка Далее
                $id = $this->next_states[$this->current_state_id][0]->id;
                $keyboard[] =  [
                    "text" => $this->GetNextBtnText(),
                    "callback_data" => "moveto_" . $id
                ];
            } else {
                foreach ($this->next_states[$this->current_state_id] as $ns) {
                    $keyboard[] =  [
                        "text" => $ns->name,
                        "callback_data" => "moveto_" . $ns->id
                    ];
                }
            }
        }
        return [$keyboard];
    }

    protected function  GetNextBtnText()
    {
        return "Далее >>";
    }

    public function Reset()
    {
        $this->current_state_id = $this->START_STATE_ID;
        $this->put_data('current_state_id', $this->current_state_id);
    }

    public function put_data($key, $value)
    {
        $this->db->put_data($this->name . "_" . $key, $value);
    }

    public function get_data($key, $def_value)
    {
        return  $this->db->get_data($this->name . "_" . $key, $def_value);
    }
}
