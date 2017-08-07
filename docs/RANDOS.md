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

# Avancées

## le 05/08/2017 :

* Malgré les SQL

```sql
REPLACE randonnees set cle = :cle, date = :date, typeRando = :typeRando, nbParticipants = :nbParticipants, titre = :titre, nomProgramme = :nomProgramme, distanceInferieure = CAST(:distanceInferieure AS DECIMAL(1,0)), distanceCalculee = CAST(:distanceCalculee AS DECIMAL(1,0)), distanceSuperieure = CAST(:distanceSuperieure AS DECIMAL(1,0)), unite =:unite, allure =:allure, 
heureDepartAller = STR_TO_DATE(:heureDepartAller, '%Y/%m/%d %h:%i:%s'), heureChgtArriveeAller = STR_TO_DATE(:heureChgtArriveeAller, '%Y/%m/%d %h:%i:%s'), heureChgtDepartAller = STR_TO_DATE(:heureChgtDepartAller, '%Y/%m/%d %h:%i:%s'), heureArriveeAller = STR_TO_DATE(:heureArriveeAller, '%Y/%m/%d %h:%i:%s'), heureDepartRetour = STR_TO_DATE(:heureDepartRetour, '%Y/%m/%d %h:%i:%s'), 
heureChgtArriveeRetour = STR_TO_DATE(:heureChgtArriveeRetour, '%Y/%m/%d %h:%i:%s'), heureChgtDepartRetour = STR_TO_DATE(:heureChgtDepartRetour, '%Y/%m/%d %h:%i:%s'), heureArriveeRetour = STR_TO_DATE(:heureArriveeRetour, '%Y/%m/%d %h:%i:%s'),
allerGareDepart =:allerGareDepart, allerChangementNb =:allerChangementNb, allerGareArrivee = :allerGareArrivee, itineraire = :itineraire, retourGareDepart = :retourGareDepart,
retourChangementNb = :retourChangementNb, retourGareArrivee = :retourGareArrivee, multiDates = :multiDates, horairesVerification = :horairesVerification, commentaires = :commentaires, efface = :efface, syncLocal = :syncLocal, syncDistant = :syncDistant
```

* J'ai encore dans les logs :
```bash
jpmena@jpmena-P34:~/RIF$ cat randonnees_2017-08-05_180016.log
[2017-08-05 18:00:23] randonnees.DEBUG: Starting transaction [] []
[2017-08-05 18:00:23] randonnees.DEBUG: executing mysql request:++csv:Recharge de la table des randonnées de jour à partir du fichier csv correspondant [] []
[2017-08-05 18:00:32] randonnees.ERROR: Ending transaction with failure; code: 22007,message: SQLSTATE[22007]: Invalid datetime format: 1292 Truncated incorrect DECIMAL value: '' [] []
```

### Ce qui paraît curieux:

* Etre obligé de mettre au format string dans le PDO
  * ligne 


```bash
jpmena@jpmena-P34:~/RIF$ cat randonnees_2017-08-05_180016.log
[2017-08-05 18:00:23] randonnees.DEBUG: Starting transaction [] []
[2017-08-05 18:00:23] randonnees.DEBUG: executing mysql request:++csv:Recharge de la table des randonnées de jour à partir du fichier csv correspondant [] []
[2017-08-05 18:00:32] randonnees.ERROR: Ending transaction with failure; code: 22007,message: SQLSTATE[22007]: Invalid datetime format: 1292 Truncated incorrect DECIMAL value: '' [] []
```