<?php

namespace Jpmena\RIF\Helper;

trait RequesterTrait {

    private $my_database_model;

    /**
     * Enriches the Database transaction with request's datas from the CSV file.
     *
     * @param $parametres_imports: associative array with 3 keys:
     *       'fichier_csv' : the CSV File I get the datas from for my prepared SQL Request
     *       'csv_to_bind_parameters' : the associative array between prepared request parameters and value's index in the csv file
     *       'sql_command_text' : the prepared request (string)
     * @return void
     *   
    */
    function prepareRequestFromCsvFile($parametres_requests){
        if (array_key_exists ( 'fichier_csv' , $parametres_requests ) && file_exists($parametres_requests['fichier_csv'])) {
            $csv = \League\Csv\Reader::createFromPath($parametres_requests['fichier_csv']);
            $firstline = TRUE;
            foreach ($csv as $csvRow) {
                if (!$firstline) { //La première ligne est celle des titres
                    $bindkeys_csvpos = $parametres_requests['csv_to_bind_parameters'];
                    $bindParameters = [];
                    foreach ($bindkeys_csvpos as $bindkey => $arr_value) {
                        $pdo_format="string";
                        $csvpos = $arr_value[0];
                        if (count($arr_value) > 1){
                            $pdo_format=$arr_value[1];
                        }
                        $bindParameters[$bindkey] =  $this->pdo_convert($csvRow[$csvpos], $pdo_format);
                    }
                    //print_r($bindParameters);
                    $sqlCommmandText = $parametres_requests['sql_command_text'];
                    $log_text = "++csv:".$parametres_requests['log_text'];
                    $this->my_database_model->prepareRequetePourTransaction($sqlCommmandText, $bindParameters, $log_text);
                } else {
                    $firstline = FALSE;
                }
            }
        } else {
            $bindParameters = $parametres_imports['bind_parameters'];
            $sqlCommmandText = $parametres_imports['sql_command_text'];
            $log_text = $parametres_imports['log_text'];
            $this->my_database_model->prepareRequetePourTransaction($sqlCommmandText, $bindParameters, $log_text);
        }
    }


    /**
     * Enriches the Database transaction with request's datas from the CSV file.
     *
     * @param $parametres_imports: an array of the above asociative arrays
     * @return void
     *   
    */
    function prepareRequestFromCsvFiles($parameters_array){
        foreach ($parameters_array as $parameters) {
            $this->prepareRequestFromCsvFile($parameters);
        }
    }

    /**
    *
    * @author jpmena
    * Helps converting different PHP Class
    * for the  PDO / Mysql prepared requests
    */
    public function pdo_convert($row_value, $pdo_format){
        $pdo_value=$row_value;
        switch ($pdo_format):
            case "datetime": //see: http://php.net/manual/fr/class.datetime.php
                $pdo_value = \DateTime::createFromFormat ( 'Y/m/d H:i:s' , $row_value)->format('Y-m-d H:i:s'); //do I need the TimeZone ?
                break;
            case "date":
                $pdo_value = \DateTime::createFromFormat ( 'Y-m-d H:i:s' , $row_value." 12:00:00")->format('Y-m-d'); //do I need the TimeZone ?
                break;
             case "float":
                $pdo_value = floatval($row_value);
                break;
        endswitch;
        return $pdo_value;
    }
}