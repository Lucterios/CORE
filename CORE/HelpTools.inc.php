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
// --- Last modification: Date 13 January 2009 19:16:04 By  ---

//@BEGIN@
function getExtensionPath($extension) {
	if($extension == "CORE")
	return "../CORE";
	if($extension == "applis")
		return "../extensions/applis";
	return "../extensions/$extension";
}

function getHelpDescriptions($extension) {
	$ext_path = getExtensionPath($extension);
	$help_menu = $ext_path."/help/menu.hlp.php";
	if( is_file($help_menu)) {
		$HelpTitle = "";
		$HelpPosition = -1;
		$HelpDescriptions = array();
		require$help_menu;
		if($HelpTitle == '') {
			require$ext_path."/setup.inc.php";
			$HelpTitle = $extention_description;
		}
		return array($HelpTitle,$HelpDescriptions,$HelpPosition);
	}
	return array( null, null,-1);
}

function getHelpTitle($extension,$helpname) {
	list($main_title,$descripts) = getHelpDescriptions($extension);
	if(! is_null($descripts))foreach($descripts as $desc)if($desc[0] == $helpname)
	return $desc[1];
	return "";
}

function getHelpContent($extension,$HelpFile,$includeAll = false) {
	$content = file($HelpFile);
	$str_content = "";
	foreach($content as $line) {
		$line = str_replace("\t",'&nbsp;&nbsp;&nbsp;',$line);
		while(($pos = strpos($line,"href='~")) !== false) {
			$sub_line = substr($line,$pos);
			$pos1 = strpos($sub_line,'~');
			$pos2 = strpos($sub_line,'$');
			if(($pos1 !== false) && ($pos2 !== false)) {
				$ext = substr($sub_line,$pos1+1,$pos2-$pos1-1);
				if($ext == '')$ext = $extension;
				if($includeAll)$line = substr($line,0,$pos)."href='#".$ext."_APAS_". substr($line,$pos+$pos2+1);
				else $line = substr($line,0,$pos)."href='HelpDefault.inc.php?extension=$ext&helpname=". substr($line,$pos+$pos2+1);
			}
		}
		while(($pos = strpos($line,"img src='~")) !== false) {
			$sub_line = substr($line,$pos);
			$pos1 = strpos($sub_line,'~');
			$pos2 = strpos($sub_line,'$');
			if(($pos1 !== false) && ($pos2 !== false)) {
				$ext = substr($sub_line,$pos1+1,$pos2-$pos1-1);
				if($ext == '')$ext = $extension;
				$help_img = getExtensionPath($ext);
				$help_img .= "/help/";
				$line = substr($line,0,$pos)."img src='$help_img". substr($line,$pos+$pos2+1);
			}
		}
		$str_content .= "$line";
	}
	return $str_content;
}

function showHelpContent($extension,$helpname,$includeAll = false) {
	$ext_path = getExtensionPath($extension);
	$help_file = "$ext_path/help/$helpname.xhlp";
	if( is_file($help_file)) {
		$Title = getHelpTitle($extension,$helpname);
		$Content = getHelpContent($extension,$help_file,$includeAll);
	}
	else {
		$Title = "<div class='error'>ERREUR</div>";
		$Content = "<div class='error'>Fichier d'aide '$extension>$helpname' non trouvé!</div>";
	}
	return "<table class='help'>
	<tr class='title'><td class='title'><a name='".$extension."_APAS_".$helpname."'>$Title</a></td></tr>
	<tr class='content'><td class='content'><hr></td></tr>
	<tr class='content'><td class='content'>$Content</td></tr>
    </table>";
}

function addItemInList($extension,&$menu,$includeAll = false) {
	list($main_title,$descripts,$pos) = getHelpDescriptions($extension,$includeAll);
	$pos = (int)$pos;
	if(! is_null($main_title) && ($pos >= 0)) {
		$position = sprintf("%'05d",$pos);
		$list_menu = array();
		foreach($descripts as $key => $desc)
			if($includeAll || ($desc[2] != 0))
				$list_menu[$key] = $desc;
		if( count($list_menu)>0)
			$new_menu = array($extension,$main_title,$list_menu);
		$menu[$position] = $new_menu;
	}
}

function getMenuList($includeAll = false) {
	$MenuList = array(); addItemInList('applis',$MenuList,$includeAll);
	$ext_path = "../extensions";
	if($handle = opendir($ext_path)) {
		while(false !== ($item = readdir($handle)))
			if(($item != ".") && ($item != "..") && ($item != "applis") && is_dir("$ext_path/$item")) 
				addItemInList($item,$MenuList,$includeAll); 
		closedir($handle);
	} 
	addItemInList('CORE',$MenuList,$includeAll); 
	ksort($MenuList);
	return $MenuList;
}
//@END@
?>
