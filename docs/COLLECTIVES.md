# tout sur la mise à jour des randonnées de jour en PHP

## TODO

* Mettre à jour les collectives _commande [REPLACE](https://dev.mysql.com/doc/refman/5.7/en/replace.html)_ (fichier _collectives.csv_)
* supprimer les anciennes randonnées à effacer (fichier _collectives.eff.csv_)
* supprimer les anciennes randonnées du programme précédent (reprendre le calcul de la date minimale avec mois pair/impar)

### NB pour chaque suppression

* supprimer les commentaires associés à une randonnée (si existent)
* supprimer le fichier joint attaché à une randonnée (si existe)

## Ce que fait _collectives.php_

* Les colonnes à insérer/remplacer par importation CSV ....
* _commande [REPLACE](https://dev.mysql.com/doc/refman/5.7/en/replace.html)_ (fichier _collectives.csv_)

```
mysql> desc collectives;
+-----------------------------+--------------+------+-----+---------+----------------+
| Field                       | Type         | Null | Key | Default | Extra          |
+-----------------------------+--------------+------+-----+---------+----------------+
| Co_Aj                       | tinyint(1)   | YES  |     | NULL    |                |
| Co_Allure                   | int(4)       | YES  |     | NULL    |                |
| Co_Annee                    | int(4)       | YES  |     | NULL    |                |
| Co_Arrhes                   | float        | YES  |     | NULL    |                |
| Co_Billet                   | varchar(255) | YES  |     | NULL    |                |
| Co_Bivouac                  | varchar(1)   | YES  |     | NULL    |                |
| Co_Budget                   | float        | YES  |     | NULL    |                |
| Co_Budget1                  | float        | YES  |     | NULL    |                |
| Co_Budget2                  | float        | YES  |     | NULL    |                |
| Co_Budget3                  | float        | YES  |     | NULL    |                |
| Co_Camping                  | varchar(1)   | YES  |     | NULL    |                |
| Co_ChambreDHotes            | varchar(1)   | YES  |     | NULL    |                |
| Co_ChgtAllerWe              | varchar(255) | YES  |     | NULL    |                |
| Co_ChgtRetourWe             | varchar(255) | YES  |     | NULL    |                |
| Co_CleCol                   | int(11)      | NO   | PRI | NULL    | auto_increment |
| Co_DateArrivee              | datetime     | YES  |     | NULL    |                |
| Co_DateClotureBis           | datetime     | YES  |     | NULL    |                |
| Co_DateDepart               | datetime     | YES  |     | NULL    |                |
| Co_DateDisponibilite        | datetime     | YES  |     | NULL    |                |
| Co_DateDebutInsc            | datetime     | YES  |     | NULL    |                |
| Co_DateFinInsc              | datetime     | YES  |     | NULL    |                |
| Co_Deniveles                | varchar(1)   | YES  |     | NULL    |                |
| Co_Dispo                    | float        | YES  |     | NULL    |                |
| Co_Distance                 | float        | YES  |     | NULL    |                |
| Co_Distance2                | float        | YES  |     | NULL    |                |
| Co_Distance3                | float        | YES  |     | NULL    |                |
| Co_Efface                   | varchar(1)   | YES  |     | NULL    |                |
| Co_Etat                     | varchar(255) | YES  |     | NULL    |                |
| Co_GareArriveeAllerWe       | varchar(255) | YES  |     | NULL    |                |
| Co_GareArriveeRetourWe      | varchar(255) | YES  |     | NULL    |                |
| Co_GareDepartRetourWe       | varchar(255) | YES  |     | NULL    |                |
| Co_Gites                    | varchar(1)   | YES  |     | NULL    |                |
| Co_Hebergement              | float        | YES  |     | NULL    |                |
| Co_HeureArriveeAllerWE      | datetime     | YES  |     | NULL    |                |
| Co_HeureArriveeGareRetWE    | datetime     | YES  |     | NULL    |                |
| Co_HeureChgtAllerArriveeWe  | datetime     | YES  |     | NULL    |                |
| Co_HeureChgtAllerDepartWe   | datetime     | YES  |     | NULL    |                |
| Co_HeureChgtRetourArriveeWe | datetime     | YES  |     | NULL    |                |
| Co_HeureChgtRetourDepartWe  | datetime     | YES  |     | NULL    |                |
| Co_HeureDep                 | datetime     | YES  |     | NULL    |                |
| Co_HeureRDV                 | datetime     | YES  |     | NULL    |                |
| Co_HeureRetourGareDepWE     | varchar(255) | YES  |     | NULL    |                |
| Co_Heures                   | float        | YES  |     | NULL    |                |
| Co_HorairesV                | varchar(1)   | YES  |     | NULL    |                |
| Co_Hotel                    | varchar(1)   | YES  |     | NULL    |                |
| Co_Inscription              | varchar(1)   | YES  |     | NULL    |                |
| Co_Inscrits                 | int(11)      | YES  |     | NULL    |                |
| Co_Itinerant                | varchar(1)   | YES  |     | NULL    |                |
| Co_ListeAttente             | int(11)      | YES  |     | NULL    |                |
| Co_Materiel                 | varchar(255) | YES  |     | NULL    |                |
| Co_NOrdre                   | int(11)      | YES  |     | NULL    |                |
| Co_NSemaine                 | int(11)      | YES  |     | NULL    |                |
| Co_NbJours                  | int(11)      | YES  |     | NULL    |                |
| Co_NbMax                    | int(11)      | YES  |     | NULL    |                |
| Co_NbMaxBis                 | int(11)      | YES  |     | NULL    |                |
| Co_NbMin                    | int(11)      | YES  |     | NULL    |                |
| Co_NbMinBis                 | int(11)      | YES  |     | NULL    |                |
| Co_NbVoyageurs              | int(11)      | YES  |     | NULL    |                |
| Co_Nom                      | varchar(255) | YES  |     | NULL    |                |
| Co_NumTrain                 | varchar(255) | YES  |     | NULL    |                |
| Co_Observation              | varchar(255) | YES  |     | NULL    |                |
| Co_PointFixe                | varchar(1)   | YES  |     | NULL    |                |
| Co_Portage                  | varchar(1)   | YES  |     | NULL    |                |
| Co_PreInscript              | varchar(255) | YES  |     | NULL    |                |
| Co_Prestataire              | float        | YES  |     | NULL    |                |
| Co_PrixAAR                  | float        | YES  |     | NULL    |                |
| Co_PrixSAR                  | float        | YES  |     | NULL    |                |
| Co_Projet                   | varchar(1)   | YES  |     | NULL    |                |
| Co_RDV                      | varchar(255) | YES  |     | NULL    |                |
| Co_RVGareDepart             | varchar(1)   | YES  |     | NULL    |                |
| Co_Refuge                   | varchar(1)   | YES  |     | NULL    |                |
| Co_Relu                     | varchar(1)   | YES  |     | NULL    |                |
| Co_Saisi                    | varchar(1)   | YES  |     | NULL    |                |
| Co_Texte                    | text         | YES  |     | NULL    |                |
| Co_Titre                    | varchar(255) | YES  |     | NULL    |                |
| Co_Transport                | float        | YES  |     | NULL    |                |
| Co_TypeDep                  | varchar(255) | YES  |     | NULL    |                |
| Co_WE_animateurs            | varchar(1)   | YES  |     | NULL    |                |
| fichier                     | varchar(255) | YES  |     | NULL    |                |
+-----------------------------+--------------+------+-----+---------+----------------+
79 rows in set (0,01 sec)
```
