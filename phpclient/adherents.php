<?php

require 'vendor/autoload.php';

use Jpmena\Databases\Mysql\Controller\RifImporter;

$mysql_settings = [
    'host' => '192.168.56.101',
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

$liste_parametres_imports = array(['bind_parameters' => [
            ':fichier_csv' => "/home/jpmena/RIF/importations/adherents.csv",
            ':table_mysql' => "adherents",
            ':liste_colonnes_pour_mysqldump' => "numero,@temp,@temp,@temp,@temp,@temp,codepostal,@temp,@temp,@temp,@temp,@temp,@temp,@temp,@temp,@temp,@temp,expiration",
            ':fields_separator' => ",",
            ':line_separator' => "\\n",
            ':enclosed_by' => "\""],
        'sql_command_text' => "LOAD DATA LOCAL INFILE \':fichier\' REPLACE INTO TABLE :table_mysql CHARACTER SET \'utf8\' FIELDS OPTIONALLY ENCLOSED BY \':enclosed_by\' TERMINATED BY \':fields_separator\' LINES TERMINATED BY \':line_separator\' IGNORE 1 LINES (:liste_colonnes_pour_mysqldump)",
        'log_text' => "Importation des adherents",
    ],
        ['bind_parameters' => [],
        'sql_command_text' => "update users, adherents set users.expiration = adherents.expiration where users.username = adherents.numero",
         'log_text' => "Mise à jour de la table users",
    ]
);

$adherentsImporter = new RifImporter($mysql_settings);

$adherentsImporter->importerDonneesCsvEtValider($liste_parametres_imports);



