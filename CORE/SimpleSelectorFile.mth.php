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
// --- Last modification: Date 15 October 2009 20:09:17 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
//@TABLES@

//@DESC@
//@PARAM@ path
//@PARAM@ FileExt
//@PARAM@ readOnly

function SimpleSelectorFile(&$self,$path,$FileExt,$readOnly)
{
//@CODE_ACTION@
if(!is_dir($path)) {
	mkdir($path);
}
$self->m_context['path'] = $path;

$file_list = array();
$handle = opendir($path);
while( false !== ($file = readdir($handle))) {
	if( is_file($path.$file) && is_writable($path.$file) && ( substr($file,-1*( strlen($FileExt)+1)) == '.'.$FileExt))
		$file_list[] = $file;
}
closedir($handle);

sort($file_list);
$temp = $file_list;
$file_list = array('');
foreach($temp as $item)
	$file_list[$item] = date("[d F Y H:i:s] ", filectime($path.$item)).$item;

$lb_name = new Xfer_Comp_LabelForm('lblfiles');
$lb_name->setValue('{[bold]}Fichiers{[/bold]}');
$lb_name->setLocation(1,3,2);
$self->addComponent($lb_name);
$select = new Xfer_Comp_CheckList('files');
$select->setSelect($file_list);
$select->simple = true;
$select->setValue('');
$select->setLocation(1,4,2);
$select->setSize(150,300);
$select->JavaScript = "
try {
	var type=current.getValue();
	var new_text='<TEXT><![CDATA['+type+']]></TEXT>';
	parent.get('filename').setValue(new_text);
} catch(e){}
";
if($readOnly)
	$select->JavaScript .= "parent.get('filename').setEnabled(false);";
$self->addComponent($select);
//
$lb_name = new Xfer_Comp_LabelForm('lblfilename');
$lb_name->setValue('{[bold]}Fichier{[/bold]}');
$lb_name->setLocation(1,5);
$self->addComponent($lb_name);
$edit_name = new Xfer_Comp_Edit('filename');
$edit_name->setValue('');
$edit_name->needed = True;
$edit_name->setLocation(2,5);
$self->addComponent($edit_name);
//@CODE_ACTION@
}

?>
