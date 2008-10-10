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
// --- Last modification: Date 10 October 2008 10:39:49 By  ---

//@BEGIN@
require_once"setup_param.inc.php";
if( is_file("../applis/setup.inc.php"))require_once"../applis/setup.inc.php";
else require_once"../extensions/applis/setup.inc.php";
require_once"HelpTools.inc.php";

function showMenuItem($menu_item) {
	list($extension,$main_title,$list_menu) = $menu_item;
	if( count($list_menu)>0) {
		$item_text = "<a href='#' onclick='showClass(".'"'."$extension".'"'.",". count($list_menu).")' class='menuhead'>$main_title</a>
			<div id='$extension' style='visibility:hidden;height:5px;'>";
		foreach($list_menu as $desc)$item_text .= "<li><a href='HelpDefault.inc.php?extension=$extension&helpname=".$desc[0]."' target='MainFrame'>".$desc[1]."</a></li>";
		$item_text .= "</div>";
		echo$item_text;
	}
}
if( array_key_exists('mode',$_GET) && ($_GET['mode'] == 'xml')) { header("Content-Type: text/xml; charset=ISO-8859-1");
	$item_text .= "<MENU>\n";
	$MenuList = getMenuList();
	foreach($MenuList as $menu_item) {
		list($extension,$main_title,$list_menu) = $menu_item;
		if( count($list_menu)>0) {
			$item_text .= "\t<ITEM>\n\t\t<EXTENSION>$extension</EXTENSION>\n\t\t<TITLE>$main_title</TITLE>\n";
			foreach($list_menu as $desc) {
				$item_text .= "\t\t<SUBITEM>\n";
				$item_text .= "\t\t\t<NAME>".$desc[0]."</NAME>\n";
				$item_text .= "\t\t\t<TITLE>".$desc[1]."</TITLE>\n";
				$item_text .= "\t\t</SUBITEM>\n";
			}
			$item_text .= "\t</ITEM>\n";
		}
	}
	$item_text .= "</MENU>\n";
	echo$item_text;
}
else { header('Content-Type: text/html; charset=ISO-8859-1');
	echo"<html>
<head>
  <title>$extention_titre</title>
	<link rel='stylesheet' href='HelpStyleCSS.inc.php' />
<script language='javascript'>
function showClass(className, nblignes)
{
	var field_obj=document.getElementById(className);
	if (field_obj.style.visibility=='hidden')
	{
		field_obj.style.visibility='visible';
		field_obj.style.height=(25*nblignes)+'px';
	}
	else
	{
		field_obj.style.visibility='hidden';
		field_obj.style.height='5px';
	}
}
</script>
</head>
<body>
<table class='Main' width='100%'>
	<tr class='menu'>
        	<td class='menu' style='height: 40px;'>
		<h3>Sommaire</h3>
		</td>
	</tr>
	<tr class='menu'>
        	<td class='menu'>";
	$MenuList = getMenuList();
	foreach($MenuList as $menu_item)echo showMenuItem($menu_item);
	echo"</td>
	</tr>
	<tr>
        	<td style='height: 50px;'>
		<center>
		<a href='#' onclick='window.open(".'"'."HelpDefault.inc.php?mode=manual".'"'.",".'"'."Manuel".'"'.",".'"'."toolbar = 0, menubar = 1, location = 0, scrollbars = 1".'"'.")'>Imprimer le manuel</a>
		</center>
		</td>
	</tr>
	<tr class='pied'>
		<td colspan=2 class='pied'>
		<table border=0 cellspacing=0 cellpadding=0 width='100%'>
		<tr><td class='pied'>$extention_titre- Guide d'utilisation
                    </td></tr>
            	</table>
        		</td>
    	</tr>
</table>
</body>
</html>";
}

//@END@
?>
