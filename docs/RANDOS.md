# tout sur la mise à jour des randonnées de jour en PHP

## TODO

* Mettre à jour les randonnées de jour _commande [REPLACE](https://dev.mysql.com/doc/refman/5.7/en/replace.html)_ (fichier _randonnees.csv_)
* supprimer les anciennes randonnées à effacer (fichier _randonnees.eff.csv_)
* supprimer les anciennes randonnées du programme précédent (reprendre le calcul de la date minimale avec mois pair/impar)

### NB pour chaque suppression

* supprimer les commentaires associés à une randonnée (si existent)
* supprimer le fichier joint attaché à une randonnée (si existe)

## Ce que fait _randonnees.php_

* Les colonnes à insérer/remplacer par importation CSV ....
* _commande [REPLACE](https://dev.mysql.com/doc/refman/5.7/en/replace.html)_ (fichier _randonnees.csv_)

```
mysql> desc randonnees;
+------------------------+--------------+------+-----+---------+----------------+
| Field                  | Type         | Null | Key | Default | Extra          |
+------------------------+--------------+------+-----+---------+----------------+
| cle                    | int(10)      | NO   | PRI | NULL    | auto_increment |
| date                   | date         | YES  |     | NULL    |                |
| typeRando              | varchar(20)  | YES  |     | NULL    |                |
| nbParticipants         | int(11)      | YES  |     | NULL    |                |
| titre                  | varchar(255) | YES  |     | NULL    |                |
| nomProgramme           | varchar(255) | YES  |     | NULL    |                |
| distanceInferieure     | float        | YES  |     | NULL    |                |
| distanceCalculee       | float        | YES  |     | NULL    |                |
| distanceSuperieure     | float        | YES  |     | NULL    |                |
| unite                  | int(4)       | YES  |     | NULL    |                |
| allure                 | int(4)       | YES  |     | NULL    |                |
| heureDepartAller       | datetime     | YES  |     | NULL    |                |
| heureChgtArriveeAller  | datetime     | YES  |     | NULL    |                |
| heureChgtDepartAller   | datetime     | YES  |     | NULL    |                |
| heureArriveeAller      | datetime     | YES  |     | NULL    |                |
| heureDepartRetour      | datetime     | YES  |     | NULL    |                |
| heureChgtArriveeRetour | datetime     | YES  |     | NULL    |                |
| heureChgtDepartRetour  | datetime     | YES  |     | NULL    |                |
| heureArriveeRetour     | datetime     | YES  |     | NULL    |                |
| allerGareDepart        | varchar(255) | YES  |     | NULL    |                |
| allerChangementNb      | varchar(255) | YES  |     | NULL    |                |
| allerGareArrivee       | varchar(255) | YES  |     | NULL    |                |
| itineraire             | text         | YES  |     | NULL    |                |
| retourGareDepart       | varchar(255) | YES  |     | NULL    |                |
| retourChangementNb     | varchar(255) | YES  |     | NULL    |                |
| retourGareArrivee      | varchar(255) | YES  |     | NULL    |                |
| multiDates             | varchar(255) | YES  |     | NULL    |                |
| horairesVerification   | varchar(255) | YES  |     | NULL    |                |
| commentaires           | text         | YES  |     | NULL    |                |
| denivelees             | varchar(1)   | NO   |     | NULL    |                |
| description_denivelees | text         | NO   |     | NULL    |                |
| efface                 | varchar(1)   | YES  |     | NULL    |                |
| syncLocal              | varchar(1)   | YES  |     | NULL    |                |
| syncDistant            | varchar(1)   | YES  |     | NULL    |                |
+------------------------+--------------+------+-----+---------+----------------+
34 rows in set (0,01 sec)
```
