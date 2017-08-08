<?php
require 'vendor/autoload.php';

use Jpmena\RIF\Model\Database;

$texte_requete_sql = 'SELECT cle, date, titre, itineraire FROM randonnees WHERE typeRando = :type order by :order :updown';
$parametres_a_lier = array(
    ':type' => 'Journée',
    ':order' => 'date',
    ':updown' => 'DESC',
);

print_r($parametres_a_lier);

$rif_mysql = new Database();
$statement = $rif_mysql->executeRequeteUnitaire($texte_requete_sql, $parametres_a_lier);

print_r($statement);

while (($result = $statement['req']->fetchObject()) !== false) {
    echo "Numero: " . $result->cle . " le " . $result->date . " titre:" . $result->titre . " vers ->" . $result->itineraire . "\n";
}