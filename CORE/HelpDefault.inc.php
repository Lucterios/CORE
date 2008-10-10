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
// --- Last modification: Date 10 October 2008 10:44:15 By  ---

//@BEGIN@
require_once"setup_param.inc.php";
if( is_file("../applis/setup.inc.php"))require_once"../applis/setup.inc.php";
else require_once"../extensions/applis/setup.inc.php";
$version_appli = "$version_max.$version_min.$version_release.$version_build";
require_once"HelpTools.inc.php";
$num_page = 0;

function showPied($includeAll = false) {
	global $extention_description;
	global $extention_titre;
	$pied_text = "<tr class='pied'>
		<td colspan=2 class='pied'>
			<table border=0 cellspacing=0 cellpadding=0 width='100%'>
				<tr>
					<td class='pied'>$extention_titre- Guide d'utilisation";
	if($includeAll) {
		global $num_page;
		$num_page++;
		$pied_text .= "- Page$num_page-";
	}
	$pied_text .= "</td>
				</tr>
            		</table>
        		</td>
    	</tr>";
	return $pied_text;
}

function showJump() {
	return "<div style='page-break-before: always;'><!--[if IE 7]><br style='height:0; line-height:0'><![endif]--></div>";
}

function addJumpPage($includeAll = false) {
	$HtmlText = "</td></tr>
	</table>
	</td></tr>";
	$HtmlText .= showPied($includeAll);
	$HtmlText .= "</table>";
	$HtmlText .= showJump();
	$HtmlText .= "<table class='Main' width='100%'>
	<tr class='corps'>
	<td class='corps'>
	<table class='help'>
	<tr class='content'><td class='content'>";
	return $HtmlText;
}

function showHelpPage($extension,$helpname,$title = '',$includeAll = false) {
	global $extention_description;
	global $extention_titre;
	global $version_appli;
	$HtmlText = "<table class='Main' width='100%'>
	<tr class='corps'>
	<td class='corps'>";
	if(($extension != '') && ($helpname != '')) {
		$content = showHelpContent($extension,$helpname,$includeAll);
		if($includeAll)while(($pos = strpos($content,"<JUMP_PAGE/>")) !== false) {
			$HtmlText .= substr($content,0,$pos);
			$HtmlText .= addJumpPage( true);
			$content = substr($content,$pos+12);
		}
		$HtmlText .= $content;
	}
	else if($extension != '')$HtmlText .= "<div class='title'><br><br><br><br><a name='$extension'>$title</a></div>";
	else {
		$HtmlText .= "<div class='title'><br><br>$extention_titre<br>
		<font size='-1'>$extention_description</font><br>";
		$HtmlText .= "Guide d'utilisation<br>";
		if($includeAll)$HtmlText .= "<br><br><br><br>";
		if( is_dir('../applis/images/')) {
			if( is_file('../applis/images/image.jpg'))$HtmlText .= "<img src='../applis/images/image.jpg'/><br>";
			else $HtmlText .= "<img src='../applis/images/logo.gif'/><br>";
		}
		else {
			if( is_file('../extensions/applis/images/image.jpg'))$HtmlText .= "<img src='../extensions/applis/images/image.jpg'/><br>";
			else $HtmlText .= "<img src='../extensions/applis/images/logo.gif'/><br>";
		}
		$HtmlText .= "<br><br><br><font size='-1'>Version ".$version_appli."</font></div>";
	}
	$HtmlText .= "</td>
	</tr>";
	$HtmlText .= showPied($includeAll);
	$HtmlText .= "</table>";
	return $HtmlText;
} header('Content-Type: text/html; charset=ISO-8859-1');
echo"<html>
	<head>
		<title>$extention_description</title>
		<link rel='stylesheet' href='HelpStyleCSS.inc.php' />
	</head>
	<body>";
if( array_key_exists('mode',$_GET) && ($_GET['mode'] == 'manual')) {
	global $num_page;
	echo showHelpPage('','','', true);
	$MenuList = getMenuList( true);
	echo showJump();
	echo"<table class='Main' width='100%'>
	<tr class='corps'>
	<td class='corps'>";
	echo"<div class='title'><br>Sommaire</div><br><br>\n<ul>\n";
	$chapiters = array();
	$chapiter_pages = array();
	$summary_page = $num_page;
	$num_page++;
	foreach($MenuList as $menu_item) {
		list($extension,$main_title,$list_menu) = $menu_item;
		if($extension != 'applis') {
			$chapiter_pages[$extension] = $num_page+1;
			$chapiters[] = showHelpPage($extension,'',$main_title, true);
		}
		if( count($list_menu)>0)foreach($list_menu as $desc) {
			$chapiter_pages[$extension."@".$desc[0]] = $num_page+1;
			$chapiters[] = showHelpPage($extension,$desc[0],'', true);
		}
	}
	$num_page = $summary_page;
	foreach($MenuList as $menu_item) {
		list($extension,$main_title,$list_menu) = $menu_item;
		echo"\t<li><table width='100%'><tr><td><a href='#$extension'>".$main_title."</a></td><td align='right'>".$chapiter_pages[$extension]."&nbsp;&nbsp;&nbsp;</td></tr></table>\n\t\t<ul>\n";
		if( count($list_menu)>0)foreach($list_menu as $desc)echo"\t\t\t<li><table width='100%'><tr><td><a href='#".$extension."_APAS_".$desc[0]."'>".$desc[1]."</a></td><td align='right'>".$chapiter_pages[$extension."@".$desc[0]]."&nbsp;&nbsp;&nbsp;</td></tr></table></li>\n";
		echo"\t\t</ul>\n\t</li>\n";
	}
	echo"</ul>\n";
	echo"</td>
	</tr>";
	echo showPied( true);
	echo"</table>";
	foreach($chapiters as $chapiter) {
		echo showJump();
		echo$chapiter;
	}
	/*	foreach($MenuList as $menu_item)
	{
		list($extension,$main_title,$list_menu)=$menu_item;
		if ($extension!='applis')
		{
			echo showJump();
			echo showHelpPage($extension,'',$main_title,true);
		}
		if (count($list_menu)>0)
			foreach($list_menu as $desc)
			{
				echo showJump();
				echo showHelpPage($extension,$desc[0],'',true);
			}
	}*/
}
else {
	if( array_key_exists('extension',$_GET) && array_key_exists('helpname',$_GET))echo showHelpPage($_GET['extension'],$_GET['helpname']);
	else echo showHelpPage('','');
}
echo"	</body>
</html>";

//@END@
?>
