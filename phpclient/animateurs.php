<?php

require 'vendor/autoload.php';
require 'config/settings.php';

use Jpmena\Databases\Mysql\Controller\RifImporter;

/*
 * Le the array of requests to be achieved in the smae transaction!
 * In that case, the first request dumpss data into mysql
 * the seccond request the users table with the datas of the newly (re)loaded adherents' table
 */

$liste_parametres_imports = [
        ['bind_parameters' => [],
        'sql_command_text' => "update users set role = NULL where role = 'animateur'",
        'log_text' => "Préparation de la table users pour les animteurs avant import csv",
    ],
        [
        'fichier_csv' => $chemins_fichiers['repertoire_csv']."/animateurs.csv",
        'csv_to_bind_parameters' => [':numero' => 0, ':surnom' => 1],
        'sql_command_text' => "UPDATE animateurs set numero = :numero, surnom = :surnom where numero = :numero", //Ici update et non REPLACE car les autres champs doivent rester les mêmes !!!
        'log_text' => "Mise à jour d'un animateur"
    ],
        ['bind_parameters' => [],
        'sql_command_text' => "update users set role = 'animateur' where `username` in (select numero from animateurs) and (role <> 'admin' OR role is null)",
        'log_text' => "Mise à jour de la table users pour les animteurs suite à import csv",
    ]
];

$dateLog = new \DateTime();

$log_array = [
    'path' => $chemins_fichiers['repertoire_log']."/animateurs_" . $dateLog->format('Y-m-d_H:i:s') . ".log",
    'name' => "animateurs"
];

$adherentsImporter = new RifImporter($log_array['path'], $log_array['name'], $mysql_settings);

$resulat = $adherentsImporter->importerDonneesCsvEtValider($liste_parametres_imports);
/*
 * Returns true or false pour le CRON OVH
 */
return $resulat['resultat_transaction']['res_t'];



