<?php
// 	This file is part of Diacamma, a software developped by "Le Sanglier du Libre" (http://www.sd-libre.fr)
// 	Thanks to have payed a retribution for using this module.
// 
// 	Diacamma is free software; you can redistribute it and/or modify
// 	it under the terms of the GNU General Public License as published by
// 	the Free Software Foundation; either version 2 of the License, or
// 	(at your option) any later version.
// 
// 	Diacamma is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// 	GNU General Public License for more details.
// 
// 	You should have received a copy of the GNU General Public License
// 	along with Lucterios; if not, write to the Free Software
// 	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// 
// 		Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
// library file write by SDK tool
// --- Last modification: Date 11 November 2011 10:46:28 By  ---

//@BEGIN@

/**
 * fichier g�rant la gestion des extensions
 *
 * @author Pierre-Oliver Vershoore/Laurent Gay
 * @version 0.10
 * @package Lucterios
 * @subpackage Outils
 */

 /**
  * Configuration
  */
require_once("conf/cnf.inc.php");
 /**
  * connexion DB
  */
require_once("CORE/dbcnx.inc.php");
 /**
  * Parametres
  */
require_once("CORE/setup_param.inc.php");

/**
 * Retourne la liste des extensions
 * @param string $rootPath repertoire racine
 * @param bool $WithClient Inclure les clients
 * @param bool $WithDB recherche en base
 * @return array liste des repertoires par extensions
 */
function getExtensions($rootPath = '',$WithClient = false,$WithDB = false) {
	if (!$WithClient && $WithDB)
		return getExtensionsByDB($rootPath);
	$exts = array();
	if(is_dir($rootPath.'CORE'))
		$exts['CORE'] = $rootPath.'CORE/';
	$extDir = $rootPath."extensions/";
	if(is_dir($extDir)) {
		$dh = opendir($extDir);
		while(($file = readdir($dh)) != false) {
			if(($file[0] != '.') && is_dir($extDir.$file))
				$exts[$file] = $extDir.$file.'/';
		}
		closedir($dh);
	}
	if($WithClient) {
		$extDir = $rootPath."UpdateClients/";
		if( is_dir($extDir)) {
			$dh = opendir($extDir);
			while(($file = readdir($dh)) != false) {
				if(($file[0] != '.') && is_dir($extDir.$file))
					$exts[$file] = $extDir.$file.'/';
			} closedir($dh);
		}
		if( is_dir($rootPath.'SDK'))
			$exts['SDK'] = $rootPath.'SDK/';
	}
	return $exts;
}

/**
 * Retourne la liste des extensions en base
 * @param string $rootPath repertoire racine
 * @return array liste des repertoires par extensions
 */
function getExtensionsByDB($rootPath = '') {
	$exts = array();
	require_once("CORE/extension.tbl.php");
	$DBExtension=new DBObj_CORE_extension;
	$DBExtension->validite='o';
	$DBExtension->find();
	while ($DBExtension->fetch()) {
		$extName=$DBExtension->extensionId;
		if (is_dir($rootPath.$extName))
			$exts[$extName]=$rootPath.$extName.'/';
		else if (is_dir($rootPath."extensions/".$extName))
			$exts[$extName]=$rootPath."extensions/".$extName.'/';
	}
	return $exts;
}

/**
 * Retourne la liste des extensions en base triees
 * @param string $rootPath repertoire racine
 * @return array liste des repertoires par extensions
 */
function getExtensionsSorted($rootPath = '') {
	$extlist=getExtensionsByDB($rootPath);
	$set_of_ext = array();
	foreach($extlist as $name => $path) {
		$set_of_ext[] = new Extension($name,$path);
	}
	$set_of_ext = sortExtension($set_of_ext,"");
	$exts = array();
	foreach($set_of_ext as $ext)
		$exts[$ext->Name]=$extlist[$ext->Name];
	return $exts;
}

/**
 * Supprime un repertoire recursif
 * @param string $dirPath repertoire a supprimer
 */
function deleteDir($dirPath) {
	if( is_dir($dirPath)) {
		$dh = opendir($dirPath);
		while(($file = readdir($dh)) != false) {
			if(($file == '.') || ($file == '..'))continue;
			if( is_file($dirPath.'/'.$file)) unlink($dirPath.'/'.$file);
			elseif ( is_dir($dirPath.'/'.$file)) deleteDir($dirPath.'/'.$file);
		} closedir($dh); rmdir($dirPath);
	}
}

/**
* Classe de gestion d'extension
*
* @package Lucterios
* @subpackage Outils
* @author Pierre-Oliver Vershoore/Laurent Gay
*/
class Extension {

	private $version_max = 0;

	private $version_min = 0;

	private $version_release = 0;

	private $version_build = 0;

	private $init_DB_version = "0.0.0.0";

	private $description = "";

	private $depencies = array();

	private $rights = array();

	private $actions = array();

	private $menus = array();

	private $params = array();

	public $extend_tables = array();

	public $Name = "";

	public $Dir = "";

	public $ID = 0;

	public $message = "";

	public $titre = "";

	public $famille = "";

	public $Appli = "";

	public $throwExcept = false;

	/**
	 * Constructeur
	 * @param string $Name Nom de l'extension
	 * @param string $Dir repertoire de stockage
	 */
	public function __construct($Name,$Dir) {
		$this->Name = $Name;
		$this->Dir = $Dir;
		$this->read();
	}

