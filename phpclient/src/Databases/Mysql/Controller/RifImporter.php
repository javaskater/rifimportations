<?php

namespace Jpmena\Databases\Mysql\Controller;

use Jpmena\Databases\Mysql\Model\Database;

/**
 * Description of RifImporter
 *
 * @author jpmena
 */
class RifImporter {

    private $myDatabaseModel;

    public function __construct($mysql_settings = NULL) {
        $this->myDatabaseModel = new Database($mysql_settings);
    }

    /**
     * Get nids of the nodes to delete.
     *
     * @param array $roles
     *   Array of roles.
     *
     * @return array
     *   Array of nids of nodes to delete.
     */
    public function importerDonneesCsvEtValider($parametres_imports_array) {
        foreach ($parametres_imports_array as $parametres_imports) {
            if ($parametres_imports['fichier_csv'] && file_exists($parametres_imports['fichier_csv'])) {
                $csv = \League\Csv\Reader::createFromPath($parametres_imports['fichier_csv']);
                $firstline = TRUE;
                foreach ($csv as $csvRow) {
                    if (!$firstline) { //La première ligne est celle des titres
                        $bindkeys_csvpos = $parametres_imports['csv_to_bind_parameters'];
                        $bindParameters = [];
                        foreach ($bindkeys_csvpos as $bindkey => $csvpos) {
                            $bindParameters[$bindkey] =  $csvRow[$csvpos];
                        }
                        print_r($bindParameters);
                        $sqlCommmandText = $parametres_imports['sql_command_text'];
                        $log_text = $parametres_imports['log_text'];
                        $this->myDatabaseModel->prepareRequetePourTransaction($sqlCommmandText, $bindParameters, $log_text);
                    } else {
                        $firstline = FALSE;
                    }
                }
            } else {
                $bindParameters = $parametres_imports['bind_parameters'];
                $sqlCommmandText = $parametres_imports['sql_command_text'];
                $log_text = $parametres_imports['log_text'];
                $this->myDatabaseModel->prepareRequetePourTransaction($sqlCommmandText, $bindParameters, $log_text);
            }
        }
        $resultat_transaction = $this->myDatabaseModel->executeTransaction();
        return [
            'parametres_imports_array' => $parametres_imports_array,
            'resultat_transaction' => $resultat_transaction
        ];
    }

}
