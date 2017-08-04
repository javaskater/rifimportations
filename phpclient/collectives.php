<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/settings.php';

use \Jpmena\Databases\Mysql\Controller\RifImporter;
use \Jpmena\Databases\Mysql\Controller\RifDeleter;

/*
* reprendre les scripts mois par et mois impair à placer dans RifDeleter...
*/



/*
 * Le the array of requests to be achieved in the smae transaction!
 * In that case, the first request dumpss data into mysql
 * the seccond request the users table with the datas of the newly (re)loaded adherents' table
 */

$liste_parametres_imports = [
        [
        'fichier_csv' => $chemins_fichiers['repertoire_csv'] . "/randonnees.csv",
        'csv_to_bind_parameters' => [':numero' => 0, ':codepostal' => 6, ':expiration' => 17],
        'sql_command_text' => "REPLACE adherents set numero = :numero, codepostal = :codepostal, expiration = :expiration", //Pas de problème avec la date AAAA-MM-JJ ?
        'log_text' => "Recharge de la table des adhérents à partir du fichier csv correspondant"
    ]
];

$dateLog = new \DateTime();

$log_array = [
    'path' => $chemins_fichiers['repertoire_log'] . "/randonnees_" . $dateLog->format('Y-m-d_His') . ".log",
    'patterns' => [
        'glog_pattern' => 'randonnees_*\.log',
        'preg_pattern' => '/randonnees_(.*)\.log/',
        'date_pattern' => 'Y-m-d_His'
    ],
    'name' => "randonnees"
];

$randonneesImporter = new RifImporter($log_array['path'], $log_array['name'], $mysql_settings);
$randonneesDeleter = new RifDeleter($log_array['path'], $log_array['name'], $mysql_settings);

$resulat = $adherentsImporter->importerDonneesCsvEtValider($liste_parametres_imports);

$adherentsImporter->logHistoryCleanup($log_array['patterns'], $chemins_fichiers['repertoire_log'], $other_settings['log_history_depth']);

/*
 * Returns true or false pour le CRON OVH
 */
return $resulat['resultat_transaction']['res_t'];
