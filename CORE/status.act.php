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
//  // Action file write by SDK tool
// --- Last modification: Date 23 August 2007 18:55:38 By Laurent GAY ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/extension.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Résumé
//@PARAM@ 


//@LOCK:0

function status($Params)
{
try {
$xfer_result=&new Xfer_Container_Custom("CORE","status",$Params);
$xfer_result->Caption='Résumé';
//@CODE_ACTION@

$lab=new Xfer_Comp_LabelForm('title');
$lab->setLocation(0,0,4);
$lab->setValue('{[center]}{[bold]}{[underline]}Etat de votre système{[/underline]}{[/bold]}{[/center]}');
$xfer_result->addComponent($lab);

$memo='';
$extpath="extensions";
if ($handle=opendir($extpath))
{
	while ($item=readdir($handle))
	{
		$memo.="dir:$item ";
		if (($item != ".") && ($item != "..") && is_dir("$extpath/$item") &&
is_file("$extpath/$item/status.inc.php"))
		{
			$memo.=" file existe ";
			require_once "$extpath/$item/status.inc.php";
			$function_name=$item."_status";
			if (function_exists($function_name))
			{
				$memo.="function existe";
				$function_name($xfer_result);
			}
		}
		$memo.="{[newline]}";
	}
	closedir($handle);
}
else
	$memo.=" bad $extpath {[newline]}";

//$labmemo=new Xfer_Comp_LabelForm('memo');
//$labmemo->setLocation(0,100,3);
//$labmemo->setValue($memo);
//$xfer_result->addComponent($labmemo);
$xfer_result->addAction(new Xfer_Action("_Fermer","close.png"));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
