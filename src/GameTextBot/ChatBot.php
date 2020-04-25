<?php

namespace GameTextBot;

use CURLFile;

class ChatBot
{
    function apiRequestWebhook($method, $parameters)
    {
        if (!is_string($method)) {
            error_log("Method name must be a string\n");
            return false;
        }

        if (!$parameters) {
            $parameters = array();
        } else if (!is_array($parameters)) {
            error_log("Parameters must be an array\n");
            return false;
        }

        $parameters["method"] = $method;

        header("Content-Type: application/json");
        echo json_encode($parameters);
        return true;
    }

    function exec_curl_request($handle)
    {
        $response = curl_exec($handle);

        if ($response === false) {
            $errno = curl_errno($handle);
            $error = curl_error($handle);
            error_log("Curl returned error $errno: $error\n");
            curl_close($handle);
            return false;
        }

        $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
        curl_close($handle);

        if ($http_code >= 500) {
            // do not wat to DDOS server if something goes wrong
            sleep(10);
            return false;
        } else if ($http_code != 200) {
            $response = json_decode($response, true);
            error_log("Request has failed with error {$response['error_code']}: {$response['description']}\n");
            if ($http_code == 401) {
                throw new Exception('Invalid access token provided');
            }
            return false;
        } else {
            $response = json_decode($response, true);
            if (isset($response['description'])) {
                error_log("Request was successful: {$response['description']}\n");
            }
            $response = $response['result'];
        }

        return $response;
    }

    function apiRequest($method, $parameters)
    {
        if (!is_string($method)) {
            error_log("Method name must be a string\n");
            return false;
        }

        if (!$parameters) {
            $parameters = array();
        } else if (!is_array($parameters)) {
            error_log("Parameters must be an array\n");
            return false;
        }

        foreach ($parameters as $key => &$val) {
            // encoding to JSON array parameters, for example reply_markup
            if (!is_numeric($val) && !is_string($val)) {
                $val = json_encode($val);
            }
        }
        $url = API_URL . $method . '?' . http_build_query($parameters);

        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($handle, CURLOPT_TIMEOUT, 60);

        return exec_curl_request($handle);
    }

