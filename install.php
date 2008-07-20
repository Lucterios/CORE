<?php
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
header('Content-Type: text/html; charset=ISO-8859-1');

$appli_dir="./applis";
if (!is_dir($appli_dir)) $appli_dir="./extensions/applis";

if (is_file($appli_dir."/setup.inc.php")) {
	include "./CORE/setup_param.inc.php";
	include $appli_dir."/setup.inc.php";
	$extention_description=str_replace("{[newline]}","<br>",$extention_description);
} else {
	$extention_description='Lucterios';
	$extention_appli='Lucterios';
	$extention_titre='Lucterios';
}

global $dbcnf;
global $connect;

function checkAndShowPrerquired()
{
	echo "<table width='90%'>
				<tr><td colspan='3'><h3>Contrôle Apache/PHP</h3></td></tr>\n";
	
	$depend_OK=1;
	$result="<font color='red'>Mauvaise version<br>(apache2)</font>";
	$apache_vers=apache_get_version();
	$apache_vers=substr($apache_vers,strpos($apache_vers,'/')+1);
	$apache_vers=substr($apache_vers,0,strpos($apache_vers,' '));
	if (version_compare($apache_vers,'2','>='))
		$result="<font color='blue'>OK</font>";
	else
		$depend_OK=0;
	echo "<tr><td>Apache</td><td>$apache_vers</td><td>$result</td></tr>\n";
	
	$result="<font color='red'>Mauvaise version<br>(php5)</font>";
	if (version_compare(phpversion(),'5','>='))
		$result="<font color='blue'>OK</font>";
	else
		$depend_OK=0;
	echo "<tr><td>PHP</td><td>".phpversion()."</td><td>$result</td></tr>\n";
	
	$lib_classes=array();
	$lib_classes[]='DomDocument';
	$lib_classes[]='XsltProcessor';
	foreach($lib_classes as $lib_classe)
	{
		$result="<font color='red'>Non trouvé</font>";
		if (class_exists($lib_classe))
			$result="<font color='blue'>OK</font>";
		else
			$depend_OK=0;
		echo "<tr><td>Lib</td><td>$lib_classe</td><td>$result</td></tr>\n";
	}
	
	$lib_files=array();
	$lib_files[]='PEAR.php';
	$lib_files[]='HTTP/Request.php';
	$lib_files[]='DB/DataObject.php';
	$lib_files[]='DB.php';
	$lib_files[]='XML/Beautifier.php';
	$lib_files[]='Archive/Tar.php';
	
	$sep=$_SERVER['DOCUMENT_ROOT'][0]=='/'?':':';';
	$paths = explode($sep,ini_get('include_path'));
	
	foreach($lib_files as $lib_file)
	{
		$depend_OK2=0;
		$result="<font color='red'>Non trouvé</font>";
		foreach($paths as $path)
			if (is_file($path.DIRECTORY_SEPARATOR.$lib_file)) {
				$depend_OK2=1; 
				$result="<font color='blue'>OK</font>";
			}
		echo "<tr><td>PEAR</td><td>$lib_file</td><td>$result</td></tr>\n";
		$depend_OK=$depend_OK && $depend_OK2;
	}
	echo "</table>";
	return $depend_OK;
}

function showConfParams()
{
	if (is_file('conf/conf.db.php')) {
		global $dbcnf;
		require_once("conf/cnf.inc.php");
		echo "<table width='90%' style='text-align:center;'>\n";
		echo "<tr><td colspan='2'><h3>Configuration de la Base de donnée</h3></td></tr>\n";
		echo "<tr><td>Serveur</td><td>".$dbcnf['dbhost']."</td></tr>\n";
		echo "<tr><td>Nom Base de Données</td><td>".$dbcnf['dbname']."</td></tr>\n";
		echo "<tr><td>Utilisateur</td><td>".$dbcnf['dbuser']."</td></tr>\n";
		echo "<tr><td>Mot de passe</td><td>******</td></tr>\n";
		echo "</table>\n";
		return true;
	}
	else{
		global $extention_appli;
		echo "<table width='90%' style='text-align:center;'>\n<form method='POST'>\n";
		echo "<tr><td colspan='2'><h3>Configuration de la Base de donnée</h3></td></tr>\n";
		echo "<tr><td>Serveur</td><td><input name='dbhost' value='localhost'></td></tr>\n";
		echo "<tr><td>Nom Base de Données</td><td><input name='dbname' value='$extention_appli'></td></tr>\n";
		echo "<tr><td>Utilisateur</td><td><input name='dbuser' value=''></td></tr>\n";
		echo "<tr><td>Mot de passe</td><td><input type='password' name='dbpass' value=''></td></tr>\n";
		echo "<tr><td><br><br></td></tr>\n";
		echo "<tr><td colspan='2'><input type='submit' name='ModifDB' value='Modifier'></td></tr>\n";
		echo "</form></table>\n";
		return false;
	}
}

