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
// library file write by SDK tool
// --- Last modification: Date 03 October 2011 22:37:58 By  ---

//@BEGIN@
class ModelConverter {
	var$_Model;
	var$_Url;

	function ModelConverter($modelXml) {
		$this->_Model = $modelXml;
		$current_file = "http://".$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"];
		$this->_Url = substr($current_file,0,-1* strlen( strrchr($current_file,"/"))+1);
	}

	function Run() {
		$pos_body = strpos($this->_Model,'<body');
		if($pos_body !== false) {
			$this->_Model = substr($this->_Model,0,$pos_body)."<xsl:template name='body'>\n". substr($this->_Model,$pos_body);
		}
		$this->__InsertInTag('header','<xsl:template name="header">','</xsl:template>');
		$this->__InsertInTag('bottom','<xsl:template name="bottom">','</xsl:template>');
		$this->__InsertInTag('left','<xsl:template name="left">','</xsl:template>');
		$this->__InsertInTag('rigth','<xsl:template name="rigth">','</xsl:template>');
		$this->__InsertInTagWithDataLoop('body',"<xsl:call-template name='header'/><xsl:call-template name='bottom'/><xsl:call-template name='left'/><xsl:call-template name='rigth'/>","<page>\n","</page>\n");
		$this->__InsertInTagWithDataLoop('columns');
		$this->__InsertInTagWithDataLoop('rows');
		$this->__InsertInTagWithDataLoop('cell');
		$this->__ChangeSelect();
		$this->__ConvertImage();
		$pos_model = strpos($this->_Model,'<model');
		$pos_model_sep = false;
		if($pos_model !== false)$pos_model_sep = strpos($this->_Model,'>',$pos_model);
		if(($pos_model !== false) && ($pos_model_sep !== false)) {
			$header = "<?xml version='1.0' encoding='ISO-8859-1' ?>\n";
			$header .= "<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>\n";
			$header .= "<xsl:template match='/'>\n";
			$header .= substr($this->_Model,$pos_model,$pos_model_sep-$pos_model+1)."\n";
			$header .= "<xsl:call-template name='body'/>\n";
			$header .= "</model>\n";
			$header .= "</xsl:template>\n";
			$this->_Model = substr($this->_Model,$pos_model_sep+1);
			$this->_Model = str_replace("</model>","\n</xsl:template>\n</xsl:stylesheet>",$this->_Model);
			$this->_Model = $header.$this->_Model;
		}
		return true;
	}

	function TransformXsl($xmldata,$xsldata) {
		if( version_compare( phpversion(),'5','>=')) {
			if(! class_exists('XsltProcessor') || ! class_exists('DomDocument'))die('processeur XSLT non installe!');
			$proc_xsl = new DomDocument();
			$proc_xsl->loadXML($xsldata);
			$proc_xml = new DomDocument();
			$proc_xml->loadXML($xmldata);
			$xslt = new XsltProcessor();
			$xslt->importStylesheet($proc_xsl);
			$obj = $xslt->transformToDoc($proc_xml);
			$obj->encoding = 'ISO-8859-1';
			$res = $obj->saveXML();
		}
		else {
			$dom_xml = domxml_open_mem($xmldata);
			$dom_xsl = domxml_xslt_stylesheet($xsldata);
			$dom_result = $dom_xsl->process($dom_xml);
			$res = $dom_result->dump_mem( true,"ISO-8859-1");
		}
		return $res;
	}

	function toXap($xmldata,$xmlresultfile = '') {
		$Xap = $this->TransformXsl($xmldata,$this->_Model);
		$Xap = $this->convertApasFormat($Xap);
		if($xmlresultfile != '')$this->Save($xmlresultfile,$Xap);
		return $Xap;
	}

	function convertApasFormat($xmltext) {
		$xml_text = str_replace('<b>','<font Font-weight="bold">',$xmltext);
		$xml_text = str_replace('<i>','<font Font-style="italic">',$xml_text);
		$xml_text = str_replace('<u>','<font text-decoration="underline">',$xml_text);
		$xml_text = str_replace('</b>','</font>',$xml_text);
		$xml_text = str_replace('</i>','</font>',$xml_text);
		$xml_text = str_replace('</u>','</font>',$xml_text);
		$xml_text = str_replace('{[newline]}','<br/>',$xml_text);
		$xml_text = str_replace('{[bold]}','<font Font-weight="bold">',$xml_text);
		$xml_text = str_replace('{[/bold]}','</font>',$xml_text);
		$xml_text = str_replace('{[italic]}','<font Font-style="italic">',$xml_text);
		$xml_text = str_replace('{[/italic]}','</font>',$xml_text);
		$xml_text = str_replace('{[underline]}','<font text-decoration="underline">',$xml_text);
		$xml_text = str_replace('{[/underline]}','</font>',$xml_text);
		$xml_text = str_replace("{[","<",$xml_text);
		$xml_text = str_replace("]}",">",$xml_text);
		$xml_text = preg_replace(array('/&(#[0-9]+;)/','/&([a-zA-Z0-9 ])/'),array('&\1','&amp;\1'),$xml_text);
		return $xml_text;
	}

