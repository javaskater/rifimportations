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
        'csv_to_bind_parameters' => [':cle' => 0,':date' => 1,':typeRando' => 2,':nbParticipants' => 3,':titre' => 4,':nomProgramme' => 5, ':distanceInferieure' => 6,':distanceCalculee' => 7, ':distanceSuperieure' => 8, ':unite' => 9 ,'allure' => 10,':heureDepartAller' => 11, ':heureChgtArriveeAller' => 12,
        ':heureChgtDepartAller' => 13 ,':heureArriveeAller' => 14,':heureDepartRetour' => 15,':heureChgtArriveeRetour' => 16, ':heureChgtDepartRetour' => 17, ':heureArriveeRetour' => 18,':allerGareDepart' => 19, ':allerChangementNb' => 20, ':allerGareArrivee' => 21, ':itineraire' => 22, ':retourGareDepart' => 23, 
        ':retourChangementNb' => 24, ':retourGareArrivee' => 25, ':multiDates' => 26, ':horairesVerification' => 27, ':commentaires' => 28, ':efface' => 29, ':syncLocal' => 30, ':syncDistant' => 31],
        'sql_command_text' => "REPLACE randonnees set cle = :cle, date = :date, typeRando = :typeRando, nbParticipants = :nbParticipants, titre = :titre, nomProgramme = :nomProgramme, distanceInferieure = CAST(:distanceInferieure AS DECIMAL(1,0)), distanceCalculee = CAST(:distanceCalculee AS DECIMAL(1,0)), distanceSuperieure = CAST(:distanceSuperieure AS DECIMAL(1,0)), unite =:unite, allure =:allure, 
        heureDepartAller = STR_TO_DATE(:heureDepartAller, '%Y/%m/%d %h:%i:%s'), heureChgtArriveeAller = STR_TO_DATE(:heureChgtArriveeAller, '%Y/%m/%d %h:%i:%s'), heureChgtDepartAller = STR_TO_DATE(:heureChgtDepartAller, '%Y/%m/%d %h:%i:%s'), heureArriveeAller = STR_TO_DATE(:heureArriveeAller, '%Y/%m/%d %h:%i:%s'), heureDepartRetour = STR_TO_DATE(:heureDepartRetour, '%Y/%m/%d %h:%i:%s'), 
        heureChgtArriveeRetour = STR_TO_DATE(:heureChgtArriveeRetour, '%Y/%m/%d %h:%i:%s'), heureChgtDepartRetour = STR_TO_DATE(:heureChgtDepartRetour, '%Y/%m/%d %h:%i:%s'), heureArriveeRetour = STR_TO_DATE(:heureArriveeRetour, '%Y/%m/%d %h:%i:%s'),
        allerGareDepart =:allerGareDepart, allerChangementNb =:allerChangementNb, allerGareArrivee = :allerGareArrivee, itineraire = :itineraire, retourGareDepart = :retourGareDepart,
        retourChangementNb = :retourChangementNb, retourGareArrivee = :retourGareArrivee, multiDates = :multiDates, horairesVerification = :horairesVerification, commentaires = :commentaires, efface = :efface, syncLocal = :syncLocal, syncDistant = :syncDistant", //Pas de problème avec la date AAAA-MM-JJ ?
        'log_text' => "Recharge de la table des randonnées de jour à partir du fichier csv correspondant"
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
//$randonneesDeleter = new RifDeleter($log_array['path'], $log_array['name'], $mysql_settings);

$resulat = $randonneesImporter->importerDonneesCsvEtValider($liste_parametres_imports);

$randonneesImporter->logHistoryCleanup($log_array['patterns'], $chemins_fichiers['repertoire_log'], $other_settings['log_history_depth']);

/*
 * Returns true or false pour le CRON OVH
 */
return $resulat['resultat_transaction']['res_t'];
