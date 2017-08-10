<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/settings.php';

use \Jpmena\RIF\Controller\RifImporter;

$expiration_jours = 120;
$now = new \DateTime();
$expiration_delay = $now->sub(new \DateInterval("P" . $expiration_jours . "D"))->format('Y-m-d');
;


/*
 * Le the array of requests to be achieved in the smae transaction!
 * In that case, the first request dumpss data into mysql
 * the seccond request the users table with the datas of the newly (re)loaded adherents' table
 */

$liste_parametres_imports = [
        [
        'fichier_csv' => $chemins_fichiers['repertoire_csv'] . "/adherents.csv",
        'csv_to_bind_parameters' => [':numero' => [0], ':codepostal' => [6], ':expiration' => [17]],
        'sql_command_text' => "REPLACE adherents set numero = :numero, codepostal = :codepostal, expiration = :expiration", //Pas de problème avec la date AAAA-MM-JJ ?
        'log_text' => "Recharge de la table des adhérents à partir du fichier csv correspondant"
    ],
        ['bind_parameters' => [':expiration_delay' => $expiration_delay],
        'sql_command_text' => "delete from adherents where expiration < :expiration_delay",
        'log_text' => "purge de la table des adherents pour les adhérents dont la date d'expiration est passée depuis  $expiration_jours jours",
    ],
        ['bind_parameters' => [],
        'sql_command_text' => "update users, adherents set users.expiration = adherents.expiration where users.username = adherents.numero",
        'log_text' => "Mise à jour de la table users suite à la recharge des adhérents",
    ],
        ['bind_parameters' => [':expiration_delay' => $expiration_delay],
        'sql_command_text' => "delete from users where expiration < :expiration_delay",
        'log_text' => "purge de la table users pour les adhérents dont la date d'expiration est passée depuis  $expiration_jours jours",
    ]
];

$dateLog = new \DateTime();

$log_array = [
    'path' => $chemins_fichiers['repertoire_log'] . "/adherents_" . $dateLog->format('Y-m-d_His') . ".log",
    'patterns' => [
        'glog_pattern' => 'adherents_*\.log',
        'preg_pattern' => '/adherents_(.*)\.log/',
        'date_pattern' => 'Y-m-d_His'
    ],
    'name' => "adherents"
];

$adherentsImporter = new RifImporter($log_array['path'], $log_array['name'], $mysql_settings);

$resulat = $adherentsImporter->importDataFromCsvAndValidate($liste_parametres_imports);

$adherentsImporter->logHistoryCleanup($log_array['patterns'], $chemins_fichiers['repertoire_log'], $other_settings['log_history_depth']);

/*
 * Returns true or false pour le CRON OVH
 */
return $resulat['resultat_transaction']['res_t'];



