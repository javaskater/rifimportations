<?php

namespace Jpmena\RIF\Helper;

use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;

/**
 *
 * @author jpmena
 * Cette Trait permet de convertir des formats pour
 * les requêtes Mysql / PDO / Csv
 */
trait ConverterTrait {

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
