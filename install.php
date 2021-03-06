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

function convertTimeDuration($timeValue) {
	$result="";
	$hour=floor($timeValue / 3600);
	$min=floor(($timeValue - ($hour*60)) / 60);
	$sec=round($timeValue - ($hour*3600) - ($min*60));
	if ($hour>0)
		$result.="$hour h ";
	if ($min>0)
		$result.="$min min ";
	if ($sec>0)
		$result.="$sec sec";
	return $result;
}

function checkAndShowPrerquired()
{
	echo "<table width='90%'>
				<tr><td colspan='3'><h3>Contr�le MySQL/PHP</h3></td></tr>\n";
	
	$depend_OK=1;
	$result="<font color='red'>Mauvaise version<br>MySQL 5</font>";
	$mysql_vers=mysqli_get_client_version();
	$v_max=(int)($mysql_vers/10000);
	$v_min=(int)(($mysql_vers-$v_max*10000)/100);
	$v_rel=(int)($mysql_vers-$v_max*10000-$v_min*100);
	$mysql_vers_txt="$v_max.$v_min.$v_rel";
	if ($mysql_vers>50000)
		$result="<font color='blue'>OK</font>";
	else
		$depend_OK=0;
	echo "<tr><td>MySQL</td><td>$mysql_vers_txt</td><td>$result</td></tr>\n";
	
	$result="<font color='red'>Mauvaise version<br>(php5)</font>";
	if (version_compare(phpversion(),'5','>='))
		$result="<font color='blue'>OK</font>";
	else
		$depend_OK=0;
	echo "<tr><td>PHP</td><td>".phpversion()."</td><td>$result</td></tr>\n";
	
	$lib_classes=array();
	$lib_classes['php5-mysql']='mysqli';
	$lib_classes['php5-xsl']='XsltProcessor';
	$lib_classes['php5-finfo']='finfo';
	$lib_classes['DomDocument']='DomDocument';
	$lib_classes['zlib']='gzinflate';
	foreach($lib_classes as $lib_name=>$lib_classe)
	{
		$result="<font color='red'>Non trouv�</font>";
		if (class_exists($lib_classe) || function_exists($lib_classe))
			$result="<font color='blue'>OK</font>";
		else
			$depend_OK=0;
		echo "<tr><td>Biblioth�que</td><td>$lib_name</td><td>$result</td></tr>\n";
	}
	echo "<tr><td><br/></td></tr>";
	$retVal=-1;
	@system("java -version", $retVal);
	$result="<td colspan='2'><font color='orange'>Impossible - Java non trouv�</font></td>";
	if ($retVal==0)
		$result="<td>JAVA</td><td><font color='blue'>OK</font></td>";
	echo "<tr><td>Impression</td>$result</tr>\n";	
	include_once("CORE/fichierFonctions.inc.php");
	$maxsize=taille_max_dl_fichier();
	$result="<font color='orange'>faible (<5Mo)</font>";
	if ($maxsize>5242880)
		$result="<font color='blue'>OK</font>";
	echo "<tr><td>Taille fichier max.</td><td>".convert_taille($maxsize)."</td><td>$result</td></tr>\n";
	
	$max_execution_time = @ini_get('max_execution_time');
    if(empty($post_max_size)) {
      	$max_execution_time = @get_cfg_var('max_execution_time');
		if(empty($max_execution_time))
			$max_execution_time = 30;
	}	
	$result="<font color='orange'>faible (<10 min)</font>";
	if ($max_execution_time>600)
		$result="<font color='blue'>OK</font>";
	echo "<tr><td>Temps de r�ponse max.</td><td>".convertTimeDuration($max_execution_time)."</td><td>$result</td></tr>\n";
	echo "</table>";
		
	return $depend_OK;
}

