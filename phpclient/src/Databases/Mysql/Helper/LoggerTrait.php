<?php

namespace Jpmena\Databases\Mysql\Helper;

use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;

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

    public function exportExistingLogger() {
        return $this->log;
    }

    public function importExistingLogger($monolog_logger) {
        $this->log = $monolog_logger;
    }

    public function debug($message) {
        if ($this->log) {
            $this->log->debug($message);
        } else {
            echo "DEBUG: $message\n";
        }
    }

    public function warn($message) {
        if ($this->log) {
            $this->log->warn($message);
        } else {
            echo "WARNING: $message\n";
        }
    }

    public function error($message) {
        if ($this->log) {
            $this->log->error($message);
        } else {
            echo "ERROR: $message\n";
        }
    }

    public function logHistoryCleanup($log_patterns, $log_directory, $log_history_depth = 10) {
        $glob_pattern = $log_patterns['glog_pattern'];
        $preg_pattern = $log_patterns['preg_pattern'];
        $date_patttern = $log_patterns['date_pattern'];
        $my_log_files = glob("$log_directory/$glob_pattern");
        $logs_to_sort = [];
        foreach ($my_log_files as $my_log_file) {
            $log_file_name = basename($my_log_file);
            //echo "$preg_pattern $log_file_name\n";
            if (preg_match($preg_pattern, basename($my_log_file), $field)) {
                //var_dump($field);
                $log = [
                    'abs_path' => $my_log_file,
                    'creation_date' => \DateTime::createFromFormat($date_patttern, $field[1]),
                ];
                $logs_to_sort[] = $log;
            }
        }

        if (count($logs_to_sort) > 0) {
            usort($logs_to_sort, function($a, $b) {
                $ad = $a['creation_date'];
                $bd = $b['creation_date'];

                if ($ad == $bd) {
                    return 0;
                }

                return $ad > $bd ? -1 : 1; //we reverse sort ... Most recent first
            });

            //var_dump($logs_to_sort);
        }
        for ($i = 0; $i < count($logs_to_sort); $i++){
            if ($i >=  $log_history_depth){
                $log_abspath = $logs_to_sort[$i]['abs_path'];
                $this->debug("on supprime l'ancien fichier de log:".$log_abspath);
                unlink($log_abspath);
            }
        }
    }

}
