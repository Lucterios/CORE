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
//  // Method file write by SDK tool
// --- Last modification: Date 05 December 2008 21:44:17 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/extension_params.tbl.php');
//@TABLES@

//@DESC@
//@PARAM@ compName

function extension_params_APAS_getParamComponentReadOnly(&$self,$compName)
{
//@CODE_ACTION@
require_once 'CORE/setup_param.inc.php';
if (is_numeric($compName)) $compName='value';
$cmp=new Xfer_Comp_LabelForm($compName);
switch ($self->type) {
  case PARAM_TYPE_BOOL:
 	$cmp->setValue($self->value=='o'?"Oui":"Non");
	break;
  case PARAM_TYPE_ENUM:
	eval('$params=array('.$self->param.');');
     $cmp->setValue($params['Enum'][$self->value]);
     break;
  default:
    	$cmp->setValue($self->value);
}
return $cmp;
//@CODE_ACTION@
}

?>
