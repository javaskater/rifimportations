<?php

require 'vendor/autoload.php';

use Jpmena\Databases\Mysql\Controller\RifImporter;

<<<<<<< HEAD
/*$mysql_settings = [
    'host' => '192.168.56.101',
=======
$mysql_settings = [
    //'host' => '192.168.56.101',
    'host' => '127.0.0.1',
>>>>>>> c2699ca3787f1e7d94254f440213e4f148d5fb94
    'port' => '3306',
    'name' => 'rif',
    'username' => 'rif',
    'password' => 'rif',
    'charset' => 'utf8'
];*/

$mysql_settings = [
    'host' => '127.0.0.1',
    'port' => '3306',
    'name' => 'rif',
    'username' => 'rif',
    'password' => 'rif',
    'charset' => 'utf8'
];

/*
 * Le the array of requests to be achieved in the smae transaction!
 * In that case, the first request dumpss data into mysql
 * the seccond request the users table with the datas of the newly (re)loaded adherents' table
 */

<<<<<<< HEAD
$liste_parametres_imports = array([
        'fichier_csv' => "/home/jpmena/RIF/importations/adherents.csv",
        'csv_to_bind_parameters' => [':numero' => 0, ':codepostal' => 6, ':expiration' => 17],
        'sql_command_text' => "REPLACE adherents set numero = :numero, codepostal = :codepostal, expiration = :expiration",//aura t'on un problème avec la date AAAA-MM-JJ ?
=======
$liste_parametres_imports = array(['bind_parameters' => [
            //':fichier_csv' => "/home/jpmena/RIF/importations/adherents.csv",
            //':table_mysql' => "adherents",
            //':liste_colonnes_pour_mysqldump' => 'numero,@temp,@temp,@temp,@temp,@temp,codepostal,@temp,@temp,@temp,@temp,@temp,@temp,@temp,@temp,@temp,@temp,expiration',
            ],
        //'sql_command_text' => "LOAD DATA INFILE :fichier_csv REPLACE INTO TABLE :table_mysql CHARACTER SET 'utf8' FIELDS OPTIONALLY ENCLOSED BY \" TERMINATED BY , LINES TERMINATED BY \n IGNORE 1 LINES (:liste_colonnes_pour_mysqldump)",
        //'sql_command_text' => "LOAD DATA LOCAL INFILE '/home/jpmena/RIF/importations/adherents.csv' REPLACE INTO TABLE adherents CHARACTER SET 'utf8' FIELDS OPTIONALLY ENCLOSED BY '\"' TERMINATED BY ',' LINES TERMINATED BY '\n' IGNORE 1 LINES (numero,@temp,@temp,@temp,@temp,@temp,codepostal,@temp,@temp,@temp,@temp,@temp,@temp,@temp,@temp,@temp,@temp,expiration)",
        'sql_command_text' =>  "LOAD DATA INFILE '/home/jpmena/RIF/importations/adherents.csv' REPLACE INTO TABLE adherents CHARACTER SET 'utf8' FIELDS OPTIONALLY ENCLOSED BY '\"' TERMINATED BY ',' LINES TERMINATED BY '\\n' IGNORE 1 LINES (numero,@temp,@temp,@temp,@temp,@temp,codepostal,@temp,@temp,@temp,@temp,@temp,@temp,@temp,@temp,@temp,@temp,expiration)",
>>>>>>> c2699ca3787f1e7d94254f440213e4f148d5fb94
        'log_text' => "Importation des adherents",
    ],
    ['bind_parameters' => [],
        'sql_command_text' => "update users, adherents set users.expiration = adherents.expiration where users.username = adherents.numero",
         'log_text' => "Mise à jour de la table users",
    ]
);

$adherentsImporter = new RifImporter($mysql_settings);

$adherentsImporter->importerDonneesCsvEtValider($liste_parametres_imports);