	private function read() {
		if( is_file($this->Dir."setup.inc.php")) {
			$extend_tables = array();
			$extention_titre = "";
			$extention_famille = "";
			$extention_appli = "";
			require($this->Dir."setup.inc.php");
			$this->version_max = $version_max;
			$this->version_min = $version_min;
			$this->version_release = $version_release;
			$this->version_build = $version_build;
			$this->titre = $extention_titre;
			$this->famille = $extention_famille;
			$this->description = $extention_description;
			$this->depencies = $depencies;
			$this->rights = $rights;
			$this->actions = $actions;
			$this->menus = $menus;
			$this->params = $params;
			$this->extend_tables = $extend_tables;
			$this->Appli = $extention_appli;
			$this->init_DB_version=$this->getDBVersion();
		}
	}

	private function convertID($text) {
		$text = str_replace(array("'"," ",'"'),"",$text);
		$text = str_replace(array("�","�","�","�"),"e",$text);
		$text = str_replace(array("�","�","�","@","_APAS_"),"a",$text);
		$text = str_replace(array("�","�"),"i",$text);
		$text = str_replace(array("�","�"),"o",$text);
		$text = str_replace(array("�","�","�"),"u",$text);
		return $text;
	}

	/**
	 * Retourne liste des classes filles
	 * @param string $motherClass classe a rechercher
	 * @return array
	 */
	public function getDaughterClasses($motherClass) {
		$res = array();
		foreach($this->extend_tables as $key => $value) {
			if( is_array($value) && ($value[1] == $motherClass))
			    $res[$this->Name.'/'.$key] = $value[0];
			if( is_array($value) && ($value == $motherClass))
			    $res[$this->Name.'/'.$key] = $value;
		}
		return $res;
	}

	/**
	 * Retourne liste des tables referencees
	 * @param string $tableName table a rechercher
	 * @return array
	 */
	public function getReferenceTables($tableName) {
		$res = array();
		foreach($this->extend_tables as $key => $value) {
			if( is_array($value) && isset($value[2][$tableName]))
				$res[$this->Name.'_'.$key] = $value[2][$tableName];
		}
		return $res;
	}

	/**
	 * Recherche le chemin d'une extension
	 * @param string $ext Nom d'extension a rechercher
	 * @param string $root Repertoire racine
	 * @param bool $isClient Est un client
	 * @return string
	 */
	public static function getFolder($ext,$root = "",$isClient = false) {
		if($isClient) {
			if($ext == 'SDK')$pathext = $root.$ext."/";
			else $pathext = $root."UpdateClients/$ext/";
		}
		else {
			$pathext = $root.$ext."/";
			if(! is_dir($pathext))$pathext = $root."extensions/$ext/";
		}
		return $pathext;
	}

	/**
	 * Retourne la version base de l'extension
	 * @return string
	 */
	public function getDBVersion() {
		global $connect;
		$q = "SELECT versionMaj, versionMin, versionRev, versionBuild ";
		$q .= "FROM CORE_extension WHERE extensionId = '".$this->Name."'";
		$res = $connect->execute($q,$this->throwExcept);
		if(( trim($connect->errorMsg) == "") && (1 == $connect->getNumRows($res)))
		  return implode('.',$connect->getRow($res));
		else
		  return "0.0.0.0";
	}

	/**
	 * Retourne la version fichier de l'extension
	 * @return string
	 */
	public function getPHPVersion() {
		return $this->version_max.".".$this->version_min.".".$this->version_release.".".$this->version_build;
	}

	/**
	 * compare les version fichier et DB de l'extension
	 * @return int
	 */
	public function compareVersionPHP_DB() {
		return version_compare($this->getPHPVersion(),$this->getDBVersion());
	}

	/**
	 * Retourne les version DB+fichier de l'extension
	 * @param bool $current True=valeur DB courante/False=valeur DB initial
	 * @return string
	 */
	public function getVersions($current=true) {
		if ($current)
			return array($this->getDBVersion(),$this->getPHPVersion());
		else
			return array($this->init_DB_version,$this->getPHPVersion());
	}

	/**
	 * Verifie que le version DB est compris entre 2 valeur
	 * @param string $versMax Version maximal (type xx.yy)
	 * @param string $versMin Version minimal (type xx.yy)
	 * @return int
	 */
	public function isVersionsInRange($versMax,$versMin) {
		$version = $this->getDBVersion();
		$pos_p = strpos($version,'.');
		$pos_p = strpos($version,'.',$pos_p+1);
		$version = substr($version,0,$pos_p);
		$check_max = ( version_compare($version,$versMax)<=0);
		$check_min = ( version_compare($version,$versMin) >= 0);
		return ($check_max && $check_min);
	}

	/**
	 * Verifie les dependances
	 * @param string $Name Nom de l'extension a comparer en dependance
	 * @param string $rootPath Repertoire racine
	 * @param array $except Extensions a ignorer
	 * @param bool $ignoreOptionel Ignore les dependance optionnels
	 * @return bool
	 */
	public function isDepencies($Name,$rootPath = '',$except = array(),$ignoreOptionel=false) {
		foreach($this->depencies as $dep) {
			if($dep->name == $Name) {
				if ($ignoreOptionel)
					return !$dep->optionnal;
				else
					return true;
			}
			else if( in_array($dep->name,$except)) {
				$except[] = $dep->name;
				$current_obj = new Extension($dep->name, Extension:: getFolder($dep->name,$rootPath));
				return $current_obj->isDepencies($Name,$rootPath,$except);
			}
		}
		return false;
	}

