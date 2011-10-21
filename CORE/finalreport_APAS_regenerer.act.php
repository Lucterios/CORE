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
// Action file write by SDK tool
// --- Last modification: Date 20 October 2011 22:12:30 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/finalreport.tbl.php');
//@TABLES@
//@XFER:print
require_once('CORE/xfer_printing.inc.php');
//@XFER:print@


//@DESC@Regénérer une impression
//@PARAM@ 
//@INDEX:print_report


//@LOCK:0

function finalreport_APAS_regenerer($Params)
{
$self=new DBObj_CORE_finalreport();
$print_report=getParams($Params,"print_report",-1);
if ($print_report>=0) $self->get($print_report);
try {
$xfer_result=&new Xfer_Container_Print("CORE","finalreport_APAS_regenerer",$Params);
$xfer_result->Caption="Regénérer une impression";
//@CODE_ACTION@
$file_root="extensions/".$self->extensionid."/";
$pos=strpos($self->identify,'_APAS_');
if ($pos===false) {
	require_once("CORE/Lucterios_Error.inc.php");
	throw new LucteriosException(IMPORTANT,"Regénération de l'impression impossible!");
}
$tableName=substr($self->identify,0,$pos);
$tablefile=$file_root.$tableName.".tbl.php";
$printfile=$file_root.$self->identify.".prt.php";
if (!is_file($tablefile) || !is_file($printfile)) {
	require_once("CORE/Lucterios_Error.inc.php");
	throw new LucteriosException(IMPORTANT,"Regénération de l'impression impossible!");
}
$paramName="";
$content=file($printfile);
foreach($content as $line) {
	if (substr($line,0,9)=='//@PARAM@') {
		$list=explode('=',substr($line,9));
		$paramName=trim($list[0]);
	}
}
if ($paramName=='') {
	require_once("CORE/Lucterios_Error.inc.php");
	throw new LucteriosException(IMPORTANT,"Regénération de l'impression impossible!");
}
$xfer_result->m_context["$paramName"]=$self->reference;
if ($xfer_result->showSelector(0)) {
	$DBPrint=new DBObj_CORE_finalreport;
	$DBPrint->get($print_report);
	$DBPrint->delete();

	$xfer_result->m_extension=$self->extensionid;
	$xfer_result->selectReport($self->identify,0,$xfer_result->m_context,"Regénération",WRITE_MODE_WRITE,$self->reference);
}
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
