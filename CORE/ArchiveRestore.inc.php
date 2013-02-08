<?php
// This file is part of Lucterios/Diacamma, a software developped by 'Le Sanglier du Libre' (http://www.sd-libre.fr)
// thanks to have payed a retribution for using this module.
// 
// Lucterios/Diacamma is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
// 
// Lucterios/Diacamma is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with Lucterios; if not, write to the Free Software
// Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// library file write by Lucterios SDK tool

//@BEGIN@
function createArchive($file_path)
{
	$temp_path = "./tmp/";
	if( is_file($file_path))
		@unlink($file_path);
	require_once("CORE/DBSetup.inc.php");
	require_once("CORE/extensionManager.inc.php");
	require_once("CORE/ArchiveTar.inc.php");
	$tar = new ArchiveTar($file_path,true);
	$dir_list = getExtensions();
	$archive_header = "-------\n";
	foreach($dir_list as $ext_name => $ext_path) {
		$q = '';
		$SQL_file_name=$temp_path."data_".$ext_name.".sql";
		$ext = new Extension($ext_name,$ext_path);
		foreach($ext->extend_tables as $table => $desc) {
			require_once($ext_path.$table.'.tbl.php');
			$class_name = 'DBObj_'.$ext_name.'_'.$table;
			$tbl = new $class_name;
			$setup = new DBObj_Setup($tbl);
			$q .= "-- Structure de la classe ".$ext_name."::$table\n";
			$q .= $setup->describeSQLTable( true)."\n";
			$q .= "-- Contenu de la classe ".$ext_name."::$table\n";
			$q .= $setup->extractSQLData()."\n\n";
		}
		if ($q!='') {
			/*$handle = @fopen($SQL_file_name, "w+");
			if ($handle) {
				@fwrite($handle,$q);
				@fclose($handle);
			}
			else
				throw new LucteriosException(IMPORTANT,"Fichier $SQL_file_name non crÃ©able!");
			$tar->addModify($SQL_file_name,'',$temp_path);
			@unlink($SQL_file_name);*/
			$tar->addString("data_".$ext_name.".sql",$q);
		}
		$archive_header.=$ext->Name.":";
		$archive_header.=$ext->getDBVersion().":";
		$archive_header.=$ext->titre.":";
		$archive_header.=$ext->Appli;
		$archive_header.="\n";
	}
	$archive_header.= "-------\n";
	$tar->addString("info.head",$archive_header);
	$tar->add("usr/");
}

function get_archive_info($temp_path) {
	$info_head = array();
	if (is_file($temp_path."info.head")) {
		$content = file($temp_path."info.head");
		foreach($content as $line)
			if ($line[0]!='-') {
				$items = explode(':',trim($line));
				$info_head[$items[0]]=$items;
			}
	}
	else {
		$dir_list = getExtensions($temp_path);
		foreach($dir_list as $ext_name => $ext_path) {
			$ext = new Extension($ext_name,$ext_path);
			$info_head[$ext->Name] = array($ext->Name,$ext->getPHPVersion(),$ext->titre,$ext->Appli);
		}
	}
	return $info_head;
}

function get_current_info() {
	$info_current = array();
	$dir_list = getExtensions();
	foreach($dir_list as $ext_name => $ext_path) {
		$ext = new Extension($ext_name,$ext_path);
		$info_current[$ext->Name] = array($ext->Name,$ext->getPHPVersion(),$ext->titre,$ext->Appli);
	}
	return $info_current;
}

function diff_info($info_head,$info_current) {
	if (array_key_exists("applis",$info_head) && array_key_exists("applis",$info_current) && ($info_head['applis'][3]!=$info_current['applis'][3]))
		$diff = "{[bold]}Application différente{[/bold]}{[newline]}";
	else {
		$diff = "";
		$module_supp = array();
		$module_diff = array();
		foreach($info_current as $ext_name => $ext_desc) {
			if (array_key_exists($ext_name,$info_head)){
				if (version_compare($info_head[$ext_name][1],$ext_desc[1])>0)
					$module_diff[]=$ext_desc[2];
			}
		}
		foreach($info_head as $ext_name => $ext_desc_arc)
			if (!array_key_exists($ext_name,$info_current))
				$module_supp[]=$ext_desc_arc[2];
		if (count($module_supp)>0) {
			$diff .= "{[bold]}Extension(s) à installer{[/bold]}{[newline]}";
			foreach($module_supp as $extname)
				$diff .= "- $extname{[newline]}";
		}
		if (count($module_diff)>0) {
			$diff .= "{[bold]}Extension(s) à mettre à jours{[/bold]}{[newline]}";
			foreach($module_diff as $extname)
				$diff .= "- $extname{[newline]}";
		}
	}
	return $diff;
}

