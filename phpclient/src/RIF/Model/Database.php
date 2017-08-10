<?php

namespace Jpmena\RIF\Model;

use \Jpmena\RIF\Helper\LoggerTrait;

/**
 * Description of Database
 *
 * @author jpmena
 */
class Database {
    
    use LoggerTrait;

    public static $default_settings = [
        'host' => '192.168.56.101',
        'port' => '3306',
        'name' => 'rif',
        'username' => 'rif',
        'password' => 'rif',
        'charset' => 'utf8'
    ];
    protected $pdo = NULL;
    protected $stmt_queries = [];

    /**
     * Constructor
     * @param array les paramètres de connexion à la base de donnée
     */
    public function __construct($settings = NULL) {
        if($settings == NULL){
            $settings = self::$default_settings;
        }
        try {
            $this->pdo = new \PDO(
                    sprintf(
                            'mysql:host=%s;dbname=%s;port=%s;charset=%s', $settings['host'], $settings['name'], $settings['port'], $settings['charset']
                    ), $settings['username'], $settings['password']
            );
            //obliger l'usage des exceptions http://php.net/manual/en/pdo.error-handling.php
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            /* Database connection failed
             * http://php.net/manual/fr/class.pdoexception.php
             */
            $this->error("connexion à la BDD echoue, cause ", $e->getMessage());
            exit;
        }
    }
    
    /**
     * Fonction utilitaire
     * @param string le texte de la requête SQL avec les emplacements pour les bindValues
     * @param array le tableau des bind values, une liste de clé/valeur où la clé est le marqueur d'emplacement et la valeur 
     * la donnée à appliquer à cet emplacement de la requête
     * Ajoute la requête une fois préparée à la liste de requêtes ppour transaction
     */
    public function prepareRequetePourTransaction($sqlCommmandText, $bindParameters, $log_text = NULL) {
        if ($this->pdo != NULL) {
            $stmtRequete = $this->pdo->prepare($sqlCommmandText);
            foreach ($bindParameters as $bindCle => $bindValeur) {
                $stmtRequete->bindValue($bindCle, $bindValeur);
            }
            $my_query=[
                'sql_text' => $sqlCommmandText,
                'bind_parameters' => $bindParameters,
                'prepared_statement' => $stmtRequete,
                'log_text' => $log_text
            ];
            $this->stmt_queries[] = $my_query;
        }
    }

    
    /**
     * Exécution d'une transaction complète
     * lance les reqêtes à effectuer dans l'ordre de la transsaction 
     * prévue
     * see http://stackoverflow.com/questions/8618618/php-pdo-mysql-transaction-code-structure
     * pour demander la gestion des exceptions ....
     */
    public function executeTransaction() {
        $resultTransaction = FALSE;
        $requetesExecutees = [];
        if (count($this->stmt_queries) > 0) {
            $this->debug("Starting transaction");
            $this->pdo->beginTransaction();
            try {
                foreach ($this->stmt_queries as $requeteDansTransaction) {
                    //var_dump($requeteDansTransaction['prepared_statement']);
                    $this->debug("executing mysql request:".$requeteDansTransaction['log_text']);
                    $preparedStatement = $requeteDansTransaction['prepared_statement'];
                    //$preparedStatement->debugDumpParams();
                    $res = $preparedStatement->execute();
                    if (!$res) {
                        $this->error("problème à l'exécution du statement");
                        throw new \PDOException('Requete terminée avec Code KO',-99);
                    }
                    $requetesExecutees[] = $requeteDansTransaction;
                }
                $this->pdo->commit();
                $this->debug("Ending transaction with success");
            }catch (\PDOException $pdoe){
                $c = $pdoe->getCode();
                $m = $pdoe->getMessage();
                $this->error("Ending transaction with failure; code: $c,message: $m");
            } finally {
                $this->stmt_queries=[];
                return array('res_t'=> $resultTransaction,'reqs_t' => $requetesExecutees);
            }
        }
        return array('res_t'=> $resultTransaction,'reqs_t' => $requetesExecutees);
    }


    /**
     * Fetchs and aggregate the results from all the requests
     */
    public function fetchAllAndAggregate($index_col_to_fetch) {
        $global_result_from_fetch = [];
        $fetchRequestsExecuted = [];
        if (count($this->stmt_queries) > 0) {
            $this->debug("Starting transaction");
            $i=1;
            foreach ($this->stmt_queries as $fetch_request) {
                $this->debug("executing mysql request:".$fetch_request['log_text']);
                // see http://php.net/manual/fr/pdostatement.fetchall.php
                $local_result_from_fetch = $fetch_request['prepared_statement']->fetchAll(\PDO::FETCH_COLUMN, $index_col_to_fetch);
                if ($local_result_from_fetch) {
                    $global_result_from_fetch = array_merge(global_result_from_fetch, $local_result_from_fetch);
                    $fetchRequestsExecuted[] = $fetch_request;
                    $i++;
                } 
            }
        }
        return array('res_f'=> $global_result_from_fetch,'reqs_t' => $fetchRequestsExecuted);
    }
    
    /**
     *
     * @param string le texte de la requête SQL à préparer avec les emplacements pour les bindValues
     * @param array le tableau des bind values, une liste de clé/valeur où la clé est le marqueur d'emplacement et la valeur 
     * la donnée à appliquer à cet emplacement de la requête
     * exécute la requête et retourne le statement 
     * en général à des fins d'exploitation pour lecture itérative des lignes retrounées
     */
    public function executeRequeteUnitaire($sqlCommmandText, $bindParameters) {
        if ($this->pdo != NULL) {
            $requeteUnitaire = $this->pdo->prepare($sqlCommmandText);
            $this->debug("sql: $sqlCommmandText");
            //print_r($bindParameters);
            foreach ($bindParameters as $bindCle => $bindValeur) {
                //echo "à lier|$bindCle|=>|$bindValeur| \n";
                $requeteUnitaire->bindValue($bindCle, $bindValeur); 
                /*et non pas bindparam car sinon il attend 
                 * une référence et ne fait rien!!!!
                 */
            }
            /*$requeteUnitaire->bindValue(':type', 'Journée'); 
            $requeteUnitaire->bindValue(':order', 'date');
            $requeteUnitaire->bindValue(':updown', 'DESC');*/
            $resultRequete = $requeteUnitaire->execute();
            //$requeteUnitaire->debugDumpParams();
            //print_r($result);
            return array('res'=> $resultRequete,'req' => $requeteUnitaire);
        }
    }

}