    function apiRequestJson($method, $parameters)
    {
        if (!is_string($method)) {
            error_log("Method name must be a string\n");
            return false;
        }

        if (!$parameters) {
            $parameters = array();
        } else if (!is_array($parameters)) {
            error_log("Parameters must be an array\n");
            return false;
        }

        $parameters["method"] = $method;

        $handle = curl_init(API_URL);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($handle, CURLOPT_TIMEOUT, 60);
        curl_setopt($handle, CURLOPT_POST, true);
        curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($parameters));
        curl_setopt($handle, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json"
        ));

        return $this->exec_curl_request($handle);
    }

    function sendDocument($chat_id, $file)
    {
        Log::get('ChatBot.sendDocument')->debug($file);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data']);
        curl_setopt($ch, CURLOPT_URL, API_URL . 'sendDocument');

        if (strpos($file, "/") === 0) {
            //file id
            $doc = $file;
        } else if (strpos($file, "http") === 0)
            $doc = $file;
        else {
            $doc = new \CURLFile(realpath($file));
        }

        $post_fields = ['chat_id' => $chat_id, 'document' => $doc];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

        Log::get('ChatBot')->debug("post_fields=" . print_r($post_fields, true));
        $output = curl_exec($ch);
        if ($output === false)
            Log::get('ChatBot')->error(curl_error($ch));
        // convert response
        $output = json_decode($output);
        // handle error; error output
        // if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) 
        {
            Log::get('ChatBot')->debug("output=" . print_r($output, true));
        }
        curl_close($ch);
    }

    function sendPhoto($chat_id, $path, $keyboard = null)
    {
        Log::get('ChatBot')->debug($path);
        if ($keyboard == null) {
            $keyboard = array(
                "remove_keyboard" => true
            );
        }

        $ch = curl_init();
        if (strpos($path, "/") === 0) {
            //file id
            $photo = $path;
        } else if (strpos($path, "http") === 0)
            $photo = $path;
        else {
            $photo = new \CURLFile(realpath($path));
        }

        $url = API_URL . "sendPhoto?chat_id=" . $chat_id;
        $post_fields = ['chat_id' => $chat_id, 'photo' => $photo];
        Log::get('ChatBot')->debug("post_fields=" . print_r($post_fields, true));

        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type:multipart/form-data"]);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        $output = curl_exec($ch);
        if ($output === false)
            Log::get('ChatBot')->error(curl_error($ch));
        // convert response
        $output = json_decode($output);
        // handle error; error output
        // if (curl_getinfo($ch, CURLINFO_HTTP_CODE) !== 200) 
        {
            Log::get('ChatBot')->debug("output=" . print_r($output, true));
        }
        curl_close($ch);
    }

    function sendLocation($chat_id, $quest)
    {
        $this->sendPhoto($chat_id, $quest->GetImg());
        // $this->sendDocument($chat_id, $quest->GetImg());
        $this->apiRequestJson("sendMessage", [
            'chat_id' => $chat_id,
            "text" => $quest->GetText(),
            // 'parse_mode' => 'HTML',
            // 'disable_web_page_preview' => false,
            'reply_markup' => array(
                "inline_keyboard" => $quest->get_inline_keyboard()
            ),
            'one_time_keyboard' => true,
            'resize_keyboard' => true
        ]);
    }

    public function ProccessCmd($text, $chat_id)
    {
        $menu = "МЕНЮ\n\n"
            . "Для начала/продолжения текущей игры:\n"
            . "/Quest1 игра 'Quest1' \n"
            . "/RandomQuest игра 'RandomQuest' \n"
            . "/reset сброс текущей игры в начало.";

        $gameFactory = new GameFactory();
        if (strpos($text, "/menu") === 0) {
            $this->apiRequestJson("sendMessage", [
                'chat_id' => $chat_id,
                "text" => $menu
            ]);
        } else if (strpos($text, "/start") === 0) {
            $this->apiRequestJson("sendMessage", [
                'chat_id' => $chat_id,
                "text" => $menu
            ]);
        } else if (strpos($text, "/reset") === 0) {
            $quest = $gameFactory->createGame($chat_id);
            $quest->Reset();
            $this->apiRequestJson("sendMessage", [
                'chat_id' => $chat_id,
                "text" => "Игра сброшена.\n"
                    . "Нажмите /start и можно начинать сначала.",
            ]);
        } else if (strpos($text, "/Quest1") === 0) {
            $quest = $gameFactory->changeGame($chat_id, "Quest1");
            $quest->Start();
            $this->sendLocation($chat_id, $quest);
        } else if (strpos($text, "/RandomQuest") === 0) {
            $quest = $gameFactory->changeGame($chat_id, "RandomQuest");
            $quest->Start();
            $this->sendLocation($chat_id, $quest);
        }
    }

    public function Process()
    {
        $request = file_get_contents("php://input");
        $input = json_decode($request, true);
        $message = $input['message'];
        $chat_id = $message['chat']['id'];
        $text = $message['text'];

        $callback_query = $input['callback_query'];
        Log::get('ChatBot')->debug(print_r($input, true));


        if (isset($callback_query)) {
            $chat_id = $callback_query['message']['chat']['id'];
            $gameFactory = new GameFactory();
            $quest = $gameFactory->createGame($chat_id);

            $callback_query_id = $callback_query['id'];
            $data = $callback_query['data'];

            $quest->LoadCurrentState();
            if (!$quest->CheckCurrentState($data))
                exit(0);

            $this->sendLocation($chat_id, $quest);
            // $this->apiRequestJson("answerCallbackQuery", array(
            //     'callback_query_id' => $callback_query_id,
            //     // 'text' => $text
            //     //'url' => ''
            // ));
        } else if (isset($text)) {

            // incoming text message
            $text = $message['text'];
            if (strpos($text, "/") === 0) {
                $this->ProccessCmd($text, $chat_id);
            } else {
                $this->apiRequestWebhook("sendMessage", array(
                    'chat_id' => $chat_id,
                    "reply_to_message_id" => $message_id,
                    "text" => 'Cool'
                ));
            }
        } else {
            $this->apiRequestJson("sendMessage", array(
                'chat_id' => $chat_id,
                "text" => 'I understand only text messages',
                'reply_markup' => array(
                    'remove_keyboard' => true
                )
            ));
        }
    }
}
