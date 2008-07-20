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
// --- Last modification: Date 10 March 2008 18:56:01 By  ---

//@BEGIN@
// +----------------------------------------------------------------------
// | PHP Source
// +----------------------------------------------------------------------
// | Copyright (C) 2005 by Laurent <ffss38info@freesurf.fr>
// +----------------------------------------------------------------------
// |
// | Copyright: See COPYING file that comes with this distribution
// +----------------------------------------------------------------------
//

global $ExensionVersions
;

$ExensionVersions=array();


function callApplicationPostInstallation()
{
	global $rootPath;
	if (!isset($rootPath))
		$rootPath="./";
	$setupMsg ='';
	if (is_file($rootPath.'applis/postInstallation.inc.php'))
		require $rootPath.'applis/postInstallation.inc.php';
	else if (is_file($rootPath.'extensions/postInstallation.inc.php'))
		require $rootPath.'extensions/applis/postInstallation.inc.php';
	else if (is_file($rootPath.'applis/application.inc.php'))
		require $rootPath.'applis/application.inc.php';
	else
		require $rootPath.'extensions/applis/application.inc.php';
	if (function_exists('postInstallation'))
	{
		global $ExensionVersions;
		$setupMsg .=postInstallation($ExensionVersions);
	}
	else if (function_exists('application_postInstallation'))
	{
		global $ExensionVersions;
		$setupMsg .=application_postInstallation($ExensionVersions);
	}
	else
		$setupMsg .='Pas de post-installation{[newline]}';
	return $setupMsg;
}


global $show_complete;
if (!isset($show_complete))
	$show_complete=true;

global $update_package;
if (!isset($update_package))
	$update_package=true;

require_once("conf/cnf.inc.php");
require_once("CORE/dbcnx.inc.php");
require_once("CORE/setup_param.inc.php");

function convertID($text)
{
	$text=str_replace(array("'"," ",'"'), "", $text);
	$text=str_replace(array("é","è","ê","ë"),"e", $text);
	$text=str_replace(array("à","â","_APAS_"),"a", $text);
	$text=str_replace(array("î","ï"),"i",$text);
	$text=str_replace(array("ô","ö"),"o",$text);
	$text=str_replace(array("û","ü"),"u",$text);
	return $text;
}

// verif du CORE
// verif de la DB du CORE

function cleanOldData($extention_name,&$return)
{
	global $connect;
	$q = "";
	$return = "netoyage de la base pour mise a jour{[newline]}";
	// c'est une mise a jour, on commence par vider les anciennes valeures de la base...

	$q = "DELETE FROM CORE_extension_actions WHERE extensionId='$extention_name'";
	$connect->execute($q);
	if (trim($connect->errorMsg)!="")
		$return .= $connect->errorMsg."{[newline]}";
	$q = "DELETE FROM CORE_extension_rights WHERE extensionId='$extention_name'";
	$connect->execute($q);
	if (trim($connect->errorMsg)!="")
		$return .= $connect->errorMsg."{[newline]}";
	$q = "DELETE FROM CORE_menu WHERE extensionId='$extention_name'";
	$connect->execute($q);
	if (trim($connect->errorMsg)!="")
		$return .= $connect->errorMsg."{[newline]}";
}

function setupTableDB($dir,$extention_name, &$return)
{
	global $dbcnf;
	$tbl_list=array();
	$dh=opendir($dir);
	while (($file = readdir($dh)) != false)
	{
		if(is_file($dir . $file) && (substr($file,-8,8)==".tbl.php"))
		{
			$tableName=substr($file,0,-8);
			array_push($tbl_list,$tableName);
		}
	}
	$return="";
	foreach($tbl_list as $table_name)
	{
		require_once($dir.$table_name.".tbl.php");
		$class_name="DBObj_".$extention_name."_".$table_name;
		$obj=new $class_name;
		$update_error=$obj->setup();
		$return.=$update_error[1];
	}
	return true;
}

