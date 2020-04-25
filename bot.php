<?php
// Load composer
require __DIR__ . '/vendor/autoload.php';

use GameTextBot\Log;
use GameTextBot\ChatBot;

define('BOT_TOKEN', '<YOUR_TELEGRAM_TOKEN>');
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');
define('CDN_IMG_URL', '<YOUR_IMG_URL>');

try {
    $bot = new ChatBot();
    $bot->Process();
} catch (Exception $e) {
    Log::get('bot')->error($e->getMessage());
}
