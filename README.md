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
