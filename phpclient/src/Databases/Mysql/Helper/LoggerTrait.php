<?php

namespace Jpmena\Databases\Mysql\Helper;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 *
 * @author jpmena
 *  Cette Trait permet de loguer 
 * en mode INFO / ERROR / WARNING
 * cf. https://github.com/Seldaek/monolog
 */
trait LoggerTrait {
    
    private $log = NULL;
    
    public function openLogFile($chemin_log = 'rif.log', $nom_log = 'Rif') {
        $this->log = new Logger($nom_log);
        $this->log->pushHandler(new StreamHandler($chemin_log, Logger::DEBUG));
    }
    
    public function exportExistingLogger(){
        return $this->log;
    }
    
    public function importExistingLogger($monolog_logger){
        $this->log = $monolog_logger;
    }
    
    public function debug($message) {
        if ($this->log){
            $this->log->debug($message);
        }else{
            echo "DEBUG: $message\n";
        }
    }

    public function warn($message) {
        if ($this->log){
            $this->log->warn($message);
        }else{
            echo "WARNING: $message\n";
        }
    }

    public function error($message) {
        if ($this->log){
            $this->log->error($message);
         }else{
            echo "ERROR: $message\n";
        }
    }
}
