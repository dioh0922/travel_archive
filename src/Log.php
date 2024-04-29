<?php
namespace Src;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Log{
    private $logger;
    public function __construct(){
        $this->logger = new Logger("log");
        $this->logger->pushHandler(new StreamHandler(sprintf("../logs/%s.log", date("Ymd")), Logger::DEBUG));
    }

    public function logInfo(string $str){
        $this->logger->info($str);
    }

    public function logError(string $str){
        $this->logger->error($str);
    }
}