	function Save($modelFile,$content = null) {
		if($content == null)$content = $this->_Model;
		$wh = fopen($modelFile,'w');
		if($wh !== false) {
			foreach( split("\n",$content) as $line) fwrite($wh,$line."\n");
		} fclose($wh);
	}

	function __GetTagPosition($tagName,$start) {
		return $this->__GetItemPosition("<$tagName","</$tagName>",array('/','>'),$start);
	}

	function __InsertInTag($tagName,$addBegin,$endBegin) {
		$start = 0;
		$len = 0;
		while($len != -1) {
			list($pos,$len) = $this->__GetTagPosition($tagName,$start);
			if($len != -1) {
				$new_text = $addBegin. substr($this->_Model,$pos,$len).$endBegin;
				$this->_Model = substr_replace($this->_Model,$new_text,$pos,$len);
				$start = $pos+ strlen($new_text);
			}
		}
	}

	function __GetItemPosition($itembegin,$itemend,$ending,$start) {
		$posIn = strpos($this->_Model,$itembegin,$start);
		$posOut = strpos($this->_Model,$itemend,$start);
		if($posOut !== false)$posOut += strlen($itemend)-1;
		if(($posIn !== false) && ($posOut === false) && ($ending != null)) {
			$end = strpos($this->_Model,$ending[1],$posIn);
			if(($end>1) && ($this->_Model[$end-1] == $ending[0]))$posOut = $end;
		}
		if(($posIn === false) || ($posOut === false) || ($posOut<$posIn))
		return array(-1,-1);
		else
		return array($posIn,$posOut-$posIn+1);
	}

	function __ConvertImage() {
		$start = 0;
		$len = 0;
		while($len != -1) {
			list($pos,$len) = $this->__GetTagPosition('image',$start);
			if($len != -1) {
				$pos_begin = strpos($this->_Model,">",$pos);
				$pos_end = strpos($this->_Model,'</image',$pos);
				if(($pos_begin >= 0) && ($pos_begin<($pos+$len-1)) && ($pos_end >= 0) && ($pos_end<($pos+$len-1))) {
					$pos_begin++;
					$file_name = substr($this->_Model,$pos_begin,$pos_end-$pos_begin);
					$len_file_name = strlen($file_name);
					$file_name = trim($file_name);
					if (is_file($file_name)) {
						$file_size = filesize($file_name);
						$handle = fopen($file_name,'r');
						$encoder = fread($handle,$file_size);
						$encoder = chunk_split( base64_encode($encoder));
						$f = fclose($handle);
						$base64_mime_image = "data:image/*;base64,$encoder";
						$this->_Model = substr($this->_Model,0,$pos_begin)."\n$base64_mime_image\n". substr($this->_Model,$pos_end);
						$start = $pos+$len+ strlen($base64_mime_image)-$len_file_name;
					}
					else $start = $pos+$len;
				}
				else $start = $pos+$len;
			}
		}
	}

	function __ChangeSelect() {
		$start = 0;
		$len = 0;
		while($len != -1) {
			list($pos,$len) = $this->__GetItemPosition('[{','}]', null,$start);
			if($len != -1) {
				$item = substr($this->_Model,$pos+2,$len-4);
				$new_text = "<xsl:value-of select='$item'/>";
				$this->_Model = substr_replace($this->_Model,$new_text,$pos,$len);
				$start = $pos+ strlen($new_text);
			}
		}
		$start = 0;
		$len = 0;
		while($len != -1) {
			list($pos,$len) = $this->__GetItemPosition('[(',')]', null,$start);
			if($len != -1) {
				$item = substr($this->_Model,$pos+2,$len-4);
				$new_text = "<xsl:value-of select='$item'/>";
				$this->_Model = substr_replace($this->_Model,$new_text,$pos,$len);
				$start = $pos+ strlen($new_text);
			}
		}
	}

	function __InsertInTagWithDataLoop($tagName,$loopingBegin = '',$addbegin = '',$addend = '') {
		$start = 0;
		$len = 0;
		while($len != -1) {
			list($pos,$len) = $this->__GetTagPosition($tagName,$start);
			if($pos != -1)$pos_end = strpos($this->_Model,'>',$pos);
			if(($len != -1) && ($pos_end !== false)) {
				$data = '';
				$pos_data = strpos($this->_Model,'data',$pos);
				if(($pos_data !== false) && ($pos_data<$pos_end)) {
					$pos_equal = strpos($this->_Model,'=',$pos_data);
					$pos_cote = min($pos_end, strpos($this->_Model,' ',$pos_equal));
					if(($pos_equal !== false) && ($pos_cote !== false) && ($pos_equal<$pos_cote))$data = substr($this->_Model,$pos_equal+1,$pos_cote-$pos_equal-1);
				}
				if(($data != '') && ($data != '"."') && ($data != "'.'") && ($data != "''") && ($data != '""')) {
					$data = str_replace("'",'"',$data);
					$new_text = "<xsl:for-each select=$data>\n".$addbegin.$loopingBegin. substr($this->_Model,$pos,$len).$addend."\n</xsl:for-each>\n";
				}
				else {
					$new_text = $addbegin.$loopingBegin. substr($this->_Model,$pos,$len).$addend;
				}
				$this->_Model = substr_replace($this->_Model,$new_text,$pos,$len);
				$start = $pos+ strlen($new_text);
			}
			else $len = -1;
		}
	}
}

