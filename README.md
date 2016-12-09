# Installation et Usage

## Installation

* Dézipper sur le serveur OVH à l'endroit que vous aurez choisi l'archive fournie
  * (Première archive fournie __rifimportations_2016-12-04_214002.zip__)

## Paramétrage

* éditer le fichier __rifimportations/phpclient/config/settings.php__ (chemin relatif à l'archive)
* on édite en particulier le tableau __$mysql_settings__ qui contient les paramètres relatifs à la base de données :
  * dans le cas ci dessous, 
    * le serveur est _localhost_
    * le nom de la base est _rif_
    * l'utilisateur correspondant de la base est _rif_ et son mot de passe est _rif_

``` php
$mysql_settings = [
    'host' => '127.0.0.1',
    'port' => '3306',
    'name' => 'rif',
    'username' => 'rif',
    'password' => 'rif',
    'charset' => 'utf8'
];
```

* on édite également le tableau __$chemins_fichiers__  qui recenseles répertoires où vous souhaitez voir apparaître:
  * Le répertoire où se trouveront les Logs relatives aux traitements: cf. clé *repertoire_log* du tableau en question cf.  ci dessous
  * Le répertoire où se trouveront les données au format csv: cf. clé *repertoire_csv* du tableau en question cf.  ci dessous

``` php
$chemins_fichiers = [
    'repertoire_csv' => '/home/jpmena/RIF/importations',
    'repertoire_log' => '/home/jpmena/RIF',
];
```

* un autre parapètre intéressants est le nombre de fichiers de logs, d'un même type de traitement (adhérents, animateurs) que l'on souhaite garder
  * ci dessous on gardera les 20 derniers fichiers de log les plus récents du traitement des adhérents et du traitement des animateurs

``` php
$other_settings = [
    'log_history_depth' => 20 //nombre de fichiers logs à garder
]
```

# La Conception

## ce que fait _adherents.php_

* toutes les tâches ci dessous, sont réalisées dans une même [transation PDO](http://php.net/manual/fr/pdo.transactions.php)
 
### il _Recharge de la table des adhérents à partir du fichier csv correspondant_

* chargement de la table __adherents__ à partir du fichier _adherents.csv_ et de la commande Mysql REPLACE expliquée quelques chapitres plus bas
  * à caque ligne csv la commande mysql passée est:
``` sql
REPLACE adherents set numero = :numero, codepostal = :codepostal, expiration = :expiration
```

### il _purge de la table des adherents pour les adhérents dont la date d'expiration est passée depuis plus 120 jours_

* 120 est un paramètre modifiable posé au début du script _adherents.php_
* pour cela il passe la requête sql suivante:
``` sql
delete from adherents where expiration < (TODAY - 120)
```

### Il met _à jour de la table users suite à la recharge des adhérents_

* c'est une commande sql reprise telle qu'elle du script python correspondant :
``` sql
update users, adherents set users.expiration = adherents.expiration where users.username = adherents.numero
```

### il _purge de la table des adherents pour les adhérents dont la date d'expiration est passée depuis plus 120 jours_

* 120 est un paramètre modifiable posé au début du script _adherents.php_
* pour cela il passe la requête sql suivante:
``` sql
delete from users where expiration < (TODAY - 120)
```

## ce que fait _animateurs.php_ :

* toutes les tâches ci dessous, sont réalisées dans une même [transation PDO](http://php.net/manual/fr/pdo.transactions.php)

### Il prépare _la table users pour les animteurs avant recharge csv des animateurs_

* c'est une commande sql reprise telle qu'elle du script python correspondant :
``` sql
update users set role = NULL where role = 'animateur'
```
 
### il _recharge de la table des animateurs à partir du fichier csv correspondant!_

* chargement de la table __animateurs__ à partir du fichier _animateurs.csv_ et de la commande Mysql REPLACE expliquée quelques chapitres plus bas
  * à caque ligne csv la commande mysql passée est:
    * on note que l'on remet ici à vide les numéros de téléphone de l'animateur
``` sql
REPLACE animateurs SET numero = :numero, surnom = :surnom, Tel_domicile = '', Tel_travail = '', Tel_mobile = ''
```

### il _met à jour les numéros de téléphone des animateurs (dans la table des animateurs) à partir du fichier adherents.csv_

* Mise à jour de la table __animateurs__ à partir du fichier _adherents.csv_ 
  * à caque ligne csv la commande mysql passée est:
``` sql
UPDATE animateurs set tel_domicile = :tel_domicile, tel_travail = :tel_travail, tel_mobile = :tel_mobile WHERE numero = :numero;
```

### il _Mise à jour de la table des animateurs à partir des adhérents actuels à savoir ceux non expirés - le script adherents.php a été exécuté auparavant-_

* pour cela il passe la requête sql suivante:
``` sql
delete from animateurs where numero not in (select numero from adherents)
```

### il met _à jour de la table users pour les animateurs suite à recharge de la table des animateurs

* c'est une commande sql reprise telle qu'elle du script python correspondant :
``` sql
update users set role = 'animateur' where `username` in (select numero from animateurs) and (role <> 'admin' OR role is null)
```

## Usage de Composer et des NameSpace du PHP objet

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

### ajout de league/csv

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


## Modifications

### le LOAD FILE REPLACE 

* est remplacé par une suite de commande REPLACE / MYSQL
  * cf. [lien officiel Mysql 5.5](https://dev.mysql.com/doc/refman/5.5/en/replace.html)
  * cette commannde fait :
    * un _INSERT_ si la clé primaire n'existe pas
    * un _UPDATE_ si la clé primaire existe déjà... 

## Ajout de monolog pour loguer:

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
 

# Programmation des traitements

* programmer via la planificateur OVH l'exécution avec le langage __php 5.6__ des fichiers:
  * _rifimportations/phpclient/adherents.php_ pour l'importations et la mise à jour des adhérents
  * _rifimportations/phpclient/animateurs.php_ pour l'importations et la mise à jour des animateurs

## Le CRON sur machine virtuelle Debian / PHP 5.6 (VirtualBox)

* Finalement le test est > 0 à

``` bash
jpmena@rifovh:~$ crontab -l
# Edit this file to introduce tasks to be run by cron.
# 
# Each task to run has to be defined through a single line
# indicating with different fields when the task will be run
# and what command to run for the task
# 
# To define the time you can provide concrete values for
# minute (m), hour (h), day of month (dom), month (mon),
# and day of week (dow) or use '*' in these fields (for 'any').# 
# Notice that tasks will be started based on the cron's system
# daemon's notion of time and timezones.
# 
# Output of the crontab jobs (including errors) is sent through
# email to the user the crontab file belongs to (unless redirected).
# 
# For example, you can run a backup of all your user accounts
# at 5 a.m every week with:
# 0 5 * * 1 tar -zcf /var/backups/home.tgz /home/
# 
# For more information see the manual pages of crontab(5) and cron(8)
# 
# m h  dom mon dow   command
35 10 * * * php /home/jpmena/RIF/rifimportations/phpclient/adherents.php
45 10 * * * php /home/jpmena/RIF/rifimportations/phpclient/animateurs.php
```

* on retrouve bien les logs avec succès sur la machine virtuelle:

``` bash
jpmena@rifovh:~$ tail -5 RIF/adherents_2016-12-04_10\:35\:01.log 
[2016-12-04 10:35:01] adherents.DEBUG: executing mysql request:Importation / mise à jour d'un adherent [] []
[2016-12-04 10:35:01] adherents.DEBUG: executing mysql request:Importation / mise à jour d'un adherent [] []
[2016-12-04 10:35:01] adherents.DEBUG: executing mysql request:Importation / mise à jour d'un adherent [] []
[2016-12-04 10:35:01] adherents.DEBUG: executing mysql request:Mise à jour de la table users [] []
[2016-12-04 10:35:01] adherents.DEBUG: Ending transaction with success [] []
jpmena@rifovh:~$ tail -5 RIF/animateurs_2016-12-04_10\:45\:01.log 
[2016-12-04 10:45:01] animateurs.DEBUG: executing mysql request:Mise à jour d'un animateur [] []
[2016-12-04 10:45:01] animateurs.DEBUG: executing mysql request:Mise à jour d'un animateur [] []
[2016-12-04 10:45:01] animateurs.DEBUG: executing mysql request:Mise à jour d'un animateur [] []
[2016-12-04 10:45:01] animateurs.DEBUG: executing mysql request:Mise à jour de la table users pour les animteurs suite à import csv [] []
[2016-12-04 10:45:01] animateurs.DEBUG: Ending transaction with success [] []
```

## Le cron sur l'hébergement mutualisé 1AND1

* Attention à bien utiliser la version _cliente_ et non _cgi_ de __php 5.5__

``` bash
#tous les php présents
(uiserver):u72756193:~/livrables_jpm/rifimportations/phpclient/config$ whereis php
php: /usr/bin/php /usr/bin/php4.4 /usr/bin/php5.2 /usr/bin/php5.4 /usr/bin/php5.5 
/usr/bin/php5.2-cli /usr/bin/php5.4-cli /usr/bin/php5.5-cli /usr/bin/php4.4-cli /usr/lib/php5.5 
/usr/lib/php5.4 /usr/lib/php4.4 /usr/lib/php5.2 /usr/local/bin/php /usr/local/bin/php4.4 /usr/local/bin/php5.2 
/usr/local/bin/php5.4 /usr/local/bin/php5.5 /usr/include/php5.5 /usr/include/php5.2 /usr/include/php5.4 /usr/include/php4.4 /usr/share/php
#en php 5.5 la version par défaut est la version cgi
(uiserver):u72756193:~/livrables_jpm/rifimportations/phpclient/config$ /usr/bin/php5.5 --version
PHP 5.5.38 (cgi-fcgi) (built: Nov 16 2016 08:07:34)
Copyright (c) 1997-2015 The PHP Group
Zend Engine v2.5.0, Copyright (c) 1998-2015 Zend Technologies
#pour nos scripts clients on a besoin de la version cliente
(uiserver):u72756193:~/livrables_jpm/rifimportations/phpclient/config$ /usr/bin/php5.5-cli --version
PHP 5.5.38 (cli) (built: Nov 16 2016 08:07:24) 
Copyright (c) 1997-2015 The PHP Group
Zend Engine v2.5.0, Copyright (c) 1998-2015 Zend Technologies
```


* Cela donne pour notre _CRONTAB_
  * le script _adherents.php_ sera exécuté tous les matins à _4.30 a.m._
  * le script _animateurs.php_ sera exécuté tous les matins à _5.30 a.m._

``` bash
(uiserver):u72756193:~/livrables_jpm/rifimportations/phpclient/config$ crontab -l | tail -5

#tests rif adherents
30 04 * * * /usr/bin/php5.5-cli /kunden/homepages/21/d462702613/htdocs/livrables_jpm/rifimportations/phpclient/adherents.php
#tests rif animateurs
30 05 * * * /usr/bin/php5.5-cli /kunden/homepages/21/d462702613/htdocs/livrables_jpm/rifimportations/phpclient/animateurs.php
```

