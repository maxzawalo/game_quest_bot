<?php

namespace GameTextBot;

class DB
{
    protected $db;
    protected $game_name;

    public function __construct($uid)
    {
        $this->db = \SleekDB\SleekDB::store('key_value', 'db/' . $uid . '/');
    }

    public function put_data($key, $value)
    {
        $arr = $this->db->where('key', '=', $key)->fetch();
        if ($arr == null)
            $this->db->insert(['key' => $key, 'value' => $value]);
        else {
            $arr['value'] = $value;
            $this->db->where('key', '=', $key)->update($arr);
        }
    }
    public function get_data($key, $def_value)
    {
        $arr = $this->db->where('key', '=', $key)->fetch();
        if ($arr != null && count($arr) != 0)
            return $arr[0]['value'];

        return $def_value;
    }
}