	/**
	 * Retourne liste des extensions dependantes
	 * @param array $exclude Extensions a ignorer
	 * @param string $rootPath Repertoire racine
	 * @param bool $ignoreOptionel Ignore les dependance optionnels
	 * @return array
	 */
	public function getDependants($exclude = array(),$rootPath = '',$ignoreOptionel=false) {
		$excludes[] = $this->Name;
		$ext_dep = array();
		$ext_list = getExtensions($rootPath);
		foreach($ext_list as $current_name => $current_dir) {
			$current_obj = new Extension($current_name,$current_dir);
			$dep_a = $current_obj->isDepencies($this->Name,$rootPath,array(),$ignoreOptionel);
			if($dep_a)
				$ext_dep[] = $current_name;
		}
		return $ext_dep;
	}

	/**
	 * Retourne liste des extensions dont la courante est dependante
	 * @param string $rootPath Repertoire racine
	 * @param string $exclude_txt Ignore ces extensions
	 * @return string
	 */
	public function getDepencies($rootPath = '',$exclude_txt = '') {
		$text = "";
		foreach($this->depencies as $dep) {
			if (strpos($exclude_txt,$dep->name) === false) {
				$current_obj = new Extension($dep->name, Extension:: getFolder($dep->name,$rootPath));
				$text.=$current_obj->getDepencies($rootPath,$exclude_txt.$this->Name)." ";
				if( strpos($text,$dep->name) === false)
					$text.= $dep->name." ";
			}
		}
		return trim($text);
	}

	/**
	 * Verifie les dependances
	 * @param array $extList Liste des extensions a controler
	 * @return array
	 */
	public function getCheckedDependances($extList) {
		$resDep=array();
		foreach($this->depencies as $dep) {
			if (isset($extList[$dep->name])) {
				$depExt=$ext=new Extension($dep->name,$extList[$dep->name]);
				$error="";
				$versMax=$dep->version_majeur_max.'.'.$dep->version_mineur_max;
				$versMin=$dep->version_majeur_min.'.'.$dep->version_mineur_min;
				$version = $depExt->getPHPVersion();
				$pos_p = strpos($version,'.');
				$pos_p = strpos($version,'.',$pos_p+1);
				$version = substr($version,0,$pos_p);
				$check_max = ( version_compare($version,$versMax)<=0);
				$check_min = ( version_compare($version,$versMin) >= 0);
				if (!$check_max || !$check_min) {
					$error="$version # [$versMax;$versMin]";
				}
				$resDep[$dep->name]=array($error=='',$error);			  
			}
			else {
				if (!$dep->optionnal)
					$resDep[$dep->name]=array(false,"Inconnu");
			}
		}
		return $resDep;
	}

	public function insertion($first) {
		if(! is_dir($this->Dir))
		return 0;
		global $dbcnf;
		require_once"CORE/extension.tbl.php";
		$DBextension = new DBObj_CORE_extension;
		$DBextension->extensionId = $this->Name;
		$DBextension->find(false);
		$act = 'insertion';
		if($DBextension->fetch()) {
			$act = 'modification';
			if($first) {
				global $connect;
				$this->message .= "netoyage de la base pour mise a jour{[newline]}";
				$q = "DELETE FROM CORE_extension_actions WHERE extension='$DBextension->id'";
				$connect->execute($q,$this->throwExcept);
				if( trim($connect->errorMsg) != "")$this->message .= $connect->errorMsg."{[newline]}";
				$q = "DELETE FROM CORE_menu WHERE extensionId='$this->Name'";
				$connect->execute($q,$this->throwExcept);
				if( trim($connect->errorMsg) != "")$this->message .= $connect->errorMsg."{[newline]}";
			}
		}
		$DBextension->description = $this->description;
		$DBextension->versionMaj = $this->version_max;
		$DBextension->versionMin = $this->version_min;
		$DBextension->versionRev = $this->version_release;
		$DBextension->versionBuild = $this->version_build;
		$DBextension->validite = 'n';
		if($DBextension->id>0) {
			$DBextension->update();
		}
		else {
			$DBextension->extensionId = $this->Name;
			$DBextension->insert();
		}
		global $connect;
		if( $connect->isFailed())
			$this->message .= $connect->errorMsg."{[newline]}";
		else
			$this->message .= $act."de l'extension N�".$DBextension->id."{[newline]}";
		$this->ID = $DBextension->id;
		return $DBextension->id>0? true: false;
	}

	private function compareTable($tbl1,$tbl2){
		$ext_tbl1=$this->extend_tables[$tbl1][2];
		$ext_tbl2=$this->extend_tables[$tbl2][2];
		$tbl1_in_tbl2=isset($ext_tbl2[$this->Name."_".$tbl1]) || ($this->extend_tables[$tbl2][1]==$this->Name."/".$tbl1);
		$tbl2_in_tbl1=isset($ext_tbl1[$this->Name."_".$tbl2]) || ($this->extend_tables[$tbl1][1]==$this->Name."/".$tbl2);
		if ($tbl1_in_tbl2==$tbl2_in_tbl1)
			return 0;
		else if ($tbl1_in_tbl2)
			return -1;
		else
			return 1;
	}

