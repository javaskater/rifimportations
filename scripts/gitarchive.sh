#!/bin/bash

init_repertoire=$(pwd)
projet_repertoire=$(dirname $init_repertoire)
client_repertoire=$projet_repertoire/phpclient
archive_repertoire=$(dirname $projet_repertoire)/tmp
#echo "$archive_repertoire"
nom_module=$(basename $projet_repertoire)
nom_archive=${nom_module}_$(date +%Y-%m-%d_%H:%M:%S).zip
archive_abs_path=$archive_repertoire/$nom_archive
#echo "$archive_abs_path"

if [ -d "$archive_repertoire" ]
then
  rm -rf "$archive_repertoire"
fi

mkdir -p $archive_repertoire

cd $projet_repertoire

branche=$(git branch | sed -n '/\* /s///p')
#echo $branche

git archive --format zip --prefix="${nom_module}/" --output $archive_abs_path $branche
echo "archive git: $archive_abs_path générée pour la branche: ${branche}; contenu:"
unzip -l "$archive_abs_path"
echo "il nous faut ajouter la partie vendor .."
cd $archive_repertoire
unzip -qq $nom_archive && rm $nom_archive && cd ${nom_module}/phpclient

#echo $(pwd)

echo "ajout de la partie vendor via composer"
composer install

cd $archive_repertoire

zip -rqq $nom_archive ${nom_module}

echo "archive: $archive_abs_path après ajout de la partie composer/vendor"

#unzip -l $nom_archive

#cd $init_repertoire