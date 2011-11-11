<?php
// 
//     This file is part of Lucterios.
// 
//     Lucterios is free software; you can redistribute it and/or modify
//     it under the terms of the GNU General Public License as published by
//     the Free Software Foundation; either version 2 of the License, or
//     (at your option) any later version.
// 
//     Lucterios is distributed in the hope that it will be useful,
//     but WITHOUT ANY WARRANTY; without even the implied warranty of
//     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//     GNU General Public License for more details.
// 
//     You should have received a copy of the GNU General Public License
//     along with Lucterios; if not, write to the Free Software
//     Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// 
// 	Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
//  // library file write by SDK tool
// --- Last modification: Date 11 May 2009 21:17:34 By  ---

//@BEGIN@
$G_REALNAME="";

function mustAutentificate($mess) {
	global $REPONSE;
	$REPONSE .= "<REPONSE observer='CORE.Auth' source_extension='CORE' source_action='authentification'><![CDATA[$mess]]></REPONSE>";
}

function getConnectionInfo($login,$ses) {
	global $REPONSE;
	global $connect;
	global $_SERVER;
	global $G_REALNAME;

	$q = "SELECT realName FROM CORE_users WHERE login='$login'";
	list($G_REALNAME) = $connect->getRow($connect->execute($q));
	require_once('CORE/setup_param.inc.php');
	require('CORE/setup.inc.php');
	$CORE_version = $version_max.".".$version_min.".".$version_release.".".$version_build;
	require("extensions/applis/setup.inc.php");
	$applis_version = $version_max.".".$version_min.".".$version_release.".".$version_build;
	$application_subtitle = "";
	$CopyRight = "";
	$SupportEmail="support@lucterios.org";
	if(is_file("extensions/applis/application.inc.php")) {
		require"extensions/applis/application.inc.php";
		if( function_exists('application_subtitle'))
			$application_subtitle = application_subtitle();
		if( function_exists('application_CopyRight'))
			$CopyRight = application_CopyRight();
		if( function_exists('application_SupportEmail'))
			$SupportEmail = application_SupportEmail();
	}
	$path = explode('/',$_SERVER["SCRIPT_NAME"]);
	unset($path[ count($path)-1]);
	$path = trim( implode("/",$path));
	if( strlen($path)>0)
		$path = "/".$path;
	$http_referer=$_SERVER["HTTP_REFERER"];
	$server_name=$_SERVER['SERVER_NAME'];
	$server_port=(int)$_SERVER['SERVER_PORT'];
	$protocol=substr($http_referer,0,strpos($http_referer,'://'));
	if ($protocol=='')
		$protocol=($server_port==443)?'https':'http';
	$InfoServer="";
	$q="SELECT titre,versionMaj,versionMin,versionRev,versionBuild FROM CORE_extension ORDER BY id";
	$q_id=$connect->execute($q);
	while (($extensionDesc = $connect->getRow($q_id))) {
		list($titre,$versionMaj,$versionMin,$versionRev,$versionBuild)=$extensionDesc;
		$InfoServer.="$titre=$versionMaj.$versionMin.$versionRev.$versionBuild{[newline]}";
	}
	$InfoServer.="{[newline]}{[italic]}".$_SERVER['SERVER_SOFTWARE']."{[/italic]}";

	$REPONSE .= "<REPONSE observer='CORE.Auth' source_extension='CORE' source_action='authentification'>
				<CONNECTION>
					<TITLE>$extention_titre</TITLE>
					<SUBTITLE>$application_subtitle</SUBTITLE>
					<VERSION>$applis_version</VERSION>
					<SERVERVERSION>$CORE_version</SERVERVERSION>
					<COPYRIGHT>$CopyRight</COPYRIGHT>
					<LOGONAME>$protocol://$server_name:$server_port$path/extensions/applis/images/logo.gif</LOGONAME>
					<LOGIN>$login</LOGIN>
					<REALNAME>$G_REALNAME</REALNAME>
					<SUPPORT_EMAIL><![CDATA[$SupportEmail]]></SUPPORT_EMAIL>
					<INFO_SERVER><![CDATA[$InfoServer]]></INFO_SERVER>
				</CONNECTION>
				<PARAM name='ses' type='str'>$ses</PARAM>
				<![CDATA[OK]]>
			</REPONSE>";
}


$IS_CONNECTED = false;
if(! array_key_exists("ses",$GLOBAL) && ! array_key_exists("login",$GLOBAL))
	mustAutentificate('NEEDAUTH');
elseif ( array_key_exists("login",$GLOBAL) && array_key_exists("pass",$GLOBAL)) {
	//tentative de connexion
	$login=str_replace("'","''",$GLOBAL["login"]);
	$pass=str_replace("'","''",$GLOBAL["pass"]);
	$pass_md5=md5($pass);
	$q = "SELECT COUNT(*) FROM CORE_users WHERE login='$login' AND (pass=PASSWORD('$pass') OR pass='$pass_md5') AND actif='o'";
	list($nb) = $connect->getRow($connect->execute($q));
	if($nb != 1)
		mustAutentificate('BADAUTH');
	else {
		require_once("CORE/securityLock.inc.php");
		$SECURITY_LOCK=new SecurityLock();
		if ($SECURITY_LOCK->isLock()!=-1) {
			$GLOBAL["ses"] = get_session_id($GLOBAL["login"],$timeOut,$connect,$GLOBAL["REMOTE_ADDR"],"multiple");
			if($GLOBAL["ses"] == "")
				mustAutentificate('BADFROMLOCATION');
			else {
				// recup du realName
				getConnectionInfo($GLOBAL["login"],$GLOBAL["ses"]);
				$IS_CONNECTED = true;
				$login = $GLOBAL['login'];
			}
		}
		else
			mustAutentificate('LOCK');
	}
}
elseif ( array_key_exists("ses",$GLOBAL) && ! verif_session($GLOBAL["ses"],$timeOut,$connect,$GLOBAL["REMOTE_ADDR"])) {
	mustAutentificate('BADSESS');
}
else {
	require_once("CORE/securityLock.inc.php");
	$SECURITY_LOCK=new SecurityLock();

	$IS_CONNECTED = true;
	if( array_key_exists("login",$GLOBAL))
		$login = $GLOBAL["login"];
	else {
		$q = "SELECT uid FROM CORE_sessions WHERE sid='".$GLOBAL["ses"]."'";
		$r = $connect->execute($q);
		list($login) = $connect->getRow($r);
		$q = "SELECT id FROM CORE_users WHERE login='$login'";
		$r = $connect->execute($q);
		list($LOGIN_ID) = $connect->getRow($r);
		if(array_key_exists("info",$GLOBAL))
			getConnectionInfo($login,$GLOBAL["ses"]);
	}
}
//@END@
?>