	private $tbl_list=null;

	/**
	 * Retourne la liste des tables SQL (ou classe lucterios) de l'extension
	 * @return array
	 */
	public function getTableList() {
		if(! is_dir($this->Dir))
			return array();
		if (!is_array($this->tbl_list)) {
			global $dbcnf;
			$this->tbl_list = array();
			$dh = opendir($this->Dir);
			while(($file = readdir($dh)) != false) {
				if( is_file($this->Dir.$file) && ( substr($file,-8,8) == ".tbl.php")) {
					$tableName = substr($file,0,-8);
					$this->tbl_list[]=$tableName;
				}
			}
			foreach($this->tbl_list as $table_name)
				require_once($this->Dir.$table_name.".tbl.php");

			$Max = count($this->tbl_list);
			for($i = 0;$i<$Max-1;$i++) {
				$min = $i;
				for($j = $i+1;$j<$Max;$j++)
					if($this->compareTable($this->tbl_list[$j],$this->tbl_list[$min])<0)
						$min = $j;
				if($min != $i) {
					$x = $this->tbl_list[$i];
					$this->tbl_list[$i] = $this->tbl_list[$min];
					$this->tbl_list[$min] = $x;
				}
			}
			global $connect;
			$connect->printDebug("tbl_list:".print_r($this->tbl_list,true));
		}
		return $this->tbl_list;
	}

	/**
	 * Met a jours les tables SQL de l'extension
	 * @return bool
	 */
	public function updateTable() {
		$tbl_list=$this->getTableList();
		$success = true;
		foreach($tbl_list as $table_name) {
			$class_name = "DBObj_".$this->Name."_".$table_name;
			$obj = new $class_name();
			if($success) {
				$update_error = $obj->setup($this->throwExcept);
				$success = $update_error[0];
				$this->message .= $update_error[1];
			}
		}
		return $success;
	}

	/**
	 * Supprime toutes les contraintes de clefs etrangeres des tables de l'extension
	 * @return bool
	 */
	public function removeAllContraintsTable() {
		$tbl_list=$this->getTableList();
		$success = true;
		foreach($tbl_list as $table_name) {
			$class_name = "DBObj_".$this->Name."_".$table_name;
			$obj = new $class_name;
			if($success) {
				require_once("CORE/DBSetup.inc.php");
				$set_obj = new DBObj_Setup($obj);
				$set_obj->throwExcept=$this->throwExcept;
				$this->message .= $set_obj->RemoveAllContraints();
			}
		}
		return $success;
	}

	/**
	 * Met a jours les contraintes de clefs etrangeres des tables de l'extension
	 * @return bool
	 */
	public function upgradeContraintsTable() {
		$tbl_list=$this->getTableList();
		$success = true;
		foreach($tbl_list as $table_name) {
			$class_name = "DBObj_".$this->Name."_".$table_name;
			$obj = new $class_name;
			if($success) {
				require_once("CORE/DBSetup.inc.php");
				$set_obj = new DBObj_Setup($obj);
				$set_obj->throwExcept=$this->throwExcept;
				$this->message .= $set_obj->CheckContraints();
			}
		}
		return $success;
	}

	/**
	 * Met a jour les valeurs par defaut pour chaques tables SQL
	 * @return bool
	 */
	public function upgradeDefaultValueTable() {
		$tbl_list=$this->getTableList();
		$success = true;
		foreach($tbl_list as $table_name) {
			$class_name = "DBObj_".$this->Name."_".$table_name;
			$obj = new $class_name;
			if($success) {
				require_once("CORE/DBSetup.inc.php");
				$set_obj = new DBObj_Setup($obj);
				$set_obj->throwExcept=$this->throwExcept;
				$this->message .= $set_obj->refreshDefaultValues();
			}
		}
		return $success;
	}

	/**
	 * Met � jour les droits pour l'extension
	 * @return bool
	 */
	public function updateRights() {
		if(! is_dir($this->Dir))
			return 0;
		if($this->ID == 0)
			return 0;
		$success = true;
		global $dbcnf;
		require_once"CORE/extension_rights.tbl.php";
		require_once"CORE/group_rights.tbl.php";
		foreach($this->rights as $key => $r) {
			$DBrights = new DBObj_CORE_extension_rights;
			$DBrights->extension = $this->ID;
			$DBrights->rightId = $key;
			$DBrights->find(false);
			$act = 'insertion';
			if($DBrights->fetch()) {
				$act = 'modification';
			}
			$DBrights->description = $r->description;
			$DBrights->weigth = $r->weigth;
			if($DBrights->id>0) {
				$DBrights->update();
			}
			else {
				$DBrights->extension = $this->ID;
				$DBrights->rightId = $key;
				$DBrights->insert();
			}
			global $connect;
			if( $connect->isFailed()) {
				$this->message .= $act." du droit:".$r->description.$connect->errorMsg."{[newline]}";
				$success = false;
			}
			else {
				$this->message .= $act." du droit:".$r->description."{[newline]}";
				$DBgrouprights = new DBObj_CORE_group_rights;
				$ret = $DBgrouprights->CheckRight($DBrights->id,$DBrights->weigth);
				$this->message .= "Groupe-Right :$ret{[newline]}";
			}
		}
		return $success;
	}

