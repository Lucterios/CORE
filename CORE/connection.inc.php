<?php
// library file write by SDK tool
// --- Last modification: Date 17 July 2008 21:21:38 By  ---

//@BEGIN@

function mustAutentificate($mess) {
	global $REPONSE;
	$REPONSE .= "<REPONSE observer='CORE.Auth' source_extension='CORE' source_action='authentification'><![CDATA[$mess]]></REPONSE>";
}
$IS_CONNECTED = false;
if(! array_key_exists("ses",$GLOBAL) && ! array_key_exists("login",$GLOBAL)) { mustAutentificate('NEEDAUTH');
}
elseif ( array_key_exists("login",$GLOBAL) && array_key_exists("pass",$GLOBAL)) {
	//tentative de connexion
	$q = "SELECT COUNT(*) FROM CORE_users WHERE login='".$GLOBAL["login"]."' AND pass=PASSWORD('".$GLOBAL["pass"]."') AND actif='o'";
	list($nb) = $connect->getRow($connect->execute($q));
	if($nb != 1) mustAutentificate('BADAUTH');
	else {
		$GLOBAL["ses"] = get_session_id($GLOBAL["login"],$timeOut,$connect,$GLOBAL["REMOTE_ADDR"],"multiple");
		if($GLOBAL["ses"] == "") { mustAutentificate('BADFROMLOCATION');
		}
		else {
			// recup du realName
			$q = "SELECT realName FROM CORE_users WHERE login='".$GLOBAL["login"]."'";
			list($realName) = $connect->getRow($connect->execute($q));
			require_once'CORE/setup_param.inc.php';
			require'CORE/setup.inc.php';
			$CORE_version = $version_max.".".$version_min.".".$version_release.".".$version_build;
			if( is_dir('applis'))$appli_dir = 'applis';
			else $appli_dir = 'extensions/applis';
			require"$appli_dir/setup.inc.php";
			$applis_version = $version_max.".".$version_min.".".$version_release.".".$version_build;
			$application_subtitle = "";
			$CopyRight = "";
			if( is_file("$appli_dir/application.inc.php")) {
				require"$appli_dir/application.inc.php";
				if( function_exists('application_subtitle'))$application_subtitle = application_subtitle();
				if( function_exists('application_CopyRight'))$CopyRight = application_CopyRight();
			}
			$path = split('/',$_SERVER["SCRIPT_NAME"]);
			unset($path[ count($path)-1]);
			$path = trim( implode("/",$path));
			if( strlen($path)>0)$path = "/".$path;
			$REPONSE .= "<REPONSE observer='CORE.Auth' source_extension='CORE' source_action='authentification'>
					<CONNECTION>
					<TITLE>".$extention_titre."</TITLE>
					<SUBTITLE>".$application_subtitle."</SUBTITLE>
					<VERSION>".$applis_version."</VERSION>
					<SERVERVERSION>".$CORE_version."</SERVERVERSION>
					<COPYRIGHT>".$CopyRight."</COPYRIGHT>
					<LOGONAME>http://".$_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$path."/$appli_dir/images/logo.gif</LOGONAME>
					<LOGIN>".$GLOBAL['login']."</LOGIN>
					<REALNAME>".$realName."</REALNAME>
					</CONNECTION>
					<PARAM name='ses' type='str'>".$GLOBAL["ses"]."</PARAM>
					<![CDATA[OK]]>
				</REPONSE>";
			$IS_CONNECTED = true;
			$login = $GLOBAL['login'];
		}
	}
}
elseif ( array_key_exists("ses",$GLOBAL) && ! verif_session($GLOBAL["ses"],$timeOut,$connect,$GLOBAL["REMOTE_ADDR"])) { mustAutentificate('BADSESS');
}
else {
	$IS_CONNECTED = true;
	if( array_key_exists("login",$GLOBAL))$login = $GLOBAL["login"];
	else {
		$q = "SELECT uid FROM CORE_sessions WHERE sid='".$GLOBAL["ses"]."'";
		$r = $connect->execute($q);
		list($login) = $connect->getRow($r);
		$q = "SELECT id FROM CORE_users WHERE login='$login'";
		$r = $connect->execute($q);
		list($LOGIN_ID) = $connect->getRow($r);
	}
}

//@END@
?>
