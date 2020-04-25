<?php

namespace GameTextBot;

use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;

class Log
{
    static $loggers = array();
    public  static function get($name)
    {
        if (isset(Log::$loggers[$name]))
            $log = Log::$loggers[$name];
        else {
            $log = new Logger($name);
            $handler = new RotatingFileHandler('./logs/yournamelog.log', 0, Logger::DEBUG, true, 0664);
            $handler->setFilenameFormat('{date}', 'Y-m-d');
            $log->pushHandler($handler);
            Log::$loggers[$name] = $log;
        }
        return $log;
    }
}