function startDBCreation()
{
	global $dbcnf;
	global $connect;
	$is_cnx=$connect->connected;
	echo "<form method='POST'>";
	if (!$is_cnx || file_exists(realpath('password.txt'))) 
		echo "	<tr style='text-align:center;'>
				<td colspan='3'>Mot de passe administrateur : <input name='PASSWD'></td>
			</tr>";
	if (!$is_cnx) $title='Créer la Base de Donnée'; else $title='Controler la Base de Donnée';
	echo "	<tr style='text-align:center;'>
			<td colspan='3'><input type='submit' name='DropDB' value='$title'></td>
		</tr>
		<tr><td><br></td></tr>";
	echo "</form>";
	return $connect;
}

function showModules()
{
	global $dbcnf;
	global $connect;
	$is_cnx=$connect->connected;
	echo "<h3>Modules</h3>
		<table width='90%' style='text-align:center;'>
		<tr>
			<th>Nom</th><th>Titre</th><th>Version à installer</th><th>Version installée</th>
		</tr>";
	require_once "CORE/extensionManager.inc.php";
	$ext_list=getExtensions();
	foreach($ext_list as $name=>$dir)
	{
		echo "<tr>";
		$ext=new Extension($name,$dir);
		echo "<td><center><b>".$ext->Name."</b></center></td>";
		echo "<td>".$ext->titre."</td>";
		echo "<td><i>".$ext->getPHPVersion()."</i></td>";
		if ($is_cnx)
			echo "<td><i>".$ext->getDBVersion()."</i></td>";
		else
			echo "<td><i> - - - - </i></td>";
		echo "</tr>";
	}

	echo "</table>";
}

?>
<html>
<head>
  <title><? echo $extention_titre;?> (<? echo $extention_description;?>) - Installation</title>
	<style type="text/css">
	<!--
		BODY {
		background-color: white;
		}
		
		h1 {
		font-family : Helvetica, serif;
		font-size : 10mm;
		font-weight : bold;
		text-align : center;
		vertical-align : middle;
		}
		
		h2 {
	<td	font-size : 8mm;
		font-style : italic;
		font-weight : lighter;
		text-align : center;
		}
		
		h3 {
		font-size : 6mm;
		font-style : italic;
		text-decoration : underline;
		text-align : center;
		}
		
		img {
		border-style: none;
		}
		
		TABLE.main {
		width: 100%;
		height: 95%;
		background-color: rgb(18, 0, 130);
		}
		
		/* banniere */
		
		TR.banniere {
		}
		
		TD.banniere {
		width: 980px;
		height: 70px;
		border-style: solid;
		border-color: orange;
		border-width: 1px;
		}
		
		h1.banniere {
		color: orange;
		}

		h2.banniere {
		color: orange;
		}

		TD.menu {
		width: 120px;
		vertical-align: top;
		color: white;
		}	

		TABLE.contents {
		width: 95%;
		}	
		
		TR.contents {
		vertical-align: top;
		}
		
		TD.contents {
		vertical-align: top;
		}
	
		/* corps */
		TR.corps {
		width: 980px;
		}
		
		TD.corps {
		width: 860px;
		vertical-align: top;
		background-color: white;
		}
		
		/* pied */
		TR.pied {
		width: 980px;
		height: 15px;
		background-color: rgb(100, 100, 200);
		}
		
		TD.pied {
		width: 980px;
		height: 15px;
		color: rgb(230, 230, 230);
		font-size: 9px;
		text-align: right;
		font-weight: bold;
		}	
		
		.go {
		font-family : verdana,helvetica;
		font-size : 10pt;
		font-style : oblique;
		text-decoration : none;
		text-indent : 2cm;
		}
		
		TD {
		font-size: 10pt;
		font-family: verdana,helvetica;
		}
		
		a.invisible {
		color: rgb(18, 0, 130);
		font-size: 0px;
		}
		
		li {
		}
		
		ul {
		font-style : italic;
		}
	-->
	</style>
</head>
<body>
<table class="main">
    <tr class="banniere">
        <td colspan="2" class="banniere">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td>
                        <img src='<? echo $appli_dir."/images/logo.gif";?>' alt='logo' />
                    </td>
                    <td align="center">
                        <h1 class="banniere"><? echo strtoupper($extention_titre);?></h1>
                        <h2 class="banniere">Installation "<? echo $extention_description;?>"</h2>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr class="corps">
	<td class="menu"></td>
	<td class="corps">
