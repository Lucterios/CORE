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
// --- Last modification: Date 05 August 2008 22:59:54 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Import grille
//@PARAM@ extension
//@PARAM@ action
//@PARAM@ textCVS


//@LOCK:0

function importGrid($Params)
{
if (($ret=checkParams("CORE", "importGrid",$Params ,"extension","action","textCVS"))!=null)
	return $ret;
$extension=getParams($Params,"extension",0);
$action=getParams($Params,"action",0);
$textCVS=getParams($Params,"textCVS",0);
try {
$xfer_result=new Xfer_Container_Custom("CORE","importGrid",$Params);
$xfer_result->Caption="Import grille";
//@CODE_ACTION@
require_once("CORE/import.inc.php");
$CVS_array=ConvertTextToCVS($textCVS);

$action=utf8_decode($action);
$label=new Xfer_Comp_LabelForm("lbl");
$label->setValue("Données");
$label->setLocation(0,0,1,1);
$xfer_result->addComponent($label);

$gridcvs=new Xfer_Comp_Grid('gridcvs');

if (count($CVS_array)>0)
{
$first=$CVS_array[0];
foreach($first as $key=>$val)
   $gridcvs->addHeader($key, $key);
foreach($CVS_array as $id=>$line)
  foreach($line as $key=>$value)
    $gridcvs->setValue($id, $key, $value);
}

$gridcvs->setLocation(0,1,1,1);
$xfer_result->addComponent($gridcvs);

$xfer_result->addAction(new Xfer_Action("_Valider","ok.png",$extension,$action,0,1));
$xfer_result->addAction(new Xfer_Action("_Annuler", "cancel.png"));
$xfer_result->m_context['textCVS']=$textCVS;
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