	/**
	 * Met a jours les parametres de l'extensions
	 * @return bool
	 */
	public function updateParams() {
		if(! is_dir($this->Dir))
			return 0;
		if($this->ID == 0)
			return 0;

		$success = true;
		global $dbcnf;
		require_once"CORE/extension_params.tbl.php";
		$DBparams = new DBObj_CORE_extension_params;
		$DBparams->extensionId = $this->Name;
		$DBparams->find(false);
		while($DBparams->fetch()) {
			if(!array_key_exists($DBparams->paramName,$this->params))
				$DBparams->delete();
		}
		foreach($this->params as $key => $val) {
			$DBparams = new DBObj_CORE_extension_params;
			$DBparams->extensionId = $this->Name;
			$DBparams->paramName = $key;
			$DBparams->find(false);
			$act = "insertion";
			if($DBparams->fetch()) {
				$act = "modification";
			}
			$DBparams->description = $val->description;
			$DBparams->type = (int)$val->type;
			$DBparams->param = $val->getExtendToText( false);
			if($DBparams->id>0)
				$DBparams->update();
			else {
				$DBparams->extensionId = $this->Name;
				$DBparams->paramName = $key;
				$DBparams->value = $val->defaultvalue;
				$DBparams->insert();
			}
			global $connect;
			if( $connect->isFailed()) {
				$this->message .= "$act du param�tre:".$key." ".$connect->errorMsg."{[newline]}";
				$success = false;
			}
			else $this->message .= $act." du param�tre:".$key."{[newline]}";
		}
		return $success;
	}

	/**
	 * Met a jour les menus de l'extension
	 * @return bool
	 */
	public function updateMenu() {
		if(! is_dir($this->Dir))
		return 0;
		if($this->ID == 0)
		return 0;
		$success = true;
		global $connect;
		foreach($this->menus as $m) {
			$mid = $this->convertID($m->description);
			$pere = $this->convertID($m->pere);
			$q = "INSERT INTO CORE_menu(menuItemId, extensionId, action, pere, description, help, icon, shortcut, position, modal) ";
			$modal_txt = 'n';
			if($m->modal == 1)$modal_txt = 'o';
			$q .= "VALUES('$mid', '".$this->Name."', '".$m->act."', '".$pere."', '". str_replace("'","''",$m->description)."', '". str_replace("'","''",$m->help)."','".$m->icon."','".$m->shortcut."',".$m->position.",'$modal_txt')";
			$connect->execute($q,$this->throwExcept);
			if( trim($connect->errorMsg) != "") {
				$this->message .= "Error d'insertion de menu '$q':".$connect->errorMsg."{[newline]}";
				$success = false;
			}
			else $this->message .= "insertion du menu: ".$m->description."{[newline]}";
		}
		return $success;
	}

	/**
	 * Verifie les actions de l'extension et leur droit associe
	 * @return bool
	 */
	public function checkActions() {
		if(! is_dir($this->Dir))
			return 0;
		if($this->ID == 0)
			return 0;
		$success = true;
		global $dbcnf;
		require_once"CORE/extension_actions.tbl.php";
		require_once"CORE/extension_rights.tbl.php";
		foreach($this->actions as $act)if( file_exists($this->Dir.$act->action.".act.php")) {
			// recheche du droit a assicier
			$DBext_rights = new DBObj_CORE_extension_rights;
			$DBext_rights->extension = $this->ID;
			$DBext_rights->rightId = $act->rightNumber;
			$DBext_rights->find(false);
			$DBext_rights->fetch();
			// creation de l'action
			$DBaction = new DBObj_CORE_extension_actions;
			$DBaction->extension = $this->ID;
			$DBaction->action = $act->action;
			$DBaction->find(false);
			$ret = "insertion";
			if($DBaction->fetch()) {
				$ret = "modification";
			}
			$DBaction->extension = $this->ID;
			$DBaction->action = $act->action;
			$DBaction->description = str_replace("'","`",$act->description);
			$DBaction->rights = $DBext_rights->id;
			if($DBaction->id>0) {
				$DBaction->update();
			}
			else {
				$DBaction->extension = $this->ID;
				$DBaction->action = $act->action;
				$DBaction->insert();
			}
			global $connect;
			if( $connect->isFailed()) {
				$this->message .= $ret." de l'action:".$act->action." [".$connect->errorMsg."-$q]{[newline]}";
				$success = false;
			}
			else $this->message .= $ret." de l'action:".$act->action."{[newline]}";
		}
		else {
			$this->message .= "EXTENSION:$this->Name:{[newline]}fichier action".$act->action." non present, l'action ne sera pas referencee dans la DB{[newline]}";
			$success = false;
		}
		return $success;
	}