function getDBModel($extension,$printmodel) {
	require_once("CORE/printmodel.tbl.php");
	$print_model_obj = new DBObj_CORE_printmodel;
	$print_model_obj->extensionid = $extension;
	$print_model_obj->identify = $printmodel;
	$print_model_obj->reference = 0;
	if($print_model_obj->find()>0) {
		$print_model_obj->fetch();
		$row = array($print_model_obj->id,$print_model_obj->model,$print_model_obj->modify);
		return $row;
	}
	else
	return array(0,'', false);
}

function checkDBModel($extension,$printmodel,$checkModify = false) {
	global $rootPath;
	if(!isset($rootPath))$rootPath = "./";
	$model = '';
	$id = 0;
	$res = '';
	$printfile = $rootPath."extensions/$extension/$printmodel.prt.php";
	if( is_file($printfile)) {
		list($id,$model,$modify) = getDBModel($extension,$printmodel);
		if($id == 0) {
			$MODEL_DEFAULT = '';
			require_once$printfile;
			require_once("CORE/printmodel.tbl.php");
			$model = new DBObj_CORE_printmodel;
			$model->extensionid = $extension;
			$model->identify = $printmodel;
			$model->reference = 0;
			$model->titre = $Title;
			$model->model = str_replace(array('#&39;'),array("'"), trim($MODEL_DEFAULT));
			$model->modify = 'n';
			$model->insert();
			list($id,$model) = getDBModel($extension,$printmodel);
			$res = 'Ajouter';
		}
		else if(($modify == 'n') && $checkModify) {
			$MODEL_DEFAULT = '';
			require_once$printfile;
			require_once("CORE/printmodel.tbl.php");
			$model = new DBObj_CORE_printmodel;
			$model->get($id);
			$model->titre = $Title;
			$model->model = str_replace(array('#&39;'),array("'"), trim($MODEL_DEFAULT));
			$model->modify = 'n';
			$model->update();
			list($id,$model) = getDBModel($extension,$printmodel);
			$res = 'Modifier';
		}
		else $res = "Rien";
	}
	return array($id,$model,$res);
}

function getDBReport($extension,$printmodel,$reference) {
	require_once("CORE/finalreport.tbl.php");
	$report = new DBObj_CORE_finalreport;
	$report->extensionid = $extension;
	$report->identify = $printmodel;
	$report->reference = $reference;
	if($report->find()>0) {
		$report->fetch();
		$xap = $report->getXap();
		$title = $report->title;
		return array($xap,$title,1);
	}
	else
	return false;
} define('WRITE_MODE_NONE',0); define('WRITE_MODE_WRITE',1); define('WRITE_MODE_OVERWRITE',2);

function CheckOrBuildReport($extension,$printmodel,$modelRef,$params,$title,$printRef = 0,$writeMode = WRITE_MODE_NONE) {
	$printfile = "extensions/$extension/$printmodel.prt.php";
	if(($printRef>0) && ($writeMode == WRITE_MODE_WRITE)) {
		$report = getDBReport($extension,$printmodel,$printRef);
		if( is_array($report))
		return $report;
	}
	list($id,$model,$res) = checkDBModel($extension,$printmodel,$modelRef);
	if($id>0) {
		require_once$printfile;
		$XmlDataFctName = $extension."_APAS_".$printmodel."_getXmlData";
		$xml_data = $XmlDataFctName($params);
		$model_converter = new ModelConverter('<?xml version="1.0" encoding="ISO-8859-1"?>'.$model);
		$model_converter->Run();
		$xap = $model_converter->toXap('<?xml version="1.0" encoding="ISO-8859-1"?>'.$xml_data);
		if(($printRef>0) && ($writeMode != WRITE_MODE_NONE)) {
			$rep = str_replace('"',"'",$report[0]);
			require_once("CORE/finalreport.tbl.php");
			$report_obj = new DBObj_CORE_finalreport;
			$report_obj->extensionid = $extension;
			$report_obj->identify = $printmodel;
			$report_obj->reference = $printRef;
			if(($nb = $report_obj->find())>0)$report_obj->fetch();
			$report_obj->setXap( urlencode($xap));
			$report_obj->date = date('Y-m-d');
			$report_obj->heure = date('H:i:s');
			$report_obj->titre = $title;
			if($nb>0)$report_obj->update();
			else $report_obj->insert();
		}
		$report = array($xap,$title,1);
	}
	else $report = false;
	return $report;
}
//@END@
?>
