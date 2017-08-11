<?php

namespace Jpmena\RIF\Controller;

use \Jpmena\RIF\Helper\RequesterTrait;
use \Jpmena\RIF\Helper\LoggerTrait;
use \Jpmena\RIF\Model\Database;

/**
 * Description of RifImporterFileSystem
 *
 * @author jpmena
 */
class RifDeleter {

    use RequesterTrait;
    use LoggerTrait;
    

    public function __construct($chemin_log, $nom_log, $mysql_settings = NULL) {
        $this->openLogFile($chemin_log, $nom_log);
        $this->my_database_model = new Database($mysql_settings);
        $this->my_database_model->importExistingLogger($this->exportExistingLogger());
    }
    
    /**
     * Execute the import transaction out of datas of many csv file.
     *
     * @param array of associative arrays.
     * Each associative array defines an array of requests to compose from a csv file
     *
     * @return array
     *   the gobal result of the transaction !!!
     */
    public function deleteFromCsvAndValidate($parameters_deletes_array) {
        $this->prepareRequestFromCsvFiles($parameters_deletes_array);
        $resultat_transaction_array = $this->my_database_model->executeTransaction();
        return [
            'parametres_imports_array' => $parameters_deletes_array,
            'resultat_transaction' => $resultat_transaction_array
        ];
    }


    /**
     * Get keys and attached files of hikes to delete
     *
     * @param parametres_imports: associative array with 3 keys:
     *       'fichier_csv' : the CSV File I get the datas from for my prepared SQL Request
     *       'csv_to_bind_parameters' : the associative array between prepared request parameters and value's index in the csv file
     *       'sql_command_text' : the prepared request (string)
     *
     * @return array
     *   Array of abspath of files to delete with the corresponding mysql's hike's key.
    */
    public function findPathsToDeleteFromCsv($parametres_imports){
        $this->prepareRequestFromCsvFile($parametres_imports);
        $resultat_transaction_array = $this->my_database_model->fetchAllAndAggregate(0);
        return $resultat_transaction_array;
        /* unlink files here ...*/
    }

}