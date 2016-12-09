<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/settings.php';

use \Jpmena\Databases\Mysql\Controller\RifImporter;

/*
 * Le the array of requests to be achieved in the smae transaction!
 * In that case, the first request dumpss data into mysql
 * the seccond request the users table with the datas of the newly (re)loaded adherents' table
 */

$liste_parametres_imports = [
        ['bind_parameters' => [],
        'sql_command_text' => "update users set role = NULL where role = 'animateur'",
        'log_text' => "Préparation de la table users pour les animteurs avant recharge csv des animateurs",
    ],
        [
        'fichier_csv' => $chemins_fichiers['repertoire_csv'] . "/animateurs.csv",
        'csv_to_bind_parameters' => [':numero' => 0, ':surnom' => 1],
        'sql_command_text' => "UPDATE animateurs set numero = :numero, surnom = :surnom where numero = :numero", //Ici update et non REPLACE car les autres champs doivent rester les mêmes !!!
        'log_text' => "recharge de la table des animateurs à partir du fichier csv correspondant!"
    ],
     [
        'fichier_csv' => $chemins_fichiers['repertoire_csv'] . "/adherents.csv",
        'csv_to_bind_parameters' => [':numero' => 0, ':tel_domicile' => 9, ':tel_travail' => 11, ':tel_mobile' => 10],
        'sql_command_text' => "UPDATE animateurs set tel_domicile = :tel_domicile, tel_travail = :tel_travail, tel_mobile = :tel_mobile WHERE numero = :numero;", //Ici update et non REPLACE car les autres champs doivent rester les mêmes !!!
        'log_text' => "Suite à recharge de la table animateurs, mise à jour des numéros de téléphones des animateurs à partir du fichier adherents.csv"
    ],
        ['bind_parameters' => [],
        'sql_command_text' => "delete from animateurs where numero not in (select numero from adherents)",
        'log_text' => "Mise à jour de la table animateurs à partir des adhérents actuels à savoir ceux non expirés - le script adherents.php a été exécuté auparavant-"
    ],
        ['bind_parameters' => [],
        'sql_command_text' => "update users set role = 'animateur' where `username` in (select numero from animateurs) and (role <> 'admin' OR role is null)",
        'log_text' => "Mise à jour de la table users pour les animateurs suite à recharge de la table des animateurs",
    ]
];

$dateLog = new \DateTime();

$log_array = [
    'path' => $chemins_fichiers['repertoire_log'] . "/animateurs_" . $dateLog->format('Y-m-d_His') . ".log",
    'patterns' => [
        'glog_pattern' => 'animateurs_*\.log',
        'preg_pattern' => '/animateurs_(.*)\.log/',
        'date_pattern' => 'Y-m-d_His'
    ],
    'name' => "animateurs"
];

$adherentsImporter = new RifImporter($log_array['path'], $log_array['name'], $mysql_settings);

$resulat = $adherentsImporter->importerDonneesCsvEtValider($liste_parametres_imports);

$adherentsImporter->logHistoryCleanup($log_array['patterns'], $chemins_fichiers['repertoire_log'], $other_settings['log_history_depth']);

/*
 * Returns true or false pour le CRON OVH
 */
return $resulat['resultat_transaction']['res_t'];



