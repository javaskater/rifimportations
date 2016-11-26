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
    
    public function __construct($mysql_settings=NULL) {
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
    public function importerDonneesCsvEtValider($parametres_imports_array){
        foreach ($parametres_imports_array as $parametres_imports){
            $sqlCommmandText = $parametres_imports['sql_command_text'];
            $bindParameters = $parametres_imports['bind_parameters'];
            $log_text = $parametres_imports['log_text'];
            $this->myDatabaseModel->prepareRequetePourTransaction($sqlCommmandText, $bindParameters, $log_text);
        }
        $resultat_transaction = $this->myDatabaseModel->executeTransaction();
        return [
            'parametres_imports_array' => $parametres_imports_array,
            'resultat_transaction' => $resultat_transaction
        ];
    }
}
