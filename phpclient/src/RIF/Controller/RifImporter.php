<?php

namespace Jpmena\RIF\Controller;

use \Jpmena\RIF\Helper\RequesterTrait;
use \Jpmena\RIF\Helper\LoggerTrait;
use \Jpmena\RIF\Model\Database;

/**
 * Description of RifImporter
 *
 * @author jpmena
 */
class RifImporter {
    
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
    public function importDataFromCsvAndValidate($parametres_imports_array) {
        $this->prepareRequestFromCsvFiles($parametres_imports_array);
        $resultat_transaction_array = $this->my_database_model->executeTransaction();
        return [
            'parametres_imports_array' => $parametres_imports_array,
            'resultat_transaction' => $resultat_transaction_array
        ];
    }

}
