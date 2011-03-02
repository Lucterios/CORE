<?
//
//  This file is part of Lucterios.
//
//  Lucterios is free software; you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation; either version 2 of the License, or
//  (at your option) any later version.
//
//  Lucterios is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with Lucterios; if not, write to the Free Software
//  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
//
//	Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
//

require_once("CORE/Lucterios_Error.inc.php");

list($usec, $sec) = split(" ", microtime());
// avant toute chose, on stipule qu'on retourne du text/plain
header("http-content: text/plain");

// on initialise la reponse
$REPONSE = "<REPONSES>";

// config statique globale de l'applis serveur
require_once("conf/cnf.inc.php");
require_once("CORE/log.inc.php");

// connexion ?a base de donn?
require_once("CORE/dbcnx.inc.php");

// fonctions gestionnaires de sessions (ouverture, test de validit?
require_once("CORE/session4.0.inc.php");

// d?upage de l'XML de requette
require_once("CORE/XMLparse.inc.php");
$REQUETTE = "";
$nourlencode=array_key_exists("nourlencode", $GLOBAL);
if(array_key_exists("XMLinput", $GLOBAL)) {
	$p = new COREParser();
	$xml_input=$GLOBAL["XMLinput"];
	if ($nourlencode || (substr($xml_input,0,3)!='%3C'))
		$XMLinput=str_replace(array("\"","\'"),"'",$xml_input);
	else
		$XMLinput=str_replace(array("\"","\'"),"'",urldecode($xml_input));
	// ajout du log
	logRequette($XMLinput);

	$p->setInput($XMLinput);
	$p->parse();
	$REQUETTE = $p->getResult();
}

// gestion des exceptions
require_once('CORE/xfer_exception.inc.php');
require_once('CORE/xfer.inc.php');

// gestion des droits
require_once('CORE/rights.inc.php');

// gestion de l'authentification
// extraction du login pass ou de la ses du tableau REQUETTE
$found = false;
$lesRequettes = array();

if("xmlelement" == strtolower(get_class($REQUETTE))) $lesRequettes = $REQUETTE->getChildsByTagName("REQUETE");

foreach($lesRequettes as $req) {
	if($found) continue;

	// recup de l'extension et de l'action
	$extension = $req->getAttributeValue("EXTENSION");
	$action = $req->getAttributeValue("ACTION");
	if($extension == "common" && $action == "authentification") {
		$found = true;
		$paramTable = $req->getChildsByTagName("PARAM");
		foreach($paramTable as $par) {
			$GLOBAL[$par->getAttributeValue("NAME")] = $par->getCData();
		}
	}
}

require_once("CORE/connection.inc.php");

if($IS_CONNECTED) {
	// on est maintenant connect? la base de donn?et authentifi?soit par ses soit par login/pass)
	try {
		// on boucle sur l'ensemble des actions demand?
        require_once("CORE/BoucleReponse.inc.php");
        $REPONSE.= BoucleReponse($lesRequettes);
	} catch (Exception $e) {              // Devrait �tre attrap�e
		require_once "CORE/xfer_exception.inc.php";
  		$Xfer_erro=new Xfer_Container_Exception("CORE","coreIndex");
  		$Xfer_erro->setData($e);
  		$REPONSE.= $Xfer_erro->getReponseXML();
	}
}
// les actions sont execut?, on exporte l'ensemble des reponses en XML
$REPONSE.="</REPONSES>";

list($usec2, $sec2) = split(" ", microtime());
$t = ($sec2-$sec)+(($usec2-$usec)/10);
logAutre("temps total serveur: $t");
// log de la reponse
logReponse($REPONSE);

if ($nourlencode) {
	print utf8_encode($REPONSE);
}
else {
	print urlencode(utf8_encode($REPONSE));
}
?>