function insertionExtension($extention_name, $extention_description, $version_max, $version_min, $version_release, $version_build, &$return)
{
	global $dbcnf;
	require_once "CORE/extension.tbl.php";
	$DBextension=new DBObj_CORE_extension;
	$DBextension->extensionId=$extention_name;
	$nb=$DBextension->find();
	if ($nb!=0)
		while ($DBextension->fetch())
		{
			$DBextension->title=$extention_description;
			$DBextension->description=$extention_description;
			$DBextension->versionMaj=$version_max;
			$DBextension->versionMin=$version_min;
			$DBextension->versionRev=$version_release;
			$DBextension->versionBuild=$version_build;
			$DBextension->validite='n';
			$DBextension->update();
		}
	else
	{
		$DBextension->description=$extention_description;
		$DBextension->versionMaj=$version_max;
		$DBextension->versionMin=$version_min;
		$DBextension->versionRev=$version_release;
		$DBextension->versionBuild=$version_build;
		$DBextension->validite='n';
		$DBextension->insert();
	}
	if (PEAR::isError($DBextension->_lastError))
		$return .= $DBextension->_lastError."{[newline]}";
	else
		$return .= "insertion/modification de l'extension N°".$DBextension->id."{[newline]}";
	return $DBextension->id;
}

function checkAndAddActions($dir,$ext_id,$extention_name,$actions, &$return)
{
	global $dbcnf;
	require_once "CORE/extension_actions.tbl.php";

	foreach($actions as $act)
	if(file_exists($dir.$act->action.".act.php"))
	{
		$DBaction=new DBObj_CORE_extension_actions;
		$DBaction->extension=$ext_id;
		$DBaction->action=$act->action;
		$nb=$DBaction->find();
		if ($nb!=0)
			while ($DBaction->fetch())
			{
				$DBaction->extension=$ext_id;
				$DBaction->action=$act->action;
				$DBaction->extensionId=$extention_name;
				$DBaction->description=str_replace("'", "`", $act->description);
				$DBaction->rightId=$act->rightNumber;
				$ret="modification";
				$DBaction->update();
			}
		else
		{
			$DBaction->extension=$ext_id;
			$DBaction->action=$act->action;
			$DBaction->extensionId=$extention_name;
			$DBaction->description=str_replace("'", "`", $act->description);
			$DBaction->rightId=$act->rightNumber;
			$ret="insertion";
			$DBaction->insert();
		}
		if (PEAR::isError($DBaction->_lastError))
			$return .= "$ret de l'action: ".$act->action." [".$DBaction->_lastError."- $q ]{[newline]}";
		else
			$return .= "$ret de l'action: ".$act->action."{[newline]}";
	}
	else $return .= "EXTENSION: $extention_name:{[newline]}fichier action ".$act->action." non present, l'action ne sera pas referencee dans la DB{[newline]}";
}

function checkAndAddReportModel($dir,$extention_name,&$return)
{
	require_once("CORE/printmodel.tbl.php");
	$model=new DBObj_CORE_printmodel;
	$model->extensionid=$extention_name;
	$model->find();

	while ($model->fetch())
	{
		$printfile="$dir/".$model->identify.".prt.php";
		if (!is_file($printfile))
		{
			$model->delete();
			$return .= "Impression ".$model->identify." supprimé{[newline]}";
		}
	}

	$prt_list=array();
	$dh=opendir($dir);
	while (($file = readdir($dh)) != false)
	{
		if(is_file($dir . $file) && (substr($file,-8,8)==".prt.php"))
		{
			$modelName=substr($file,0,-8);
			array_push($prt_list,$modelName);
		}
	}
        	require_once("ConvertPrintModel.inc.php");
	foreach($prt_list as $printmodel)
	{
            		list($id,$model,$res) = checkDBModel($extention_name,$printmodel,true);
            		if ($id>0)
			$return .= "Vérification de l`impression $printmodel ($res){[newline]}";
            		else
			$return .= "Impression $printmodel non valid{[newline]}";
	}
}

