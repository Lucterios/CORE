<?
/******************************************************************************/
/* Fichier cnf.inc.php
/* fichier contenant toutes les variables de configuration de l'application
/******************************************************************************/

// recup des variables en get et post
$GLOBAL = array_merge($_POST, $_GET, $_COOKIE, $_ENV, $_SERVER);

// configuration base de données
require_once("conf.db.php");

// time out de session
$timeOut = 30;

/////////////////////////////////////////////////////////////////////////////////////////////////////
///////   PARTIE A NE PAS MODIFIER SANS ETRE SUR DE SOI!   AUTO CONFIG!     /////////////////////////

$pathSeparator = DIRECTORY_SEPARATOR;

$tmpPath = $_SERVER["DOCUMENT_ROOT"].$pathSeparator."tmp".$pathSeparator;

// si les repertoires n'existent pas, on les crée
if(!is_dir($tmpPath)) mkdir($tmpPath, 0777);
if(!is_dir("tmp".$pathSeparator)) mkdir("tmp".$pathSeparator, 0777);
if(!is_dir('usr')) mkdir('usr', 0777);

/////////////////////////////////////////////////////////////////////////////////////////////////////

?>
