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
// --- Last modification: Date 15 November 2011 0:23:20 By  ---

//@BEGIN@
class ModelConverter {
	private $mModel;
	private $mUrl;

	public function __construct($modelXml) {
		$this->mModel = $modelXml;
		$current_file = "http://".$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"];
		$this->mUrl = substr($current_file,0,-1* strlen( strrchr($current_file,"/"))+1);
	}

	public function run() {
		$pos_body = strpos($this->mModel,'<body');
		if($pos_body !== false) {
			$this->mModel = substr($this->mModel,0,$pos_body)."<xsl:template name='body'>\n". substr($this->mModel,$pos_body);
		}
		$this->insertInTag('header','<xsl:template name="header">','</xsl:template>');
		$this->insertInTag('bottom','<xsl:template name="bottom">','</xsl:template>');
		$this->insertInTag('left','<xsl:template name="left">','</xsl:template>');
		$this->insertInTag('rigth','<xsl:template name="rigth">','</xsl:template>');
		$this->insertInTagWithDataLoop('body',"<xsl:call-template name='header'/><xsl:call-template name='bottom'/><xsl:call-template name='left'/><xsl:call-template name='rigth'/>","<page>\n","</page>\n");
		$this->insertInTagWithDataLoop('columns');
		$this->insertInTagWithDataLoop('rows');
		$this->insertInTagWithDataLoop('cell');
		$this->changeSelect();
		$this->convertImage();
		$pos_model = strpos($this->mModel,'<model');
		$pos_model_sep = false;
		if($pos_model !== false)$pos_model_sep = strpos($this->mModel,'>',$pos_model);
		if(($pos_model !== false) && ($pos_model_sep !== false)) {
			$header = "<?xml version='1.0' encoding='ISO-8859-1' ?>\n";
			$header .= "<xsl:stylesheet version='1.0' xmlns:xsl='http://www.w3.org/1999/XSL/Transform'>\n";
			$header .= "<xsl:template match='/'>\n";
			$header .= substr($this->mModel,$pos_model,$pos_model_sep-$pos_model+1)."\n";
			$header .= "<xsl:call-template name='body'/>\n";
			$header .= "</model>\n";
			$header .= "</xsl:template>\n";
			$this->mModel = substr($this->mModel,$pos_model_sep+1);
			$this->mModel = str_replace("</model>","\n</xsl:template>\n</xsl:stylesheet>",$this->mModel);
			$this->mModel = $header.$this->mModel;
		}
		return true;
	}

	public function toXap($xmldata,$xmlresultfile = '') {
		$Xap = $this->TransformXsl($xmldata,$this->mModel);
		$Xap = $this->ConvertApasFormat($Xap);
		if($xmlresultfile != '')
			$this->save($xmlresultfile,$Xap);
		return $Xap;
	}

	public static function TransformXsl($xmldata,$xsldata) {
		if(! class_exists('XsltProcessor') || ! class_exists('DomDocument'))
			die('processeur XSLT non installe!');
		$proc_xsl = new DomDocument();
		$proc_xsl->loadXML($xsldata);
		$proc_xml = new DomDocument();
		$proc_xml->loadXML($xmldata);
		$xslt = new XsltProcessor();
		$xslt->importStylesheet($proc_xsl);
		$obj = $xslt->transformToDoc($proc_xml);
		$obj->encoding = 'ISO-8859-1';
		if (!method_exists($obj,'saveXML')) {
			logAutre(" *** Impression impossible *** \nxmldata='$xmldata'\nxsldata='$xsldata'");
			require_once("CORE/Lucterios_Error.inc.php");
			throw new LucteriosException(GRAVE,"Impression impossible!");
		}
		$res = $obj->saveXML();
		return $res;
	}

