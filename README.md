# Composer

* A l'origine j'ai déclaré que tout namespace commençant par _Jpmena_
  * a ses source sous le répertoire 'src'
  * le reste du namepace est parallèle au chemin relatif vers le fichier
  * ainsi le namespace __Jpmena\Databases\Mysql\Model__ correspond au chemin _src/Databases/Mysql/Model_
* à cette fin __composer.json__ contient :

``` javascript
"autoload": {
        "psr-4": {
            "Jpmena\\": "src/"
        }
    }
```

## ajout de league/csv

* Nous avons besoins de lire efficaement lees fichiers csv pour importer des données en table Mysql
  * En suivant l'exemple _scanner.php_ du livre __modern php__ (chapitre 4 composer)
  * cela donne:

``` bash
jpmena@jpmena-P34 ~/RIF/rifimportations/phpclient (master *=) $ composer require league/csv
You are running composer with xdebug enabled. This has a major impact on runtime performance. See https://getcomposer.org/xdebug
Using version ^8.1 for league/csv
./composer.json has been updated
Loading composer repositories with package information
Updating dependencies (including require-dev)
  - Installing league/csv (8.1.2)
    Loading from cache

Writing lock file
Generating autoload files
#on vérifie qu'il a mis à jour mon fichier composer.json
jpmena@jpmena-P34 ~/RIF/rifimportations/phpclient (master *=) $ cat composer.json 
{
    "autoload": {
        "psr-4": {
            "Jpmena\\": "src/"
        }
    },
    "require": {
        "league/csv": "^8.1"
    }
}

```


# Modifications

## le LOAD FILE REPLACE 