	/**
	 * Rafraichi les fonctions stoquees de l'extension
	 * @return bool
	 */
	public function checkStorageFunctions(){
		$this->message .= "checkStorageFunctions ";
		if(! is_dir($this->Dir))
			return 0;
		$this->message .= "--";
		$success = true;
		global $dbcnf;
		global $connect;

		$nb=0;
		$this->message .= " ".$this->Dir;
		$dh = opendir($this->Dir);
		while(($file = readdir($dh)) != false) {
			if( is_file($this->Dir.$file) && ( substr($file,-4,4) == ".fsk")) {
				$storageName = substr($file,0,-4);
				$storageFile = $this->Dir.$file;
				$SQL="";
				$contents=File($storageFile);
				$this->message .= "fonction stock�e:'$storageFile' (lines=".count($contents)."){[newline]}";
				foreach($contents as $line) {
					if ((substr($line,0,3)!='-- ') && (trim($line)!=''))
						$SQL.=rtrim($line)." ";
				}
				if ($SQL!='') {
					$connect->execute("DROP FUNCTION IF EXISTS ".$this->Name."_FCT_$storageName",$this->throwExcept);

					$connect->execute($SQL,$this->throwExcept);
					if( trim($connect->errorMsg) != "") {
						$this->message .= "Error d'insertion de fonction stock�es '$q':".$connect->errorMsg."{[newline]}";
						$success = false;
					}
					$nb++;
				}
			}
		}
		$this->message .= "Insertion de $nb fonction(s) stock�e(s){[newline]}";
		return $success;
	}

	/**
	 * verifie les model de raport d'impression de l'extension
	 * @return bool
	 */
	public function checkReportModel() {
		if(! is_dir($this->Dir))
		return 0;
		if($this->ID == 0)
		return 0;
		$success = true;
		require_once("CORE/printmodel.tbl.php");
		$model = new DBObj_CORE_printmodel;
		$model->extensionid = $this->Name;
		$model->find(false);
		while($model->fetch()) {
			$printfile = $this->Dir.$model->identify.".prt.php";
			if(! is_file($printfile)) {
				$model->delete();
				$this->message .= "Impression ".$model->identify." supprim�{[newline]}";
			}
		}
		$prt_list = array();
		$dh = opendir($this->Dir);
		while(($file = readdir($dh)) != false) {
			if( is_file($this->Dir.$file) && ( substr($file,-8,8) == ".prt.php")) {
				$modelName = substr($file,0,-8);
				array_push($prt_list,$modelName);
			}
		}
		require_once("ConvertPrintModel.inc.php");
		foreach($prt_list as $printmodel) {
			list($id,$model,$res) = checkDBModel($this->Name,$printmodel, true);
			if($id>0)$this->message .= "V�rification de l`impression ".$printmodel."($res){[newline]}";
			else {
				$this->message .= "Impression ".$printmodel." non valid{[newline]}";
				$success = false;
			}
		}
		return $success;
	}

	/**
	 * Valide une extension
	 * @return bool
	 */
	public function validation() {
		if(! is_dir($this->Dir))
		return 0;
		global $dbcnf;
		require_once"CORE/extension.tbl.php";
		$DBextension = new DBObj_CORE_extension;
		$DBextension->extensionId = $this->Name;
		if($DBextension->find(false) && $DBextension->fetch()) {
			$DBextension->titre = $this->titre;
			$DBextension->validite = 'o';
			$DBextension->update();
			$this->message .= "Validation de l'extension N�".$DBextension->id."{[newline]}";
		}
		else $this->message .= "Extension inconnue!{[newline]}";
		return $DBextension->id>0? true: false;
	}

	public function postInstall() {
		if( is_file($this->Dir."postInstall.inc.php")) {
			require_once($this->Dir."postInstall.inc.php");
			$func = "install_".$this->Name;
			if( function_exists($func)) {
				$this->message .= "appel de la propre fonction d'install de l'extension{[newline]}";
				$this->message .= $func($this->getVersions(false));
				$this->message .= "fin d'appel de la propre fonction d'install de l'extension{[newline]}";
			}
			return true;
		}
		return 0;
	}

	/**
	 * Appel la fonction "post-install" de l'application
	 * @return string
	 */
	public static function callApplicationPostInstallation($ExtensionDescription) {
		global $rootPath;
		$message = "";
		if( is_file($rootPath.'extensions/applis/postInstallation.inc.php'))
				require('extensions/applis/postInstallation.inc.php');
		if(function_exists('postInstallation'))
			$message .= postInstallation($ExtensionDescription);
		else
			$message .= 'Pas de post-installation{[newline]}';
		return $message;
	}

	/**
	 * Deroule la mise a jour integrale de l'extension
	 * @return string
	 */
	public function installComplete() {
		$nb = 0;
		$nb += $this->removeAllContraintsTable();
		$nb += $this->updateTable();

		try {
			$nb += $this->insertion( true);
			$insert = true;
		}
		 catch( Exception$e) {
			$insert = false;
		}
		$nb += $this->upgradeDefaultValueTable();
		$nb += $this->updateRights();
		$nb += $this->updateParams();
		$nb += $this->updateMenu();
		$nb += $this->checkReportModel();
		$nb += $this->checkActions();
		$nb += $this->upgradeContraintsTable();
		$nb += $this->checkStorageFunctions();
		if(!$insert)$nb += $this->insertion( false);
		$nb += $this->postInstall();
		$nb += $this->validation();
		return "$nb/10";
	}

