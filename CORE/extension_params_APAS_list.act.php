<?php
// Action file write by SDK tool
// --- Last modification: Date 17 June 2008 22:42:37 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/extension.tbl.php');
require_once('CORE/extension_params.tbl.php');
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Liste des paramètres généraux de l`application
//@PARAM@ 


//@LOCK:0

function extension_params_APAS_list($Params)
{
$self=new DBObj_CORE_extension_params();
try {
$xfer_result=&new Xfer_Container_Custom("CORE","extension_params_APAS_list",$Params);
$xfer_result->Caption="Liste des paramètres généraux de l`application";
//@CODE_ACTION@
$posY = 0;
$DBObjextension = new DBObj_CORE_extension;
$DBObjextension->find();
while($DBObjextension->fetch()) {
	$extension_Id = $DBObjextension->extensionId;
	$self = new DBObj_CORE_extension_params();
	$self->extensionId = $extension_Id;
	$self->orderBy("paramName");
	if($self->find()>0) {
		$lbl = & new Xfer_Comp_LabelForm('Lbl_'.$extension_Id);
		$lbl->setValue('{[underline]}{[bold]}Paramètres de "'.$DBObjextension->titre.'"{[/bold]}{[/underline]}');
		$lbl->setLocation(0,$posY++);
		$xfer_result->addComponent($lbl);
		require_once'CORE/setup_param.inc.php';
		$grid = & new Xfer_Comp_Grid('paramid_'.$extension_Id);
		$grid->addHeader('description',"Description");
		$grid->addHeader('value',"Valeur");
		while($self->fetch()) {
			eval('$params=array('.$self->param.');');
			$grid->setValue($self->id,'description',$self->description);
			if($self->type == PARAM_TYPE_ENUM) {
				$enum = $params['Enum'];
				$grid->setValue($self->id,'value',$enum[$self->value]);
			}
			else $grid->setValue($self->id,'value',$self->value);
		}
		$grid->setDBObject($self,array('description','value'));
		$grid->addAction($self->NewAction("_Modifier",'edit.png','modifier', FORMTYPE_MODAL, CLOSE_NO, SELECT_SINGLE));
		$grid->setLocation(0,$posY++);
		$xfer_result->addComponent($grid);
	}
}
$xfer_result->addAction($self->NewAction("_Fermer",'close.png'));
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
