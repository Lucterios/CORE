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
// --- Last modification: Date 13 January 2009 19:14:44 By  ---

//@BEGIN@
require_once("setup_param.inc.php");
require_once("../extensions/applis/setup.inc.php");
require_once("HelpTools.inc.php");

function showMenuItem($menu_item) {
	list($extension,$main_title,$list_menu) = $menu_item;
	if( count($list_menu)>0) {
		$item_text = "<tr name='0' class='menurow'>\n\t<td valign=top><img id='$extension' src='../images/closed.png' border=0></td>\n\t<td colspan=2>\n";
		$item_text.= "\t\t<a href='#' onclick='showClass(".'"'."$extension".'"'.")' class='menuhead'>$main_title</a>\n";
		$item_text.= "\t</td>\n</tr>\n";
		foreach($list_menu as $desc) {
			$item_text.= "\t<tr name='$extension' class='menurow' style='display:none'>\n\t\t<td valign=top></td>\n\t\t<td valign=top><img src='../images/topic.png' border=0></td>\n\t\t<td>\n";
			$item_text.= "\t\t\t<a href='HelpDefault.inc.php?extension=$extension&helpname=".$desc[0]."' target='MainFrame'>".$desc[1]."</a>\n";
			$item_text.= "\t\t</td>\n</tr>\n";
		}
		echo $item_text;
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
function showClass(className)
{
	var table_menu=document.getElementById('menuList');
	var lines=table_menu.getElementsByTagName('tr');
	for (i=0; i<lines.length; i++) {
		var item_name=lines[i].getAttribute('name');
		if (item_name=='0') {
			lines[i].style.display='block';
			var imgs=lines[i].getElementsByTagName('img');
			if (imgs[0].getAttribute('id')==className)
				imgs[0].src='../images/open.png';
			else
				imgs[0].src='../images/closed.png';
		}
		else {
			if (item_name==className)
				lines[i].style.display='block';
			else
				lines[i].style.display='none';
		}
	}

}
</script>
</head>
<body>
<table class='Main' width='100%'>
	<tr class='menu'>
        	<td class='menu' style='height: 40px;'>
		<h3><a href='HelpDefault.inc.php' target='MainFrame' onclick='showClass(\"\")'>Sommaire</a></h3>
		</td>
	</tr>
	<tr class='menu'>
        	<td class='menu'>\n<table id='menuList'>\n";
	$MenuList = getMenuList();
	foreach($MenuList as $menu_item)
		echo showMenuItem($menu_item);
	echo"\n</table>\n</td>
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