function updateTableExtensionRights($ext_id,$extention_name,$rights, &$return)
{
	global $dbcnf;
	require_once "CORE/extension_rights.tbl.php";
	require_once "CORE/group_rights.tbl.php";
	global $connect;
	foreach($rights as $key=>$r)
	{
		$DBrights=new DBObj_CORE_extension_rights;
		$DBrights->extension=$ext_id;
		$DBrights->rightId=$key;
		$nb=$DBrights->find();
		if ($nb!=0)
			while($DBrights->fetch())
			{
				$DBrights->description=$r->description;
				$DBrights->extensionId=$extention_name;
				$DBrights->weigth=$r->weigth;
				$DBrights->update();
			}
		else
		{
			$DBrights->description=$r->description;
			$DBrights->extensionId=$extention_name;
			$DBrights->weigth=$r->weigth;
			$DBrights->insert();
		}
		if (PEAR::isError($DBrights->_lastError))
			$return .= "insertion du droit:". $r->description.$DBrights->_lastError."{[newline]}";
		else
		{
			$return .= "insertion du droit:".$r->description."{[newline]}";
			$DBgrouprights=new DBObj_CORE_group_rights;
			//CheckGroupRight($right, $weigth, $extensionId, $rightId)
			$ret=$DBgrouprights->call("CheckGroupRight",$DBrights->id,$DBrights->weigth,$extention_name,$key);
			$return .= "GroupRight : $ret{[newline]}";
		}
	}
}

function refreshlinkActionRight($ext_id,$extention_name,&$return)
{
	global $connect;
	$q= "UPDATE CORE_extension_actions a, CORE_extension_rights b SET a.rights=b.id
WHERE a.extension =$ext_id AND a.extension = b.extension AND a.rightId = b.rightId";
	$connect->execute($q);
	$return .= "Rafrichissement des liens actions/rights ".$connect->errorMsg."{[newline]}";
}

function updateTableExtensionParams($extention_name,$params, &$return)
{
	global $dbcnf;
	require_once "CORE/extension_params.tbl.php";
	$DBparams=new DBObj_CORE_extension_params;
	$DBparams->extensionId=$extention_name;
	$DBparams->find();
	while ($DBparams->fetch())
	{
		if (!array_key_exists($DBparams->paramName,$params))
			$DBparams->delete();
	}
	foreach($params as $key=>$val)
	{
		$DBparams=new DBObj_CORE_extension_params;
		$DBparams->extensionId=$extention_name;
		$DBparams->paramName=$key;
		$nb=$DBparams->find();
		if ($nb==0)
		{
			$DBparams->value=$val->defaultvalue;
			$DBparams->description=$val->description;
			$DBparams->type=$val->type;
			$DBparams->param=$val->getExtendToText(false);
			$DBparams->insert();
			if (PEAR::isError($DBparams->_lastError))
				$return .= "insertion du paramètre: ".$key." ".$DBparams->_lastError."{[newline]}";
			else
				$return .= "insertion du paramètre: ".$key."{[newline]}";
		}
		else
		{
			$DBparams->fetch();
			$DBparams->description=$val->description;
			$DBparams->type=$val->type;
			$DBparams->param=$val->getExtendToText(false);
			$DBparams->update();
			if (PEAR::isError($DBparams->_lastError))
				$return .= "modification du paramètre: ".$key." ".$DBparams->_lastError."{[newline]}";
			else
				$return .= "modification du paramètre: ".$key."{[newline]}";
		}
	}
}