<?

if (array_key_exists('ModifDB',$_POST))
{
	$fh=@fopen('conf/conf.db.php','w');
	if (!$fh)
		echo "<br><br><center><font color='red'>Echec d'écriture!</font></center>";
	else
	{
		fwrite($fh,"<?\n");
		fwrite($fh,"/******************************************************************************/\n");
		fwrite($fh,"/* Fichier cnf.db.php\n");
		fwrite($fh,"/* fichier de configuration de la base de données de l'application\n");
		fwrite($fh,"/******************************************************************************/\n");
		fwrite($fh,"\n");
		fwrite($fh,"\$dbcnf = array(\n");
		fwrite($fh,"\t\"dbtype\"=>\"mysql\",\n");
		fwrite($fh,"\t\"dbhost\"=>\"".$_POST['dbhost']."\",\n");
		fwrite($fh,"\t\"dbuser\"=>\"".$_POST['dbuser']."\",\n");
		fwrite($fh,"\t\"dbpass\"=>\"".$_POST['dbpass']."\",\n");
		fwrite($fh,"\t\"dbname\"=>\"".$_POST['dbname']."\"\n");
		fwrite($fh,");\n");
		fwrite($fh,"\n");
		fwrite($fh,"// activation de debug\n");
		fwrite($fh,"\$debugMode = 'n';\n");
		fwrite($fh,"\n");
		fwrite($fh,"/******************************************************************************/\n");
		fwrite($fh,"?>\n");
		fclose($fh);
		echo "<br><br><center><font color='blue'>Configuration modifié</font></center>";
	}
}

if (array_key_exists('DropDB',$_POST))
{
	require_once("conf/cnf.inc.php");
	require_once("CORE/dbcnx.inc.php");
	$is_cnx=$connect->connected;
	$can_be_change_pass=(!$is_cnx || file_exists(realpath('password.txt'))); 

	require_once "CORE/extensionManager.inc.php";
	createDataBase();

	$install=refreshDataBase($can_be_change_pass);
	$install=str_replace("{[newline]}","<br>",$install);
	$install=str_replace("{[","<",$install);
	$install=str_replace("]}",">",$install);

	$last_error='';
	$PASSWD='';
	if ($can_be_change_pass && array_key_exists('PASSWD',$_POST)) {
		$PASSWD=$_POST['PASSWD'];
		if ($PASSWD!='') {
	                $q="UPDATE CORE_users SET pass=PASSWORD('$PASSWD') WHERE login='admin';\n";
        	        if (!$connect->execute($q)) $last_error=$connect->errorMsg;
		}
	}
	if ($last_error=='')
	{
		echo "<center><i><u>$extention_titre</u> est maintenant correctement configure.</i></center>";
		if ($PASSWD!='') 
			echo "<br><br>Vous pouvez maintenant lancer le logiciel en vous connectant entant qu'administrateur (alias <i>admin</i>, mot de passe <i>".str_replace("''","'",$PASSWD)."</i>). Une aide en ligne est à votre disposition pour vous aider dans l'utilisation de cet outil.";
		echo "<br><b>Attention:</b>Nous vous conseillons d'effectuer une mise &agrave; jours pour t&eacute;l&eacute;charger sur internet les derni&egrave;res fonctionnalit&eacute;s<br>";	
	}
	else
		echo "<h3>Erreur de configuration : $last_error</h3>";
	echo "<br><hr>";
	echo "<h3>Rapport d'installation</h3>";
	echo $install;
}
else
{
echo '
		<table class="contents">
			<tr>
				<td><br><br></td>
			</tr>
			<tr class="contents">
				<td width="10%"></td>
				<td>';
	$depend_OK=checkAndShowPrerquired();
echo '				</td>
				<td>';

	$param_OK=showConfParams();

	echo "			</td>
			</tr>
			<tr>
				<td><br><br><br></td>
			</tr>";

	if (!$depend_OK) 
		echo "	<tr style='text-align:center;'>
				<td colspan='3'><font color='red'>Installation impossible - Les dépendances ne sont pas satisfaites</font></td>
			</tr>";

	if ($depend_OK && $param_OK) {
		global $dbcnf;
		global $connect;
		require_once("conf/cnf.inc.php");
		require_once("CORE/dbcnx.inc.php");
		startDBCreation();

    		echo "	<tr class='contents'>
				<td width='10%'></td><td colspan='2'>";
		showModules();
		echo "		</td>
			</tr>";
	}

echo "		</table>
        ";
}
?>
	</td>
    </tr>
    <tr class="pied">
        <td colspan="2" class="pied">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td class="pied">
                        Mise à jour <? echo date ("d/m/Y", filemtime("index.php")); ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