function showConfParams()
{
	if (is_file('conf/conf.db.php')) {
		global $dbcnf;
		require_once("conf/cnf.inc.php");
		echo "<table width='90%' style='text-align:center;'>\n";
		echo "<tr><td colspan='2'><h3>Configuration de la Base de donn�e</h3></td></tr>\n";
		echo "<tr><td>Serveur</td><td>".$dbcnf['dbhost']."</td></tr>\n";
		echo "<tr><td>Nom Base de Donn�es</td><td>".$dbcnf['dbname']."</td></tr>\n";
		echo "<tr><td>Utilisateur</td><td>".$dbcnf['dbuser']."</td></tr>\n";
		echo "<tr><td>Mot de passe</td><td>******</td></tr>\n";
		echo "</table>\n";
		return true;
	}
	else{
		global $extention_appli;
		echo "<table width='90%' style='text-align:center;'>\n<form method='POST'>\n";
		echo "<tr><td colspan='2'><h3>Configuration de la Base de donn�e</h3></td></tr>\n";
		echo "<tr><td>Serveur</td><td><input name='dbhost' value='localhost'></td></tr>\n";
		echo "<tr><td>Nom Base de Donn�es</td><td><input name='dbname' value='$extention_appli'></td></tr>\n";
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
	if (!$is_cnx) {
		echo "<tr style='text-align:center;'><td colspan='3'>Mot de passe administrateur : <input name='PASSWD'></td></tr>";
		$title='Cr�er la Base de Donn�e';
	}
	else {
		global $GLOBAL;
		global $login;
		$login="admin";
		$GLOBAL['ses']=rand().'@'.time();
		require_once("CORE/securityLock.inc.php");
		$SECURITY_LOCK=new SecurityLock();
		list($ret,$msg)=$SECURITY_LOCK->open();
		if (!$ret) {
			  echo "<tr style='text-align:center;'><td colspan='3'>UTILISATEUR CONNECTE</td></tr>";
			  $title='';
		}
		else {
			  echo "<tr style='text-align:center;'><td colspan='3'>Mot de passe administrateur : <input type='password' name='PASSWD'></td></tr>";
			  $title='Controler la Base de Donn�e';
		}
		$SECURITY_LOCK->close();
	}
	if ($title!='') {
		echo "	<tr style='text-align:center;'>
				<td colspan='3'><input type='submit' name='DropDB' value='$title'></td>
			</tr>
			<tr><td><br></td></tr>";
	}
	echo "</form>";
	return $connect;
}

function showModules()
{
	global $dbcnf;
	global $connect;
	$is_cnx=$connect->connected;
	echo "<h3>Modules</h3>
		<table class='modules'>
		<tr>
			<th>Nom</th><th>Titre</th><th>Version � installer</th><th>Version install�e</th><th>D�pendances</th>
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
		echo "<td>";
		$depList=$ext->getCheckedDependances($ext_list);
		foreach($depList as $depName=>$depItem) {
		    echo "$depName ";
		    if ($depItem[0])
			echo "<font color='green'>OK</font><br/>";
		    else
			echo "<font color='red'>".$depItem[1]."</font><br/>";
		}
		echo "</td>";
		echo "</tr>";
	}

	echo "</table>";
}

function checkSessionExisting() {
	global $connect;
	$rep = $connect->execute("SHOW TABLE STATUS LIKE 'CORE_sessions'");
	return ($rep && ($connect->getNumRows($rep) == 1));
}

function checkAdminPassword() {
	global $GLOBAL;
	global $connect;
	if ($connect->connected) {
		if (checkSessionExisting()) {
			$pass=str_replace("'","''",$GLOBAL["PASSWD"]);
			$pass_md5=md5($pass);
			$q = "SELECT COUNT(*) FROM CORE_users WHERE login='admin' AND (pass=PASSWORD('$pass') OR pass='$pass_md5') AND actif='o'";
			list($nb) = $connect->getRow($connect->execute($q));
			return ($nb == 1);
		}
		else
		      return true;
	}
	else
	      return true;
}

