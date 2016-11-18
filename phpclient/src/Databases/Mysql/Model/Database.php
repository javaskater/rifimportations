<?php

namespace Jpmena\Databases\Mysql\Model;

/**
 * Description of Database
 *
 * @author jpmena
 */
class Database {

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
        } catch (\PDOException $e) {
            /* Database connection failed
             * http://php.net/manual/fr/class.pdoexception.php
             */
            echo "connexion à la BDD echoue, cause ", $e->getMessage();
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
    public function prepareRequetePourTransaction($sqlCommmandText, $bindParameters) {
        if ($this->pdo != NULL) {
            $stmtRequete = $pdo->prepare($sqlCommmandText);
            foreach ($bindParameters as $bindCle => $bindValeur) {
                $stmtRequete->bindParam($bindCle, $bindValeur);
            }
            $this->stmt_queries[] = $stmtRequete;
        }
    }

    
    /**
     * Exécution d'une transaction complète
     * lance les reqêtes à effectuer dans l'ordre de la transsaction 
     * prévue
     */
    public function executeTransaction() {
        if (count($this->stmt_queries) > 0) {
            $this->pdo->beginTransaction();
            foreach ($this->stmt_queries as $requeteDansTransaction) {
                $requeteDansTransaction->execute();
            }
            $this->pdo->commit();
            $this->stmt_queries=[];
        }
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
            print_r($bindParameters);
            foreach ($bindParameters as $bindCle => $bindValeur) {
                $requeteUnitaire->bindParam($bindCle, $bindValeur);
                //print_r($bindCle);
            }
            $requeteUnitaire->execute();
            return $requeteUnitaire;
        }
    }

}
