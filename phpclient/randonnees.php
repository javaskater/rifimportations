<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/settings.php';

use \Jpmena\RIF\Controller\RifImporter;
use \Jpmena\RIF\Controller\RifDeleter;

/*
* reprendre les scripts mois par et mois impair à placer dans RifDeleter...
*/



/*
 * Le the array of requests to be achieved in the smae transaction!
 * In that case, the first request dumpss data into mysql
 * the seccond request the users table with the datas of the newly (re)loaded adherents' table
 */

$import_hikes_parameters_list = [
        [
        'fichier_csv' => $chemins_fichiers['repertoire_csv'] . "/randonnees.csv",
        'csv_to_bind_parameters' => [':cle' => [0],':date' => [1,'date'],':typeRando' => [2],':nbParticipants' => [3],':titre' => [4],':nomProgramme' => [5], ':distanceInferieure' => [6,'float'],':distanceCalculee' => [7,'float'], ':distanceSuperieure' => [8,'float'], ':unite' => [9] ,'allure' => [10],':heureDepartAller' => [11,'datetime'], ':heureChgtArriveeAller' => [12,'datetime'],
        ':heureChgtDepartAller' => [13,'datetime'] ,':heureArriveeAller' => [14,'datetime'],':heureDepartRetour' => [15,'datetime'],':heureChgtArriveeRetour' => [16,'datetime'], ':heureChgtDepartRetour' => [17,'datetime'], ':heureArriveeRetour' => [18,'datetime'],':allerGareDepart' => [19], ':allerChangementNb' => [20], ':allerGareArrivee' => [21], ':itineraire' => [22], ':retourGareDepart' => [23], 
        ':retourChangementNb' => [24], ':retourGareArrivee' => [25], ':multiDates' => [26], ':horairesVerification' => [27], ':commentaires' => [28], ':efface' => [29], ':syncLocal' => [30], ':syncDistant' => [31]],
        'sql_command_text' => "REPLACE randonnees set cle = :cle, date = :date, typeRando = :typeRando, nbParticipants = :nbParticipants, titre = :titre, nomProgramme = :nomProgramme, distanceInferieure = :distanceInferieure, distanceCalculee = :distanceCalculee, distanceSuperieure = :distanceSuperieure, unite =:unite, allure =:allure, 
        heureDepartAller = :heureDepartAller, heureChgtArriveeAller = :heureChgtArriveeAller, heureChgtDepartAller = :heureChgtDepartAller, heureArriveeAller = :heureArriveeAller, heureDepartRetour = :heureDepartRetour, 
        heureChgtArriveeRetour = :heureChgtArriveeRetour, heureChgtDepartRetour = :heureChgtDepartRetour, heureArriveeRetour = :heureArriveeRetour,
        allerGareDepart =:allerGareDepart, allerChangementNb =:allerChangementNb, allerGareArrivee = :allerGareArrivee, itineraire = :itineraire, retourGareDepart = :retourGareDepart,
        retourChangementNb = :retourChangementNb, retourGareArrivee = :retourGareArrivee, multiDates = :multiDates, horairesVerification = :horairesVerification, commentaires = :commentaires,
        efface = :efface, syncLocal = :syncLocal, syncDistant = :syncDistant, denivelees ='', description_denivelees=''", //Pas de problème avec la date AAAA-MM-JJ ?
        'log_text' => "Recharge de la table des randonnées de jour à partir du fichier csv correspondant"
    ]
];


$select_hikes_files_parameters_list = [
        'fichier_csv' => $chemins_fichiers['repertoire_csv'] . "/randonnees.eff.csv",
        'csv_to_bind_parameters' => [':cle' => [0]],
        'sql_command_text' => "SELECT fichier FROM fichiers WHERE randonnee_cle = :cleRando",
        'log_text' => "sélection des chemins des fichiers associés aux randonnées de jour à supprimer"
];

$delete_hikes_files_parameters_list = [
        'fichier_csv' => $chemins_fichiers['repertoire_csv'] . "/randonnees.eff.csv",
        'csv_to_bind_parameters' => [':cle' => [0]],
        'sql_command_text' => "DELETE FROM fichiers WHERE randonnee_cle = :cleRando",
        'log_text' => "supression des fichiers associés aux randonnées de jours en base"
];

$delete_hikes_comments_parameters_list = [
        'fichier_csv' => $chemins_fichiers['repertoire_csv'] . "/randonnees.eff.csv",
        'csv_to_bind_parameters' => [':cle' => [0]],
        'sql_command_text' => "DELETE FROM commentaires WHERE randonnee_cle = :cleRando",
        'log_text' => "suppression des commentaires des randonnées de jours en base"
];

$delete_hikes_parameters_list = [
        'fichier_csv' => $chemins_fichiers['repertoire_csv'] . "/randonnees.eff.csv",
        'csv_to_bind_parameters' => [':cle' => [0]],
        'sql_command_text' => "DELETE FROM randonnees WHERE cle = :cleRando",
        'log_text' => "suppression des randonnées de jours en base"
];

$delete_day_hikes = [$delete_hikes_files_parameters_list, $delete_hikes_comments_parameters_list, $delete_hikes_parameters_list];

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

$hikesImporter = new RifImporter($log_array['path'], $log_array['name'], $mysql_settings);
$new_hikes_transaction_res = $hikesImporter->importDataFromCsvAndValidate($import_hikes_parameters_list);

$hikesDeleter = new RifDeleter($log_array['path'], $log_array['name'], $mysql_settings);
$hikes_files_to_remove_arr = $hikesDeleter->findPathsToDeleteFromCsv($select_hikes_files_parameters_list);
$deleted_hikes_transaction_res = $hikesDeleter->deleteFromCsvAndValidate($delete_day_hikes);

var_dump($deleted_hikes_transaction_res);

//$hikesDeleter->logHistoryCleanup($log_array['patterns'], $chemins_fichiers['repertoire_log'], $other_settings['log_history_depth']);