?>
<html>
<head>
  <title><?php echo $extention_titre;?> (<?php echo $extention_description;?>) - Installation</title>
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
		font-size : 8mm;
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
		vertical-align: text-top;
		color: white;
		}	

		TABLE.contents {
		width: 95%;
		}	
		
		TR.contents {
		vertical-align: text-top;
		}
		
		TD.contents {
		vertical-align: text-top;
		}
	
		/* corps */
		TR.corps {
		width: 980px;
		}
		
		TD.corps {
		width: 860px;
		vertical-align: text-top;
		background-color: white;
		}
		

		TABLE.modules {
		width: 90%;
		text-align:center;
		}	
		
		TABLE.modules TR {
		vertical-align: text-top;
		}
		
		TABLE.modules TD {
		vertical-align: text-top;
		text-align:left;
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
                        <img src='<?php echo $appli_dir."/images/logo.gif";?>' alt='logo' />
                    </td>
                    <td align="center">
                        <h1 class="banniere"><?php echo strtoupper($extention_titre);?></h1>
                        <h2 class="banniere">Installation "<?php echo $extention_description;?>"</h2>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr class="corps">
	<td class="menu"></td>
	<td class="corps">
<?php

if (array_key_exists('ModifDB',$_POST))
{
	$fh=@fopen('conf/conf.db.php','w');
	if (!$fh)
		echo "<br><br><center><font color='red'>Echec d'�criture!</font></center>";
	else
	{
		fwrite($fh,"<?php\n");
		fwrite($fh,"/******************************************************************************/\n");
		fwrite($fh,"/* Fichier cnf.db.php\n");
		fwrite($fh,"/* fichier de configuration de la base de donn�es de l'application\n");
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
		echo "<br><br><center><font color='blue'>Configuration modifi�</font></center>";
	}
}

if (array_key_exists('DropDB',$_POST))
{
	require_once("conf/cnf.inc.php");
	require_once("CORE/dbcnx.inc.php");
	$is_cnx=$connect->connected;
	if (!checkAdminPassword()) {
		  $last_error="Mot de passe invalide!!";
	}
	else
	try
	{
		global $GLOBAL;
		global $login;
		global $SECURITY_LOCK;
		$login="admin";
		$GLOBAL['ses']=rand().'@'.time();
		require_once("CORE/securityLock.inc.php");
		$SECURITY_LOCK=new SecurityLock();
		if ($is_cnx)
		      $SECURITY_LOCK->open(true);

		$can_be_change_pass=!$is_cnx || !checkSessionExisting(); 
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
				$q="UPDATE CORE_users SET pass=md5('$PASSWD') WHERE login='admin';\n";
				$connect->execute($q,true);
			}
		}
		$SECURITY_LOCK->close();
	}
	catch(Exception $e){
		$last_error=$e->getMessage();
		$SECURITY_LOCK->close();
	}
	if ($last_error=='')
	{
		echo "<center><i><u>$extention_titre</u> est maintenant correctement configure.</i></center>";
		if ($PASSWD!='') 
			echo "<br><br>Vous pouvez maintenant lancer le logiciel en vous connectant entant qu'administrateur (alias <i>admin</i>, mot de passe <i>".str_replace("''","'",$PASSWD)."</i>). Une aide en ligne est � votre disposition pour vous aider dans l'utilisation de cet outil.";
		echo "<br><b>Attention:</b>Nous vous conseillons d'effectuer une mise &agrave; jours pour t&eacute;l&eacute;charger sur internet les derni&egrave;res fonctionnalit&eacute;s<br>";	
	}
	else {
		echo "<h3>Erreur de configuration</h3>";
		echo "$last_error<br/>";
	}
	if ($install!='') {
		echo "<br><hr>";
		echo "<h3>Rapport d'installation</h3>";
		echo $install;
	}
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
				<td colspan='3'><font color='red'>Installation impossible - Les d�pendances ne sont pas satisfaites</font></td>
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
                        Mise � jour <?php echo date ("d/m/Y", filemtime("index.php")); ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
