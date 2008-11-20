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
// --- Last modification: Date 19 November 2008 0:28:22 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/extension_params.tbl.php');
//@TABLES@

//@DESC@
//@PARAM@ compName=0

function extension_params_APAS_getParamComponent(&$self,$compName=0)
{
//@CODE_ACTION@
require_once 'CORE/setup_param.inc.php';
eval('$params=array('.$self->param.');');
if (is_numeric($compName)) $compName='value';
$cmp=null;
switch ($self->type)
{
  case PARAM_TYPE_STR:
    if (array_key_exists("Multi",$params) && ($params['Multi']=='true'))
      $cmp=new Xfer_Comp_Memo($compName);
    else
      $cmp=new Xfer_Comp_Edit($compName);
    $cmp->setValue($self->value);
    break;
  case PARAM_TYPE_INT:
    $cmp=new Xfer_Comp_Float($compName,$params['Min'],$params['Max'],0);
    $cmp->setValue($self->value);
    break;
  case PARAM_TYPE_REAL:
    $cmp=new Xfer_Comp_Float($compName,$params['Min'],$params['Max'],$params['Prec']);
    $cmp->setValue($self->value);
    break;
  case PARAM_TYPE_BOOL:
    $cmp=new Xfer_Comp_Check($compName);
    $cmp->setValue($self->value=='o');
    break;
  case PARAM_TYPE_ENUM:
    $cmp=new Xfer_Comp_Select($compName);
    $select=array();
    foreach($params['Enum'] as $key=>$val)
       $select[$key]=$val;
    $cmp->setSelect($select);
    $cmp->setValue($self->value);
    break;
}
return $cmp;
//@CODE_ACTION@
}

?>
