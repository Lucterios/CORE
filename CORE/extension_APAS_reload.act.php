<?php
//
//  This file is part of Lucterios.
//
//  Lucterios is free software; you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation; either version 2 of the License, or
//  (at your option) any later version.
//
//  Lucterios is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with Lucterios; if not, write to the Free Software
//  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
//
//	Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
//
// Action file write by SDK tool
// --- Last modification: Date 17 June 2008 22:39:38 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/extension.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Recharger les configurations
//@PARAM@ 


//@LOCK:0

function extension_APAS_reload($Params)
{
$self=new DBObj_CORE_extension();
try {
$xfer_result=&new Xfer_Container_Custom("CORE","extension_APAS_reload",$Params);
$xfer_result->Caption="Recharger les configurations";
//@CODE_ACTION@
require_once"CORE/extensionManager.inc.php";
$install = "";
$extlist = getExtensions();
$set_of_ext = array();
foreach($extlist as $name => $path) {
	$set_of_ext[] = new Extension($name,$path);
}
$set_of_ext = sortExtension($set_of_ext,"");
$ExtensionDescription = array();
foreach($set_of_ext as $ext) {
	$install .= "{[center]}{[bold]}".$ext->Name."{[/bold]}{[/center]}";
	$ExtensionDescription[$ext->Name] = $ext->getVersions();
	$ext->installComplete();
	$install .= $ext->message;
}
$install .= Extension:: callApplicationPostInstallation($ExtensionDescription);
$lbl = new Xfer_Comp_LabelForm("info");
$lbl->setLocation(1,0);
$lbl->setValue($install);
$xfer_result->addComponent($lbl);
$xfer_result->addAction( new Xfer_Action('_Fermer','close.png','','', FORMTYPE_MODAL, CLOSE_YES));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
