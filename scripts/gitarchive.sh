#!/bin/bash

init_repertoire=$(pwd)
cd ..
mon_repertoire=$(pwd)
nom_module=$(basename $mon_repertoire)
branche=$(git branch | sed -n '/\* /s///p')
git archive --format zip --prefix="${nom_module}/" --output ../"${nom_module}.zip" $branche
echo "archive: ${nom_module}.zip générée (dans le répertoire parent) pour la branche: ${branche}; contenu:"
cd ..
unzip "${nom_module}.zip" && rm "${nom_module}.zip" && cd ${nom_module}/phpclient

composer install

cd ../..

zip -r "${nom_module}.zip" ${nom_module}

cd $init_repertoire