	/**
	 * Supprime une extension (donnees et repertoire)
	 * @return bool
	 */
	public function delete() {
		if(! is_dir($this->Dir))
		return 0;
		if(($this->Name == 'CORE') || ($this->Name == 'applis'))
			throw new LucteriosExtension(2,'Extension non supprimable!');

		$temp_path = getcwd()."/tmp/delete/";
		if(is_dir($temp_path.$this->Dir))
			deleteDir($temp_path.$this->Dir);
		if(!is_dir($temp_path))
			mkdir($temp_path,0777, true);

		$ext_list = getExtensions($rootPath);
		foreach($ext_list as $current_name => $current_dir) {
			$current_obj = new Extension($current_name,$current_dir);
			$current_obj->throwExcept=$this->throwExcept;
			$current_obj->removeAllContraintsTable();
		}

		global $connect;
		require_once"CORE/extension.tbl.php";
		$DBextension = new DBObj_CORE_extension;
		$DBextension->extensionId = $this->Name;
		$DBextension->find(false);
		if($DBextension->fetch()) {
			$q = "DELETE FROM CORE_group_rights WHERE rightref IN(SELECT id FROM CORE_extension_rights WHERE extension='$DBextension->id')";
			$connect->execute($q,$this->throwExcept);
			if( trim($connect->errorMsg) != "")
				$this->message .= $connect->errorMsg."{[newline]}";
			$q = "DELETE FROM CORE_extension_actions WHERE extension='$DBextension->id'";
			$connect->execute($q,$this->throwExcept);
			if( trim($connect->errorMsg) != "")
				$this->message .= $connect->errorMsg."{[newline]}";
			$q = "DELETE FROM CORE_menu WHERE extensionId='$this->Name'";
			$connect->execute($q,$this->throwExcept);
			if( trim($connect->errorMsg) != "")
				$this->message .= $connect->errorMsg."{[newline]}";
			$q = "DELETE FROM CORE_extension_params WHERE extensionId='$this->Name'";
			$connect->execute($q,$this->throwExcept);
			if( trim($connect->errorMsg) != "")
				$this->message .= $connect->errorMsg."{[newline]}";
			$q = "DELETE FROM CORE_extension_rights WHERE extension='$DBextension->id'";
			$connect->execute($q,$this->throwExcept);
			if( trim($connect->errorMsg) != "")
				$this->message .= $connect->errorMsg."{[newline]}";
			$q = "DELETE FROM CORE_printmodel WHERE extensionId='$this->Name'";
			$connect->execute($q,$this->throwExcept);
			if( trim($connect->errorMsg) != "")
				$this->message .= $connect->errorMsg."{[newline]}";
			$q = "DELETE FROM CORE_finalreport WHERE extensionId='$this->Name'";
			$connect->execute($q,$this->throwExcept);
			if( trim($connect->errorMsg) != "")
				$this->message .= $connect->errorMsg."{[newline]}";
			$DBextension->delete();
		}
		@rename($this->Dir,$temp_path.$this->Dir);

		$tables=$this->getTableList();
		foreach($tables as $table) {
			$q = "DROP TABLE ".$this->Name."_".$table;
			$connect->execute($q,$this->throwExcept);
			if( trim($connect->errorMsg) != "")
				$this->message .= $connect->errorMsg."{[newline]}";
		}

		$ext_list = getExtensions($rootPath);
		foreach($ext_list as $current_name => $current_dir) {
			$current_obj = new Extension($current_name,$current_dir);
			$current_obj->throwExcept=$this->throwExcept;
			$current_obj->upgradeContraintsTable();
		}
	}
}

/**
  * Trie une liste d'extensions en fonction du lien de dependance (plus dependant au moins dependant)
  * @param array $extList list d'extension a trier
  * @param string $rootPath Repertoire racine
  * @return array
  */
function sortExtension($extList,$rootPath = '') {
	$res = $extList;
	$Max = count($res);
	for($i = 0;$i<$Max-1;$i++) {
		$min = $i;
		for($j = $i+1;$j<$Max;$j++)
			if(sort_function($res[$j],$res[$min],$rootPath)<0)
				$min = $j;
		if($min != $i) {
			$x = $res[$i];
			$res[$i] = $res[$min];
			$res[$min] = $x;
		}
	}
	return $res;
}

/**
  * Compare deux extensions en fonction du lien de dependance
  * @param array $ext1 extension 1
  * @param array $ext2 extension 2
  * @param string $rootPath Repertoire racine
  * @return int 0=pas de dependance / 1=ext1 depend de ext2 / -1=ext2 depend de ext1
  */
function sort_function($ext1,$ext2,$rootPath = '') {
	$dep1 = $ext1->isDepencies($ext2->Name,$rootPath);
	$dep2 = $ext2->isDepencies($ext1->Name,$rootPath);
	if($ext1->Name == 'applis')$res = +1;
	else if($ext2->Name == 'applis')$res = -1;
	else if($dep1 && !$dep2)$res = +1;
	else if($dep2 && !$dep1)$res = -1;
	else $res = 0;
	return $res;
}

/**
  * Creation de la base de donnees
  * Utilise la variable globale $dbcnf decrivant les parametres de DB MySQL
  * @param bool $DropDB supprime la base si elle existe
  * @param bool $ThrowExcept remonte une exception si erreur
  * @return string
  */
