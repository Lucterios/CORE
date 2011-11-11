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
// --- Last modification: Date 14 October 2009 22:36:13 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/extension.tbl.php');
require_once('CORE/extension_actions.tbl.php');
require_once('CORE/extension_rights.tbl.php');
require_once('CORE/group_rights.tbl.php');
require_once('CORE/groups.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Editer les droits d'un groupe
//@PARAM@ extension=0
//@INDEX:group


//@LOCK:2

function groups_APAS_editer($Params)
{
$extension=getParams($Params,"extension",0);
$self=new DBObj_CORE_groups();
$group=getParams($Params,"group",-1);
if ($group>=0) $self->get($group);

$self->lockRecord("groups_APAS_editer");
try {
$xfer_result=&new Xfer_Container_Custom("CORE","groups_APAS_editer",$Params);
$xfer_result->Caption="Editer les droits d'un groupe";
$xfer_result->m_context['ORIGINE']="groups_APAS_editer";
$xfer_result->m_context['TABLE_NAME']=$self->__table;
$xfer_result->m_context['RECORD_ID']=$self->id;
//@CODE_ACTION@
$img=new  Xfer_Comp_Image('img');
$img->setValue('group.png');
$img->setLocation(0,0,1,3);
$xfer_result->addComponent($img);

$xfer_result->setDBObject($self,2,true,0,2);

$lbl=new Xfer_Comp_LabelForm('lblExtension');
$lbl->setValue('{[bold]}Extension{[/bold]}');
$lbl->setLocation(0,3,2);
$xfer_result->addComponent($lbl);

$q="SELECT * FROM CORE_extension WHERE id in (SELECT extension FROM CORE_extension_rights) ORDER BY titre";
$DBExt=new DBObj_CORE_extension;
$DBExt->Query($q);
$list=array();
while ($DBExt->fetch()) {
	$list[$DBExt->id]=$DBExt->titre;
	if ($extension==0)
		$extension=$DBExt->id;
}

$sel=new Xfer_Comp_Select('extension');
$sel->setSelect($list);
$sel->setValue($extension);
$sel->setLocation(2,3,2);
$sel->setAction($xfer_result->getRefreshAction());
$xfer_result->addComponent($sel);

$Q="SELECT CORE_group_rights.*
FROM CORE_group_rights,CORE_extension_rights
WHERE CORE_group_rights.rightref=CORE_extension_rights.id
AND CORE_group_rights.groupref=$group
AND CORE_extension_rights.extension=$extension
ORDER BY CORE_extension_rights.description";
$group_rights=new DBObj_CORE_group_rights;
$group_rights->query($Q);

$grid=$group_rights->getGrid($Params);
$grid->setLocation(0,5,6);
$xfer_result->addComponent($grid);

$xfer_result->addAction($self->NewAction("_Fermer","close.png",""));
//@CODE_ACTION@
	$xfer_result->setCloseAction(new Xfer_Action('unlock','','CORE','UNLOCK',FORMTYPE_MODAL,CLOSE_YES,SELECT_NONE));
}catch(Exception $e) {
	$self->unlockRecord("groups_APAS_editer");
	throw $e;
}
return $xfer_result;
}

?>
