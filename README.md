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
```V

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

* Pour la reprise des scripts Python concernant Adhérents et Animateurs voir [La page Adhérents](docs/ADHERENTS.md)
* Pour la reprise des scripts Python concernant les Randonnées voir [La page Randonnées](docs/RANDOS.md)
* Pour la reprise des scripts Python concernant les collectives voir [La page Collectives](docs/COLLECTIVES.md)
