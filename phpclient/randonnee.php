<?php

include('config/settings.php');
try {
    $pdo = new PDO(
            sprintf(
                    'mysql:host=%s;dbname=%s;port=%s;charset=%s', $settings['host'], $settings['name'], $settings['port'], $settings['charset']
            ), $settings['username'], $settings['password']
    );
} catch (PDOException $e) {
    /* Database connection failed
     * http://php.net/manual/fr/class.pdoexception.php
     */
    echo "connexion à la BDD echoue, cause ",$e->getMessage();
    exit;
}

$sql = 'SELECT cle, date, titre, itineraire FROM randonnees WHERE typeRando = :type order by :order :updown';
$statement = $pdo->prepare($sql);

$statement->bindValue(':type', 'Journée');
$statement->bindValue(':order', 'date');
$statement->bindValue(':updown', 'DESC');

$statement->execute();
// Iterate results
while (($result = $statement->fetchObject()) !== false) {
    echo "Numero: " . $result->cle . " le " . $result->date . " titre:" . $result->titre . " vers ->" . $result->itineraire . "\n";
}