function createDataBase($DropDB = false,$ThrowExcept = false) {
	global $dbcnf;
	global $connect;
	$setupMsg = "";
	$dsn = $dbcnf['dbtype']."://".$dbcnf['dbuser'].":".$dbcnf['dbpass']."@".$dbcnf['dbhost'];
	if($connect->connected && $DropDB) {
		$connect->execute('DROP DATABASE '.$dbcnf['dbname'],$ThrowExcept);
		$setupMsg .= "Destruction de DB :".$dbcnf['dbname']." ".$connect->errorMsg."{[newline]}";
		$connect->connected = false;
		$connect->connect($dbcnf);
	}
	if(!$connect->connected) {
		$ret=DBCNX::createDataBase($dbcnf);
		if (is_string($ret)) {
			if ($ThrowExcept) {
				require_once("CORE/Lucterios_Error.inc.php");
				throw new LucteriosException(GRAVE,$ret." - DSN=$dsn");
			}
			$setupMsg .= "Echec de creation de DB :".$ret."{[newline]}";
		}
		else {
			$setupMsg .= "Creation de DB :".$dbcnf['dbname']."{[newline]}";
			$connect->connect($dbcnf);
		}
	}
	else $setupMsg .= "Base de donn�e existante.{[newline]}";
	return $setupMsg;
}

/**
  * Rafraichi la base de donnees
  * @param bool $noVersionControl Controle la version - Rafraichi que si different de la DB
  * @param sring $rootPath Repertoire racine
  * @return string
  */
function refreshDataBase($noVersionControl = false, $rootPath = '') {
	$install = '';
	$set_of_ext = array();
	$ext_list = getExtensions($rootPath,true);
	foreach($ext_list as $name => $dir)$set_of_ext[] = new Extension($name,$dir);
	$set_of_ext = sortExtension($set_of_ext);
	$ExtensionDescription = array();
	foreach($set_of_ext as $ext) {
		$install .= "{[center]}".$ext->Name."{[/center]}{[newline]}";
		if($noVersionControl || ($ext->compareVersionPHP_DB()>0)) {
			$ExtensionDescription[$ext->Name] = $ext->getVersions();
			$ext->installComplete();
		}
		else {
			$install .= "Module � jours{[newline]}";
			$ext->postInstall();
		}
		$install .= $ext->message;
		$install .= "{[newline]}";
	}
	$install .= "Finalisation d'installation:{[newline]}";
	$install .= Extension:: callApplicationPostInstallation($ExtensionDescription);
	return $install;
}

/**
  * Retourne la liste des classes filles
  * @param string $motherClass Classe mere initial
  * @param sring $rootPath Repertoire racine
  * @param bool $recursif Recherche recursif
  * @param bool $includeMother Ajoute la mere a la liste
  * @return array
  */
function getDaughterClassesList($motherClass,$rootPath = '',$recursif = false,$includeMother = false) {
	$ret = array();
	if($includeMother) {
		list($ext_name,$table_name) = explode('/',$motherClass);
		$table_name = trim($table_name);
		$path = Extension:: getFolder($ext_name,$rootPath);
		$current_obj = new Extension($ext_name,$path);
		$ret[$motherClass] = $table_name;
		foreach($current_obj->extend_tables as $tbl_nm => $values) {
			if($tbl_nm == $table_name)$ret[$motherClass] = $values[0];
		}
	}
	$ext_list = getExtensions($rootPath);
	foreach($ext_list as $current_name => $current_dir) {
		$current_obj = new Extension($current_name,$current_dir);
		$current_ret = $current_obj->getDaughterClasses($motherClass);
		foreach($current_ret as $key=>$val)
			$ret[$key]=$val;
	}
	if($recursif) {
		$tmp = $ret;
		foreach($tmp as $key => $item) {
			$sub_table_list = getDaughterClassesList($key,$rootPath, true);
			if( count($sub_table_list)>0)
				foreach($sub_table_list as $key=>$val) 
					$ret[$key]=$val;
		}
	}
	return $ret;
}

/**
  * Retourne la liste des tables referencees
  * @param string $tableName Table initial
  * @param sring $rootPath Repertoire racine
  * @return array
  */
function getReferenceTablesList($tableName,$rootPath="") {
	$ret = array();
	$ext_list = getExtensionsByDB($rootPath);
	foreach($ext_list as $current_name => $current_dir) {
		$current_obj = new Extension($current_name,$current_dir);
		$current_ret = $current_obj->getReferenceTables($tableName);
		$ret = array_merge($ret,$current_ret);
	}
	return $ret;
}

/**
  * Verifie et rafraichi eventuellement les fonctions stockees.
  * @param sring $rootPath Repertoire racine
  */
function checkExtensions($rootPath="") {
	global $dbcnf;
	global $connect;
	$q="SELECT CORE_FCT_extension_APAS_getExtDesc(id) as info FROM CORE_extension limit 0,1";
	try{
		$QId=$connect->execute($q,true);
		$row=$connect->getRow($QId);
		$refresh=!is_array($row);
	}catch(Exception $e) {
		$refresh=true;
	}
	if ($refresh) {
		logAutre("QId=$QId - Q=$q");
		$ext_list = getExtensions($rootPath);
		foreach($ext_list as $name => $dir)
			$set_of_ext[] = new Extension($name,$dir);
		$set_of_ext = sortExtension($set_of_ext);
		$connect->begin();
		try{
			foreach($set_of_ext as $ext) {
				$ext->upgradeContraintsTable();
				$ext->checkStorageFunctions();
				$ext->postInstall();
				logAutre($ext->Name.":".$ext->message);
			}
			$connect->commit();
		}catch(Exception $e) {
			$connect->rollback();
			throw $e;
		}
	}
	else
		logAutre("No refresh");
}

//@END@
?>
