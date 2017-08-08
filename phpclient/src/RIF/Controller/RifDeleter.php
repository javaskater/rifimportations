<?php

namespace Jpmena\RIF\Mysql\Controller;

use \Jpmena\RIF\Helper\LoggerTrait;
use \Jpmena\RIF\Model\Database;

/**
 * Description of RifImporterFileSystem
 *
 * @author jpmena
 */
class RifDeleter {

    private $myDatabaseModel;

    use LoggerTrait;


    /**
     * Get nids of the nodes to delete.
     *
     * @param array $roles
     *   Array of roles.
     *
     * @return array
     *   Array of nids of nodes to delete.
     */
    public function deleteAttached($parametres_imports_array) {
        foreach ($parametres_imports_array as $parametres_imports) {
            //var_dump($parametres_imports);
            if (array_key_exists ( 'fichier_csv' , $parametres_imports ) && file_exists($parametres_imports['fichier_csv'])) {
                $csv = \League\Csv\Reader::createFromPath($parametres_imports['fichier_csv']);
                $firstline = TRUE;
                foreach ($csv as $csvRow) {
                    if (!$firstline) { //La première ligne est celle des titres
                        $bindkeys_csvpos = $parametres_imports['csv_to_bind_parameters'];
                        $bindParameters = [];
                        foreach ($bindkeys_csvpos as $bindkey => $csvpos) {
                            $bindParameters[$bindkey] =  $csvRow[$csvpos];
                        }
                        //print_r($bindParameters);
                        $sqlCommmandText = $parametres_imports['sql_command_text'];
                        $log_text = "++csv:".$parametres_imports['log_text'];
                        $this->myDatabaseModel->prepareRequetePourTransaction($sqlCommmandText, $bindParameters, $log_text);
                    } else {
                        $firstline = FALSE;
                    }
                }
            } else {
                $bindParameters = $parametres_imports['bind_parameters'];
                $sqlCommmandText = $parametres_imports['sql_command_text'];
                $log_text = $parametres_imports['log_text'];
                $this->myDatabaseModel->prepareRequetePourTransaction($sqlCommmandText, $bindParameters, $log_text);
            }
        }
        $resultat_transaction_array = $this->myDatabaseModel->executeTransaction();
        return [
            'parametres_imports_array' => $parametres_imports_array,
            'resultat_transaction' => $resultat_transaction_array
        ];
    }

/*    def nettoieRandonnees(path_a_effacer):
	nomColCle="Rj_CleJour"
	repertoireUpload=os.path.join(os.path.dirname(__file__),"../extranet/app/webroot/upload/randonnees")
	indiceCle=-1
	f_to_del=open(os.path.abspath(os.path.join(os.path.dirname(__file__),path_a_effacer)),'r')
	to_del_csv=csv.reader(f_to_del)
	entete=None
	for ligne_a_effacer in to_del_csv:
		if entete is None:
			entete=ligne_a_effacer
			colnum=0
			for col in entete:
				if col==nomColCle:
					indiceCle=colnum
					break
				colnum += 1
		elif indiceCle >= 0 and len(ligne_a_effacer) > indiceCle:œ
			requete="delete from commentaires where randonnee_cle=%s" %(rando_cle)
			executesql(requete)
			#finalement suppression de la randonnee elle meme
			requete="delete from randonnees where cle=%s" %(rando_cle)
			executesql(requete)
		
def nettoieCollectives(path_a_effacer):
	nomColCle="Co_CleCol"
	indiceCle=-1
	repertoireUpload=os.path.join(os.path.dirname(__file__),"../extranet/app/webroot/upload/collectives")
	f_to_del=open(os.path.abspath(os.path.join(os.path.dirname(__file__),path_a_effacer)),'r')
	to_del_csv=csv.reader(f_to_del)
	entete=None
	for ligne_a_effacer in to_del_csv:
		if entete is None:
			entete=ligne_a_effacer
			colnum=0
			for col in entete:
				if col==nomColCle:
					indiceCle=colnum
					break
				colnum += 1
		elif indiceCle >= 0 and len(ligne_a_effacer) > indiceCle:
			cle_coll=ligne_a_effacer[indiceCle]
			requete="select id,fichier from fichiers where collective_cle=%s" %(cle_coll)
			fichiersASupprimer=executesql(requete)
			if fichiersASupprimer is not None:
				for fichier in fichiersASupprimer:
			        #detruit fichier sur le disque dur
					path_to_del=os.path.join(repertoireUpload,fichier[1])
					if os.path.exists(path_to_del):
						print "suppression du fichier %s" %(path_to_del)
						os.unlink(path_to_del)
			        #detruit l'enregitrement du fichier dans la base
					requete="delete from fichiers where id=%s" %(fichier[0])
					executesql(requete)
			#suppression des commentaires correspondant a la randonnee
			requete="delete from commentaires where collective_cle=%s" %(cle_coll)
			executesql(requete)
			#finalement suppression de la randonnee elle meme
			requete="delete from collectives where Co_CleCol=%s" %(cle_coll)
			executesql(requete)

            def moispair():
    mois=date.today().month
    if mois%2==0:
		print 'mois pair'
		return 1
    else:
		print 'mois impair'
		return 0
if moispair(): 
	def datelimite():
		aujourdhui=date.today()
		premier=aujourdhui.replace(day=1)
		return premier.isoformat()
else :
	def datelimite():
		aujourdhui=date.today()
		premier=aujourdhui.replace(day=1)
		premier=premier.replace(month=date.today().month - 1)
		return premier.isoformat()

		

repertoireUpload="/homez.25/rifrando/extranet/app/webroot/upload/collectives"

requete= "select Co_CleCol from collectives where Co_DateDepart < '"+datelimite()+"'"
collectivesanciennes=sql.executesqlselect(requete)
if len(collectivesanciennes) > 0:
    for id_collective in collectivesanciennes:
        id_str=str(id_collective[0])
        for f in os.listdir(repertoireUpload):
            #detruit fichier sur le disque dur
            path=repertoireUpload+"/"+f
            if (f.startswith(id_str) and os.path.exists(path)):
                try:
                    os.unlink(path)
                except OSError as e:
                    print u'impossible de supprimer %s cause:%d-%s' %(e.filename,e.errno,e.strerror)
        #detruit l'enregitrement du fichier dans la base
        requete="select id,fichier from fichiers where collective_cle="+str(id_collective[0])
        fichiersASupprimer=sql.executesqlselect(requete)
        for fichier in fichiersASupprimer:
            requete="delete from fichiers where id="+str(fichier[0])
            sql.executesql(requete)
        #suppression des commentaires correspondant a la collective
        requete="delete from commentaires where collective_cle="+str(id_collective[0])
        sql.executesql(requete)
        #finalement suppression de la collective elle meme
        requete="delete from collectives where Co_CleCol="+str(id_collective[0])
        sql.executesql(requete)
else:
    print u'aucune collective ancienne abandon'


#!/usr/bin/python
from datetime import date
import os
import anim_sql as sql

#le planificateur OVH repete la tache tout les mois et on ne veut supprimer que les fichiers anterieurs au 1er du mois pair precedent

def moispair():
    mois=date.today().month
    if mois%2==0:
		print 'mois pair'
		return 1
    else:
		print 'mois impair'
		return 0
if moispair(): 
	def datelimite():
		aujourdhui=date.today()
		premier=aujourdhui.replace(day=1)
		return premier.isoformat()
else :
	def datelimite():
		aujourdhui=date.today()
		premier=aujourdhui.replace(day=1)
		premier=premier.replace(month=date.today().month - 1)
		return premier.isoformat()

		

repertoireUpload="/homez.25/rifrando/extranet/app/webroot/upload/randonnees"

requete= "select cle from randonnees where date < '"+datelimite()+"'"
randossanciennes=sql.executesqlselect(requete)
if len(randossanciennes) > 0:
    for id_rando in randossanciennes:
        id_str=str(id_rando[0])
        requete="select id,fichier from fichiers where randonnee_cle="+id_str
        fichiersASupprimer=sql.executesqlselect(requete)
        for fichier in fichiersASupprimer:
            requete="delete from fichiers where id="+str(fichier[0])
            sql.executesql(requete)
        for f in os.listdir(repertoireUpload):
            #detruit fichier sur le disque dur
            path=repertoireUpload+"/"+f
            if (f.startswith(id_str,0) and os.path.exists(path)):
                try:
                    os.unlink(path)
                except OSError as e:
                    print u'impossible de supprimer %s cause:%d-%s' %(e.filename,e.errno,e.strerror)
        #suppression des commentaires correspondant a la randonnee
        requete="delete from commentaires where randonnee_cle="+str(id_rando[0])
        sql.executesql(requete)
        #finalement suppression de la randonnee elle meme
        requete="delete from randonnees where cle="+str(id_rando[0])
        sql.executesql(requete)
else:
    print u'aucune randonnee ancienne abandon'

}*/