function updateTableMenu($extention_name,$menus, &$return)
{
	global $connect;
	foreach($menus as $m)
	{
		$mid = convertID($m->description);
		$pere = convertID($m->pere);
		$q = "INSERT INTO CORE_menu(menuItemId, extensionId, action, pere, description, help, icon, shortcut, position, modal) ";
            		$modal_txt='n';
		if ($m->modal==1) $modal_txt='o';
		$q.= "VALUES('$mid', '$extention_name', '".$m->act."', '".$pere."', '".str_replace("'", "'", $m->description)."', '".str_replace("'", "'", $m->help)."','".$m->icon."','".$m->shortcut."',".$m->position.",'$modal_txt')";
		$connect->execute($q);
		$return .= "insertion du menu: ".$m->description." ".$connect->errorMsg."{[newline]}";
	}
}
// fonction de mise a jour de la base pour une extension
function installExtention($extention_name, $extention_description, $version_max, $version_min,
			$version_release, $version_build, $rights, $actions, $menus, $params, $asInstallFunc, $mode = "install")
{
	global $rootPath;
	if (!isset($rootPath))
		$rootPath="./";
	global $update_package;
	$update_package=false;

	if ($extention_name=="CORE")
		$dir = $rootPath.$extention_name."/";
	else if ($extention_name=="applis")
	{
		$dir = $rootPath.$extention_name."/";
		if (!is_dir($dir))
			$dir = $rootPath."extensions/$extention_name/";
	}
	else
		$dir = $rootPath."extensions/$extention_name/";
	$return = "";

	addExtensionVersions($dir, $extention_name);

	// mise a jour de la table extension
	if($mode != "install")
		cleanOldData($extention_name,$return);

	// mise a jour des tables DB de l'extension
	$success = setupTableDB($dir,$extention_name,$return);

	// insertion de l'extension
	$ext_id=insertionExtension($extention_name, $extention_description, $version_max, $version_min, $version_release, $version_build,$return);

	// mise a jour de la table extension_rights
	updateTableExtensionRights($ext_id,$extention_name,$rights,$return);

	// verif de la presence des fichiers actions
	checkAndAddActions($dir,$ext_id,$extention_name,$actions,$return);

	// verif de la presence des fichiers impression
        	checkAndAddReportModel($dir,$extention_name,$return);

	// mise a jour de la table extension_params
	updateTableExtensionParams($extention_name,$params,$return);

	// rafraichir le lien entre les tables extension_actions et extension_rights
	refreshlinkActionRight($ext_id,$extention_name,$return);

	// mise a jour de la table menu
	updateTableMenu($extention_name,$menus,$return);

	if ($success)
	{
		// appel de la fonction d'install du module
		if (is_file($dir."postInstall.inc.php")
)
		{
			require_once $dir."postInstall.inc.php";
			$func = "install_".$extention_name;
			if (function_exists($func))
			{
				$return .= "appel de la propre fonction d'install de l'extension{[newline]}";
				global $ExensionVersions;
				$return .= $func($ExensionVersions[$extention_name]);
				$return .= "fin d'appel de la propre fonction d'install de l'extension{[newline]}";
			}
		}

		// validation de l'extension
		global $connect;
		$q = "UPDATE CORE_extension SET validite='o' WHERE extensionId='$extention_name'";
		$connect->execute($q);

		// affichage de la fin de traitement de l'extesion
		$return .= "Fin de traitement{[newline]}";
	}
	else
		$return .= "Echec du traitement{[newline]}";
	$update_package=$success;
	return $return;
}

function checkversion($version_max,$vmax,$version_min,$vmin,$version_release,$vrel,$version_build,$vbuild)
{ //retrun true si plus recent
   if ($version_max < $vmax)
      return false;
   elseif ($version_max > $vmax)
      return true;
   elseif ($version_min < $vmin)
      return false;
   elseif ($version_min > $vmin)
      return true;
   elseif ($version_release < $vrel)
      return false;
   elseif ($version_release > $vrel)
      return true;
   elseif ($version_build <= $vbuild)
      return false;
   else
      return true;
}

function getCurrentExtensionVersion($ext)
{
	global $connect;
	$q = "SELECT versionMaj, versionMin, versionRev, versionBuild, validite ";
	$q.= "FROM CORE_extension WHERE extensionId = '$ext'";
	$res = $connect->execute($q);
	if ((trim($connect->errorMsg)=="")
  && (1 == $connect->getNumRows($res))
)
	{
		return $connect->getRow($res);
	}
	else
		return null;
}

function addExtensionVersions($dir, $ext)
{
	require($dir . "/setup.inc.php");
	list($vmax, $vmin, $vrel, $vbuild, $valid) = getCurrentExtensionVersion($ext);
	global $ExensionVersions;
	$ExensionVersions[$ext]=array("$vmax.$vmin.$vrel.$vbuild","$version_max.$version_min.$version_release.$version_build");
}