	public static function ConvertApasFormat($xmltext) {
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

	private function save($modelFile,$content = null) {
		if($content == null)
			$content = $this->mModel;
		$wh = fopen($modelFile,'w');
		if($wh !== false) {
			foreach( explode("\n",$content) as $line)
				fwrite($wh,$line."\n");
		}
		fclose($wh);
	}

	private function getTagPosition($tagName,$start) {
		return $this->getItemPosition("<$tagName","</$tagName>",array('/','>'),$start);
	}

	private function insertInTag($tagName,$addBegin,$endBegin) {
		$start = 0;
		$len = 0;
		while($len != -1) {
			list($pos,$len) = $this->getTagPosition($tagName,$start);
			if($len != -1) {
				$new_text = $addBegin. substr($this->mModel,$pos,$len).$endBegin;
				$this->mModel = substr_replace($this->mModel,$new_text,$pos,$len);
				$start = $pos+ strlen($new_text);
			}
		}
	}

	private function getItemPosition($itembegin,$itemend,$ending,$start) {
		$posIn = strpos($this->mModel,$itembegin,$start);
		$posOut = strpos($this->mModel,$itemend,$start);
		if($posOut !== false)$posOut += strlen($itemend)-1;
		if(($posIn !== false) && ($posOut === false) && ($ending != null)) {
			$end = strpos($this->mModel,$ending[1],$posIn);
			if(($end>1) && ($this->mModel[$end-1] == $ending[0]))$posOut = $end;
		}
		if(($posIn === false) || ($posOut === false) || ($posOut<$posIn))
		return array(-1,-1);
		else
		return array($posIn,$posOut-$posIn+1);
	}

	private function convertImage() {
		$start = 0;
		$len = 0;
		while($len != -1) {
			list($pos,$len) = $this->getTagPosition('image',$start);
			if($len != -1) {
				$pos_begin = strpos($this->mModel,">",$pos);
				$pos_end = strpos($this->mModel,'</image',$pos);
				if(($pos_begin >= 0) && ($pos_begin<($pos+$len-1)) && ($pos_end >= 0) && ($pos_end<($pos+$len-1))) {
					$pos_begin++;
					$file_name = substr($this->mModel,$pos_begin,$pos_end-$pos_begin);
					$len_file_name = strlen($file_name);
					$file_name = trim($file_name);
					if (is_file($file_name)) {
						$file_size = filesize($file_name);
						$handle = fopen($file_name,'r');
						$encoder = fread($handle,$file_size);
						$encoder = chunk_split( base64_encode($encoder));
						$f = fclose($handle);
						$base64_mime_image = "data:image/*;base64,$encoder";
						$this->mModel = substr($this->mModel,0,$pos_begin)."\n$base64_mime_image\n". substr($this->mModel,$pos_end);
						$start = $pos+$len+ strlen($base64_mime_image)-$len_file_name;
					}
					else $start = $pos+$len;
				}
				else $start = $pos+$len;
			}
		}
	}

	private function changeSelect() {
		$start = 0;
		$len = 0;
		while($len != -1) {
			list($pos,$len) = $this->getItemPosition('[{','}]', null,$start);
			if($len != -1) {
				$item = substr($this->mModel,$pos+2,$len-4);
				$new_text = "<xsl:value-of select='$item'/>";
				$this->mModel = substr_replace($this->mModel,$new_text,$pos,$len);
				$start = $pos+ strlen($new_text);
			}
		}
		$start = 0;
		$len = 0;
		while($len != -1) {
			list($pos,$len) = $this->getItemPosition('[(',')]', null,$start);
			if($len != -1) {
				$item = substr($this->mModel,$pos+2,$len-4);
				$new_text = "<xsl:value-of select='$item'/>";
				$this->mModel = substr_replace($this->mModel,$new_text,$pos,$len);
				$start = $pos+ strlen($new_text);
			}
		}
	}

	private function insertInTagWithDataLoop($tagName,$loopingBegin = '',$addbegin = '',$addend = '') {
		$start = 0;
		$len = 0;
		while($len != -1) {
			list($pos,$len) = $this->getTagPosition($tagName,$start);
			if($pos != -1)$pos_end = strpos($this->mModel,'>',$pos);
			if(($len != -1) && ($pos_end !== false)) {
				$data = '';
				$pos_data = strpos($this->mModel,'data',$pos);
				if(($pos_data !== false) && ($pos_data<$pos_end)) {
					$pos_equal = strpos($this->mModel,'=',$pos_data);
					$pos_cote = min($pos_end, strpos($this->mModel,' ',$pos_equal));
					if(($pos_equal !== false) && ($pos_cote !== false) && ($pos_equal<$pos_cote))$data = substr($this->mModel,$pos_equal+1,$pos_cote-$pos_equal-1);
				}
				if(($data != '') && ($data != '"."') && ($data != "'.'") && ($data != "''") && ($data != '""')) {
					$data = str_replace("'",'"',$data);
					$new_text = "<xsl:for-each select=$data>\n".$addbegin.$loopingBegin. substr($this->mModel,$pos,$len).$addend."\n</xsl:for-each>\n";
				}
				else {
					$new_text = $addbegin.$loopingBegin. substr($this->mModel,$pos,$len).$addend;
				}
				$this->mModel = substr_replace($this->mModel,$new_text,$pos,$len);
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
		$title = $report->titre;
		return array($xap,$title,1);
	}
	else
	return false;
}

define('WRITE_MODE_NONE',0);
define('WRITE_MODE_WRITE',1);
define('WRITE_MODE_OVERWRITE',2);

function CheckOrBuildReport($extension,$printmodel,$params,$title,$printRef = 0,$writeMode = WRITE_MODE_NONE) {
	$printfile = "extensions/$extension/$printmodel.prt.php";
	if(($printRef>0) && ($writeMode == WRITE_MODE_WRITE)) {
		$report = getDBReport($extension,$printmodel,$printRef);
		if( is_array($report))
		return $report;
	}
	list($id,$model,$res) = checkDBModel($extension,$printmodel);
	if($id>0) {
		require_once$printfile;
		$XmlDataFctName = $extension."_APAS_".$printmodel."_getXmlData";
		$xml_data = $XmlDataFctName($params);
		$model_converter = new ModelConverter('<?xml version="1.0" encoding="ISO-8859-1"?>'.$model);
		$model_converter->run();
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
