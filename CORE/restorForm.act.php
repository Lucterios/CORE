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
// --- Last modification: Date 09 January 2010 13:31:50 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
//@TABLES@
//@XFER:custom
require_once('CORE/xfer_custom.inc.php');
//@XFER:custom@


//@DESC@Restauration de données
//@PARAM@ file_path


//@LOCK:0

function restorForm($Params)
{
if (($ret=checkParams("CORE", "restorForm",$Params ,"file_path"))!=null)
	return $ret;
$file_path=getParams($Params,"file_path",0);
try {
$xfer_result=&new Xfer_Container_Custom("CORE","restorForm",$Params);
$xfer_result->Caption="Restauration de données";
//@CODE_ACTION@
if(isset($xfer_result->m_context['RESTOR'])) {
	$img_title = new Xfer_Comp_Image('img_title');
	$img_title->setLocation(0,0,1,2);
	$img_title->setValue('backup_restor.png');
	$xfer_result->addComponent($img_title);
	$lbl = new Xfer_Comp_LabelForm("info");
	$lbl->setLocation(1,0);
	$xfer_result->addComponent($lbl);
	//
	$temp_path = getcwd()."/tmp/restor/";
	if( is_dir($temp_path)) rmdir($temp_path);
	if(! is_dir($temp_path)) mkdir($temp_path,0777, true);
	//
	PEAR::setErrorHandling(PEAR_ERROR_EXCEPTION);
	require_once("Archive/Tar.php");
	require_once("CORE/Lucterios_Error.inc.php");
	$tar = new Archive_Tar($file_path);
	$result = $tar->extract($temp_path);
	$items = array("CORE/","extensions/","usr/","images/","index.php","coreIndex.php","install.php","Help.php");
	foreach($items as $item) {
		if( substr($item,-1) == "/") {
			$item = substr($item,0,-1);
			if(! is_dir($temp_path.$item))
				throw new LucteriosException( IMPORTANT,"Répértoire ".$temp_path.$item." non trouvé!");
		}
		else if(! is_file($temp_path.$item)) {
			throw new LucteriosException( IMPORTANT,"Fichier ".$temp_path.$item." non trouvé!");
		}
	}
	global $connect;
	$connect->begin();
	try {

		require_once("CORE/extensionManager.inc.php");
		$ext_list = getExtensions();
		foreach($ext_list as $current_name => $current_dir) {
			$current_obj = new Extension($current_name,$current_dir);
			$current_obj->throwExcept=true;
			$current_obj->removeAllContraintsTable();
		}
		$addSQL=false;
		$dh = opendir($temp_path);
		while(($file = readdir($dh)) != false)
			if(substr($file,-4)=='.sql') {
				$query_txt = "";
				$SQL_file_name=$temp_path.$file;
				$handle = @fopen($SQL_file_name, "r");
				while ($handle && !feof($handle)) {
	        		$line = @fgets($handle);
				if(( substr( trim($line),0,2) != '--') && ( trim($line) != '')) {
					$line = trim($line);
					$query_txt .= " ".$line;
					if((substr($line,-1) == ';') && ($query_txt != '')) {
						$addSQL=true;
						$connect->execute($query_txt,true);
						$query_txt = '';
					}
				}
    			}
			if ($handle)
				@fclose($handle);
		}
		closedir($dh);
		if(!$addSQL)
			throw new LucteriosException( IMPORTANT,'Données non trouvées! ('.$temp_path.")");
		foreach($items as $item) {
			$r = rm_recursive($item);
			$r = rename($temp_path.$item,$item);
		}
		$connect->commit();
		$lbl->setValue("{[center]}{[bold]}Restauration Terminer.{[newline]}Vous devez vous reconnecter.{[/bold]}{[/center]}");
		$r = rm_recursive($temp_path);
	}
	 catch( Exception$e) {
		$connect->rollback();
		$lbl->setValue("{[center]}{[bold]}Erreur.{[newline]}{[font color=red]}".$e->getMessage()."{[/font]}{[/bold]}{[/center]}");
	}
	PEAR::setErrorHandling(PEAR_ERROR_RETURN);
	$xfer_result->addAction( new Xfer_Action('_Fermer','ok.png','CORE','menu', FORMTYPE_MODAL, CLOSE_YES));
}
else {
	$xfer_result->m_context['RESTOR'] = 1;
	$img_title = new Xfer_Comp_Image('img_title');
	$img_title->setLocation(0,0,1,2);
	$img_title->setValue('backup_restor.png');
	$xfer_result->addComponent($img_title);
	$lbl = new Xfer_Comp_LabelForm("info");
	$lbl->setLocation(1,0);
	$lbl->setValue("{[center]}{[bold]}Restauration en cours.{[newline]}Merci de patienter.{[/bold]}{[/center]}");
	$xfer_result->addComponent($lbl);
	$btn = new Xfer_Comp_Button("Next");
	$btn->setLocation(1,1);
	$btn->setAction( new Xfer_Action('Restaurer','','CORE','restorForm', FORMTYPE_REFRESH, CLOSE_NO));
	$btn->JavaScript = "
	parent.refresh();
";
	$xfer_result->addComponent($btn);
	$xfer_result->addAction( new Xfer_Action('_Annuler','cancel.png','','', FORMTYPE_MODAL, CLOSE_YES));
}
//@CODE_ACTION@
}catch(Exception $e) {
	throw $e;
}
return $xfer_result;
}

?>