function get_current_session() {
	require_once("CORE/sessions.tbl.php");
	$DBSes=new DBObj_CORE_sessions;
	$DBSes->valid='o';
	$DBSes->find();
	if ($DBSes->fetch())
		return "INSERT INTO CORE_sessions  (sid, uid, dtcreate, dtmod, valid, ip) VALUES ('".$DBSes->sid."', '".$DBSes->uid."', '".$DBSes->dtcreate."', '".$DBSes->dtmod."', 'o', '".$DBSes->ip."')";
	else
		return "";
}

function clearTables() {
	global $connect;
	$dir_list = getExtensions();
	foreach($dir_list as $ext_name => $ext_path) {
		$ext = new Extension($ext_name,$ext_path);
		$ext->throwExcept=true;
		$ext->removeAllContraintsTable();
	}
	foreach($dir_list as $ext_name => $ext_path) {
		$ext = new Extension($ext_name,$ext_path);
		$tables=$ext->getTableList();
		foreach($tables as $table) {
			$q = "DROP TABLE ".$ext->Name."_".$table;
			$connect->execute($q,true);
		}
	}
}

function restorData($temp_path) {
	global $connect;
	$dh = opendir($temp_path);
	while(($file = readdir($dh)) != false)
		if(substr($file,-4)=='.sql') {
			$query_txt = "";
			$SQL_file_name=$temp_path.$file;
			$handle = @fopen($SQL_file_name, "r");
			while ($handle && !feof($handle)) {
				$line = @fgets($handle);
				if(( substr( trim($line),0,2) != '--') && ( trim($line) != '')) {
					$line = trim($line);
					$query_txt .= " ".trim($line);
					if(((substr($line,-1) == ';') || (substr($line,-14) == 'CHARSET=latin1')) && ($query_txt != '')) {
						$addSQL=true;
						$query_txt=trim($query_txt);
						if (substr($query_txt,0,12)=='CREATE TABLE') {
							$cst_pos=strpos($query_txt,', CONSTRAINT');
							if ($cst_pos>0) {
								$par_pos=strrpos($query_txt,')');
								$query_txt=substr($query_txt,0,$cst_pos).substr($query_txt,$par_pos);
							}
						}
						if (substr($query_txt,-1) != ';')
							$query_txt.=';';
						$connect->execute($query_txt,true);
						$query_txt = '';
					}
				}
			}
			if ($handle)
				@fclose($handle);
		}
	closedir($dh);
	if(!$addSQL)
		throw new LucteriosException( IMPORTANT,'Données non trouvées!');
}

function restorArchive($file_path) {
	$status = "{[font color='red']}échouée{[/font]}";
	require_once("CORE/ArchiveTar.inc.php");
	require_once("CORE/Lucterios_Error.inc.php");
	require_once("CORE/extensionManager.inc.php");
	$temp_path = getcwd()."/tmp/restor/";
	if( is_dir($temp_path)) rmdir($temp_path);
	if(! is_dir($temp_path)) mkdir($temp_path,0777, true);
	global $connect;
	try {
		$tar = new ArchiveTar($file_path);
		$tar->extract($temp_path);
		$info_head = get_archive_info($temp_path);
		$info_current = get_current_info();
		$result = diff_info($info_head,$info_current);
		if ($result=='') {
			if (is_dir("./usr"))
				rename("./usr",$temp_path."old_usr");
			$res_sess=get_current_session();
			$connect->begin();
			clearTables();
			restorData($temp_path);
			refreshDataBase(true);
			if (is_dir($temp_path."usr"))
				rename($temp_path."usr","./usr");
			if ($res_sess!='')
				$connect->execute($res_sess,true);
			$connect->commit();
			$status = "terminer";
			$result = "Votre archive '$file_path' est maintenant réstaurée.";
			if (is_dir($temp_path."old_usr"))
				rmdir($temp_path."old_usr");
		}
        	} catch(Exception $e) {
		if (!is_dir("./usr") && is_dir($temp_path."old_usr"))
			rename($temp_path."old_usr","./usr");
		$connect->rollback();
		$result=str_replace(array("\n"),array('{[newline]}'),$e->getMessage());
	}
	rm_recursive($temp_path);
	return array($status, $result);
}
//@END@
?>