function checkAndDo($dir, $ext)
{
	global $show_complete;
	global $connect;
	global $update_package;
	$update_package=false;

	$return = "";
	require_once "CORE/log.inc.php";
	logAutre("checkAndDo: $dir, $ext");
	$return .= "{[newline]}EXTENSION: $ext{[newline]}";
	if(file_exists($dir . "/setup.inc.php"))
	{
		require($dir . "/setup.inc.php");
		if($asInstallFunc && !function_exists("install_".$extention_name))
			require_once($dir . "/setupfunc.inc.php");
		// verif de la presence d'une version de l'extension dans la base
		$extension_version = getCurrentExtensionVersion($extention_name);
		if($extension_version)
		{
			list($vmax, $vmin, $vrel, $vbuild, $valid) = $extension_version;
			// si present, verif de la version
			if (!checkversion($version_max,$vmax,$version_min,$vmin,$version_release,$vrel,$version_build,$vbuild))
			{
				// si version plus ancienne en fichier que dans la base => mess erreur
				$return .= "Attention la version distante est plus ancienne que la version local{[newline]}";
			}
			elseif($version_max == $vmax && $version_min == $vmin && $version_release == $vrel && $version_build == $vbuild)
			{
				// si meme version, verif de la validit?
				if($valid == 'o')
				{
					$return .= "Version non mise à jour{[newline]}La base n'est pas touché{[newline]}";
				}
				else
				{
					// si pas valide => mise a jour de la base pour l'extention
					$return .= "Version distante et local identique mais extension non validée dans la base de donnée{[newline]}Mise à jour{[newline]}";
					$ret=installExtention($extention_name, $extention_description, $version_max, $version_min, $version_release, $version_build, $rights, $actions, $menus, $params, $asInstallFunc, "update");
					if ($show_complete)
						$return .= $ret;
				}
			}
			else
			{
				// si version plus recente en fichier que dans la base => mise a jour de la base pour la nouvelle version
				$return .= "Version distante plus recente que la version local{[newline]}Mise à jour{[newline]}";
				$ret=installExtention($extention_name, $extention_description, $version_max, $version_min, $version_release, $version_build, $rights, $actions, $menus, $params, $asInstallFunc, "update");
				if ($show_complete)
					$return .= $ret;
			}
		}
		else
		{
			// pas present dans la base, on l'install
			$return .= "Extension non existante dans la base, {[newline]}Installation{[newline]}";
			$ret=installExtention($extention_name, $extention_description, $version_max, $version_min, $version_release, $version_build, $rights, $actions, $menus, $params, $asInstallFunc);
			if ($show_complete)
				$return .= $ret;
		}
	}
	return $return;
}

function doAllSetup($DropDB='N',$showComplete=true)
{
	global $show_complete;
	$show_complete=$showComplete;
	global $rootPath;
	if (!isset($rootPath))
		$rootPath="./";
	// Verifier l'existance de la DB

	$setupMsg = "";
	global $dbcnf;
	global $connect;

	if ($connect->connected && ($DropDB!='N'))
	{
		$connect->execute('DROP DATABASE '.$dbcnf['dbname']);
		$setupMsg .= "Destruction de DB :".$dbcnf['dbname']." ".$connect->errorMsg."{[newline]}";
		$connect->connected = false;
		$connect->connect($dbcnf);
	}

	if (!$connect->connected)
	{
		$dsn = $dbcnf['dbtype']."://".$dbcnf['dbuser'].":".$dbcnf['dbpass']."@".$dbcnf['dbhost'];
		$options = array('debug'=> 2,'portability' => DB_PORTABILITY_ALL);
		$tmp_dbh =& DB::connect($dsn, $options);
		if (!DB::isError($tmp_dbh))
		{
			$q='CREATE DATABASE '.$dbcnf['dbname'];
			$r =& $tmp_dbh->query($q);
			if (DB::isError($r))
				$setupMsg .= "Echec de creation de DB :".$r->getMessage()."{[newline]}";
			else
				$setupMsg .= "Creation de DB :".$dbcnf['dbname']."{[newline]}";
			$connect->connect($dbcnf);
		}
		else
			$setupMsg .= "Echec de connection pour creation de DB :".$tmp_dbh->getMessage()."{[newline]}";
	}

	if ($connect->connected)
	{
		// l'applis
		$setupMsg .= checkAndDo($rootPath."CORE/", "CORE");
		if (is_dir('applis/'))
		{
			$setupMsg .= checkAndDo($rootPath."applis/", "applis");
		}
		else
		{
			$setupMsg .= checkAndDo($rootPath."extensions/applis/", "applis");
		}
		// boucle sur les extensions
		$extDir = $rootPath."extensions/";
		if(is_dir($extDir))
		{
			$dh = opendir($extDir);
			while (($file = readdir($dh)) != false)
			{
				if(($file[0]!='.') && ($file[0]!='applis') && is_dir($extDir . $file))
				{
					$setupMsg .= checkAndDo($extDir . $file, $file);
				}
			}
			closedir($dh);
		}

		$setupMsg .=callApplicationPostInstallation();
	}
	else
		$setupMsg .= "BASE DE DONNEE '".$dbcnf['dbname']."' INTROUVABLE!{[newline]}";
	return $setupMsg;
}
//@END@
?>
