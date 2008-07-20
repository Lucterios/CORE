<?php
// Action file write by SDK tool
// --- Last modification: Date 16 June 2008 22:40:23 By  ---

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
	require_once("Archive/Tar.php");
	require_once("CORE/Lucterios_Error.inc.php");
	$tar = new Archive_Tar($file_path);
	$result = $tar->extract($temp_path);
	if(! is_file($temp_path."data.sql")) {
		throw new LucteriosException( IMPORTANT,'Données non trouvées! ('.$temp_path."/data.sql)");
	}
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
	$query = file($temp_path."data.sql");
	global $connect;
	$connect->begin();
	
	try {
		$query_txt = "";
		$new_query = array();
		foreach($query as $q) {
			if(( substr( trim($q),0,2) != '--') && ( trim($q) != '')) {
				$q = trim($q);
				$query_txt .= " ".$q;
				if( substr($q,-1) == ';') {
					$new_query[] = $query_txt;
					$query_txt = '';
				}
			}
		}
		if($query_txt != '')$new_query[] = $query_txt;
		foreach($new_query as $q) {
			if(!$connect->execute($q)) {
				throw new LucteriosException( IMPORTANT,"Erreur dans les données (".$connect->errorMsg.")!");
			}
		}
		foreach($items as $item) {
			$r = rm_recursive($item);
			$r = rename($temp_path.$item,$item);
		}
		$connect->commit();
		$lbl->setValue("{[center]}{[bold]}Restauration Terminer.{[newline]}Vous devez vous reconnecter.{[/bold]}{[/center]}");
	}
	 catch( Exception$e) {
		$connect->rollback();
		$lbl->setValue("{[center]}{[bold]}Erreur.{[newline]}{[font color=red]}".$e->getMessage()."{[/font]}{[/bold]}{[/center]}");
	}
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
