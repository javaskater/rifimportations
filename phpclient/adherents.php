<?php

require 'vendor/autoload.php';

use Jpmena\Databases\Mysql\Controller\RifImporter;

/* $mysql_settings = [
  'host' => '192.168.56.101',
  =======
  $mysql_settings = [
  //'host' => '192.168.56.101',
  'host' => '127.0.0.1',
  'port' => '3306',
  'name' => 'rif',
  'username' => 'rif',
  'password' => 'rif',
  'charset' => 'utf8'
  ]; */

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

$liste_parametres_imports = [
        [
        'fichier_csv' => "/home/jpmena/RIF/importations/adherents.csv",
        'csv_to_bind_parameters' => [':numero' => 0, ':codepostal' => 6, ':expiration' => 17],
        'sql_command_text' => "REPLACE adherents set numero = :numero, codepostal = :codepostal, expiration = :expiration", //aura t'on un problème avec la date AAAA-MM-JJ ?
        'log_text' => "Importation / mise à jour d'un adherent"
    ],
        ['bind_parameters' => [],
        'sql_command_text' => "update users, adherents set users.expiration = adherents.expiration where users.username = adherents.numero",
        'log_text' => "Mise à jour de la table users",
    ]
];

$adherentsImporter = new RifImporter($mysql_settings);

$adherentsImporter->importerDonneesCsvEtValider($liste_parametres_imports);