* est remplacé par une suite de commande REPLACE / MYSQL
  * cf. [lien officiel Mysql 5.5](https://dev.mysql.com/doc/refman/5.5/en/replace.html)

# Ajout de monolog pour loguer:

* Remplacer les commandes echo par une trace log !!!
  * _cf. OReilly / Modern PHP chapitre 5_  sous partie Exception ....

``` bash
jpmena@jpmena-P34 ~/RIF/rifimportations/phpclient (master=) $ composer require monolog/monolog
You are running composer with xdebug enabled. This has a major impact on runtime performance. See https://getcomposer.org/xdebug
Using version ^1.22 for monolog/monolog
./composer.json has been updated
Loading composer repositories with package information
Updating dependencies (including require-dev)
  - Installing psr/log (1.0.2)
    Downloading: 100%         

  - Installing monolog/monolog (1.22.0)
    Downloading: 100%         

monolog/monolog suggests installing aws/aws-sdk-php (Allow sending log messages to AWS services like DynamoDB)
monolog/monolog suggests installing doctrine/couchdb (Allow sending log messages to a CouchDB server)
monolog/monolog suggests installing ext-amqp (Allow sending log messages to an AMQP server (1.0+ required))
monolog/monolog suggests installing ext-mongo (Allow sending log messages to a MongoDB server)
monolog/monolog suggests installing graylog2/gelf-php (Allow sending log messages to a GrayLog2 server)
monolog/monolog suggests installing mongodb/mongodb (Allow sending log messages to a MongoDB server via PHP Driver)
monolog/monolog suggests installing php-amqplib/php-amqplib (Allow sending log messages to an AMQP server using php-amqplib)
monolog/monolog suggests installing php-console/php-console (Allow sending log messages to Google Chrome)
monolog/monolog suggests installing rollbar/rollbar (Allow sending log messages to Rollbar)
monolog/monolog suggests installing ruflin/elastica (Allow sending log messages to an Elastic Search server)
monolog/monolog suggests installing sentry/sentry (Allow sending log messages to a Sentry server)
Writing lock file
Generating autoload files
# vérification:
jpmena@jpmena-P34 ~/RIF/rifimportations/phpclient (master *=) $ cat composer.json 
{
    "autoload": {
        "psr-4": {
            "Jpmena\\": "src/"
        }
    },
    "require": {
        "league/csv": "^8.1",
        "monolog/monolog": "^1.22"
    }
}
```

## 

# Il reste

## au 3/12/2016

* __adherents.php__ et __animateurs.php__ OK !!!

### Log OK mais peut être mieux exploitée

* Mais apparemment on peut l'enrichir voir [le site du créateur de Monolog](https://github.com/Seldaek/monolog)
** pourquoi les _[][]_ ???
* faut il envoyer la log par mail ? (cas notamment de result KO)

``` bash
[2016-12-03 08:57:15] adherents.DEBUG: executing mysql request:Importation / mise à jour d'un adherent [] []
[2016-12-03 08:57:15] adherents.DEBUG: executing mysql request:Mise à jour de la table users [] []
[2016-12-03 08:57:15] adherents.DEBUG: Ending transaction with success [] []
```

### sortir certains paramètres dans un fichier _setting.php_

* Je pense à ce qui est commun à tous les _imports/nettoyages_, notamment
  * les coordonnées d'acccès à la BDD
  * le répertoire d'accueil des logs !!!

## au 4/12/2016

* la configuration a été exportée ....

### Log OK mais peut être mieux exploitée

* Mais apparemment on peut l'enrichir voir [le site du créateur de Monolog](https://github.com/Seldaek/monolog)
  * pourquoi les _[][]_ ??? (cf. ci dessus)
* faut il envoyer la log par mail ? (cas notamment de result KO)
  * et supprimer ensuite le fichier ?

### Faire tester par CRON 

* le script de livraison _rifimportations/scripts/gitarchive.sh_ fonctionne enfin
  * il exporte la partie git
  * et il relance composer pour reconstruire la parrtie vendor non exxportée sous github !!!
  * il fait un zip de l'ensemble

``` bash
jpmena@jpmena-P34 ~/RIF/rifimportations/scripts (master *=) $ ./gitarchive.sh 
archive git: /home/jpmena/RIF/tmp/rifimportations_2016-12-04_08:55:11.zip générée pour la branche: master; contenu:
Archive:  /home/jpmena/RIF/tmp/rifimportations_2016-12-04_08:55:11.zip
136b97790497fbecf26be729e9a275fa4d644ed1
  Length      Date    Time    Name
---------  ---------- -----   ----
        0  2016-12-04 08:39   rifimportations/
       28  2016-12-04 08:39   rifimportations/.gitignore
     4767  2016-12-04 08:39   rifimportations/README.md
        0  2016-12-04 08:39   rifimportations/phpclient/
     1510  2016-12-04 08:39   rifimportations/phpclient/adherents.php
     1780  2016-12-04 08:39   rifimportations/phpclient/animateurs.php
      177  2016-12-04 08:39   rifimportations/phpclient/composer.json
        0  2016-12-04 08:39   rifimportations/phpclient/config/
      560  2016-12-04 08:39   rifimportations/phpclient/config/settings.php
      683  2016-12-04 08:39   rifimportations/phpclient/randonnee.php
        0  2016-12-04 08:39   rifimportations/phpclient/src/
        0  2016-12-04 08:39   rifimportations/phpclient/src/Databases/
        0  2016-12-04 08:39   rifimportations/phpclient/src/Databases/Mysql/
        0  2016-12-04 08:39   rifimportations/phpclient/src/Databases/Mysql/Controller/
     2713  2016-12-04 08:39   rifimportations/phpclient/src/Databases/Mysql/Controller/RifImporter.php
        0  2016-12-04 08:39   rifimportations/phpclient/src/Databases/Mysql/Helper/
     1224  2016-12-04 08:39   rifimportations/phpclient/src/Databases/Mysql/Helper/LoggerTrait.php
        0  2016-12-04 08:39   rifimportations/phpclient/src/Databases/Mysql/Model/
     5998  2016-12-04 08:39   rifimportations/phpclient/src/Databases/Mysql/Model/Database.php
        0  2016-12-04 08:39   rifimportations/phpclient/vendor/
      178  2016-12-04 08:39   rifimportations/phpclient/vendor/autoload.php
        0  2016-12-04 08:39   rifimportations/scripts/
      945  2016-12-04 08:39   rifimportations/scripts/gitarchive.sh
---------                     -------
    20563                     23 files
il nous faut ajouter la partie vendor ..
ajout de la partie vendor via composer
You are running composer with xdebug enabled. This has a major impact on runtime performance. See https://getcomposer.org/xdebug
Loading composer repositories with package information
Updating dependencies (including require-dev)
  - Installing psr/log (1.0.2)
    Loading from cache

  - Installing monolog/monolog (1.22.0)
    Loading from cache

  - Installing league/csv (8.1.2)
    Loading from cache

monolog/monolog suggests installing aws/aws-sdk-php (Allow sending log messages to AWS services like DynamoDB)
monolog/monolog suggests installing doctrine/couchdb (Allow sending log messages to a CouchDB server)
monolog/monolog suggests installing ext-amqp (Allow sending log messages to an AMQP server (1.0+ required))
monolog/monolog suggests installing ext-mongo (Allow sending log messages to a MongoDB server)
monolog/monolog suggests installing graylog2/gelf-php (Allow sending log messages to a GrayLog2 server)
monolog/monolog suggests installing mongodb/mongodb (Allow sending log messages to a MongoDB server via PHP Driver)
monolog/monolog suggests installing php-amqplib/php-amqplib (Allow sending log messages to an AMQP server using php-amqplib)
monolog/monolog suggests installing php-console/php-console (Allow sending log messages to Google Chrome)
monolog/monolog suggests installing rollbar/rollbar (Allow sending log messages to Rollbar)
monolog/monolog suggests installing ruflin/elastica (Allow sending log messages to an Elastic Search server)
monolog/monolog suggests installing sentry/sentry (Allow sending log messages to a Sentry server)
Writing lock file
Generating autoload files
archive: /home/jpmena/RIF/tmp/rifimportations_2016-12-04_08:55:11.zip après ajout de la partie composer/vendor
```
 