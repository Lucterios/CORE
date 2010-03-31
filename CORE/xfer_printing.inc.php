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
//  // library file write by SDK tool
// --- Last modification: Date 30 March 2010 22:39:10 By  ---

//@BEGIN@
/**
 * fichier gï¿½rant le classe de d'impression de transfert
 *
 * @author Pierre-Oliver Vershoore/Laurent Gay
 * @version 0.10
 * @package Lucterios
 * @subpackage Xfer
 */

require_once 'xfer.inc.php';
require_once 'ConvertPrintModel.inc.php';

/**
 * Classe de transfert d'impression
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Container_Print extends Xfer_Container_Abstract
{
	/**
	 * Titre de l'impression
	 *
	 * @var string
	 */
	var $ReportTitle;

	/**
	 * Contenu du raport d'impression
	 *
	 * @var string
	 */
	var $ReportContent;

	/**
	 * Type d'impression
	 *
	 * 0: Retour au format xml-fo
	 * 1: Retour au format Lucterios-Print
	 * @var integer
	 */
	var $ReportType;

	/**
	 * Mode d'impression
	 *
	 * 1: Imprimante
	 * 2: Previsualisation
	 * 3: PDF
	 * 4: CVS
	 * Autre : Selectionneur
	 * @var integer
	 */
	var $ReportMode=0;

	/**
	 * Export text autorisé
	 *
	 * @var integer
	 */
	var $withTextExport=0;

	/**
	 * Constructeur
	 *
	 * @param string $extension
	 * @param string $action
	 * @param array $context
	 * @return Xfer_Container_Print
	 */
	function Xfer_Container_Print($extension,$action,$context=array())
	{
		$this->Xfer_Container_Abstract($extension,$action,$context);
		$this->m_observer_name="Core.Print";
		$this->ReportContent="";
		$this->ReportType=1;
	}

	/**
	* SelectorDesc
	*
	* @access private
	* @var string
	*/
	var $SelectorDesc='';
	/**
	* Selector
	*
	* @access private
	* @var string
	*/
	var $Selector=null;

	/**
	 * Affiche un choix d'impression.
	 *
	 * @param array $SpecialSelector : Etiquette si non spécifié
	 * @param array $SelectorDesc : Description du selctionneur
	 * @return boolean
	 */
	function showSelector($SpecialSelector=array(),$SelectorDesc=array('Etiquette','ETIQUETTE'))
	{
		$this->Selector=$SpecialSelector;
		$this->SelectorDesc=$SelectorDesc;
		if (array_key_exists("PRINT_MODE",$this->m_context))
			return ($this->m_context["PRINT_MODE"]!="");
		else
			return false;
	}


	/**
	 * _ReponseSelector
	 *
	* @access private
	 * @return String
	 */
	function _ReponseSelector()
	{
		if (!is_array($this->Selector) && ($this->Selector!=0))
			throw new LucteriosException(GRAVE,"Erreur de selecteur d'impression!");

		require_once('CORE/xfer_custom.inc.php');
		$xfer_result= new Xfer_Container_Custom($this->m_extension, $this->m_action, $this->m_context);
		$xfer_result->Caption=$this->Caption;

		$lbl=new Xfer_Comp_LabelForm('lblPrintMode');
		$lbl->setValue('{[bold]}Type de rapport{[/bold]}');
		$lbl->setLocation(0,0);
		$xfer_result->addComponent($lbl);
		$print_mode=new Xfer_Comp_Select('PRINT_MODE');
		$selector=array(2=>'Prévisualisation',3=>'Fichier PDF');
		if ($this->withTextExport!=0)
			$selector[4]='Fichier CSV';
		$print_mode->setSelect($selector);
		$print_mode->setValue(1);
		$print_mode->setLocation(1,0);
		$xfer_result->addComponent($print_mode);

		if ($this->Selector!=0) {
			if (count($this->Selector)==0) {
				require_once('CORE/etiquettes.tbl.php');
				$etiquette=new DBObj_CORE_etiquettes;
				$etiquette->orderBy('nom');
				$etiquette->find();
				while ($etiquette->fetch())
					$this->Selector[$etiquette->id]=$etiquette->nom;
				$lbl=new Xfer_Comp_LabelForm('lbldecalage');
				$lbl->setValue('{[bold]}N° première étiquette{[/bold]}');
				$lbl->setLocation(0,2);
				$xfer_result->addComponent($lbl);
				$num=new Xfer_Comp_Float("PREMIERE_ETIQUETTE",1,100,0);
				$num->setValue(1);
				$num->setLocation(1,2);
				$xfer_result->addComponent($num);
			}
			$lbl=new Xfer_Comp_LabelForm('lblselector');
			$lbl->setValue('{[bold]}'.$this->SelectorDesc[0].'{[/bold]}');
			$lbl->setLocation(0,1);
			$xfer_result->addComponent($lbl);
			$selector=new Xfer_Comp_Select($this->SelectorDesc[1]);
			$selector->setSelect($this->Selector);
			$selector->setValue('');
			$selector->setLocation(1,1);
			$xfer_result->addComponent($selector);
		}

		$xfer_result->addAction(new Xfer_Action("_Imprimer", "print.png", $this->m_extension, $this->m_action, FORMTYPE_MODAL, CLOSE_YES));
		$xfer_result->addAction(new Xfer_Action("_Fermer", "close.png"));

		return $xfer_result->getReponseXML();
	}

	/**
	 * Construit un rapport d'impression
	 *
	 * @param string $printmodel nom du model
	 * @param integer $modelRef
	 * @param array $params parametre d'impression
	 * @param string $title Titre de l'impression
	 * @param integer $writeMode
	 * @param integer $printRef
	 * @return boolean reussite
	 */
	function selectReport($printmodel,$modelRef,$params,$title,$writeMode=WRITE_MODE_NONE,$printRef=0)
	{
		$modelRef=(int)$modelRef;
		$printRef=(int)$printRef;
		$report=CheckOrBuildReport($this->m_extension, $printmodel, $modelRef, $params, $title, $printRef, $writeMode);
		if (is_array($report))
		{
			$this->ReportContent=$report[0];
			$this->ReportTitle=$report[1];
			$this->ReportType=$report[2];
		}
		return is_array($report);
	}

	/**
	 * Construit un rapport depuis des donnÃ©es
	 *
	 * @param String $aTitle Titre du rapport
	 * @param String $aData XML Ã  imprimer
	 * @param Int $aType Type de donnÃ©e
	 * @return boolean reussite
	 */
	function printData($aTitle,$aData,$aType=0)
	{
		$this->ReportTitle=$aTitle;
		$this->ReportType=$aType;
		if ($this->ReportType==0){
			$model_converter=new ModelConverter("");
			$this->ReportContent=$model_converter->TransformXsl($aData,implode("",file('CORE/LucteriosPrintStyleForFo.xsl')));
		}
		else {
			$this->ReportContent=$aData;
			$this->withTextExport=1;
		}
		return ($this->ReportContent!="");
	}

	/**
	 * Générateur d'étiquettes
	 *
	 * @param array $EtiquetteValues liste des contenus
	 * @return boolean reussite
	 */
	function printEtiquette($aTitle,$EtiquetteValues) {
		if (array_key_exists($this->SelectorDesc[1],$this->m_context))
			$etiquette=(int)$this->m_context[$this->SelectorDesc[1]];
		else
			throw new LucteriosException(GRAVE,"Erreur de selecteur d'étiquette!");
		require_once('CORE/etiquettes.tbl.php');
		if (array_key_exists("PREMIERE_ETIQUETTE",$this->m_context))
			$premiere_etiquette=(int)$this->m_context["PREMIERE_ETIQUETTE"];
		else
			$premiere_etiquette=1;
		$DBObjettiquettes=new DBObj_CORE_etiquettes();
		$DBObjettiquettes->get($etiquette);
		$report=$DBObjettiquettes->getReport($premiere_etiquette,$EtiquetteValues);
		$this->printData($aTitle,$report,$this->ReportType);
		return ($this->ReportContent!="");
	}

	/**
	 * Construit un rapport depuis un listing
	 *
	 * @param PrintListing $aPrintListing listing Ã  imprimer
	 * @return boolean reussite
	 */
	function printListing($aPrintListing)
	{
		$this->printData($aPrintListing->Title,$aPrintListing->generate(),$this->ReportType);
		return ($this->ReportContent!="");
	}

	/**
	 * _ReponseXML
	 *
	 * @access private
	 * @return string
	 */
	function getReponseXML()
	{
		if (array_key_exists("PRINT_MODE",$this->m_context))
			$ReportMode=(int)$this->m_context["PRINT_MODE"];
		else
			$ReportMode=$this->ReportMode;
		if ((!is_array($this->Selector) && ($this->Selector!=0)) || ($ReportMode!=0)) {
			$this->ReportMode=$ReportMode;
			return Xfer_Container_Abstract::getReponseXML();
		}
		else
			return $this->_ReponseSelector();
	}

	/**
	 * getBodyContent
	 *
	 * @access private
	 * @return string
	 */
	function getBodyContent($InBase64=true)
	{
		global $rootPath;
		if(!isset($rootPath)) $rootPath = "";
		if ($this->ReportMode==4) {
			$xsl_file="CORE/ConvertxlpToCSV.xsl";
			if (is_file($xsl_file)) {
				require_once("CORE/ConvertPrintModel.inc.php");
				$rep_content=str_replace(array("\t","<br/>"),' ',$this->ReportContent);
				$content=ModelConverter::TransformXsl($rep_content,implode("", file($xsl_file)));
				$content=str_replace(array("\t"),' ',$content);
				$content=str_replace(array('<?xml version="1.0" encoding="ISO-8859-1"?>'."\n"),'',$content);
				$this->ReportType=2;
				if ($InBase64)
					return base64_encode($content);
				else
					return $content;
			}
			else
				return $this->ReportContent;
		}
		else {
			$fop_java_dir=realpath($rootPath."CORE/fop/");
			$fop_java_file=realpath($rootPath."CORE/fop/fop.jar");
			$xsl_file=realpath($rootPath."CORE/LucteriosPrintStyleForFo.xsl");
			if (is_file($fop_java_file) && is_file($xsl_file)) {
				global $tmpPath;
				$xml_file=realpath(tempnam($tmpPath,'xml'));
				$pdf_file=realpath(tempnam($tmpPath,'pdf'));

				$handle = fopen($xml_file, "w");
				fwrite($handle, $this->ReportContent);
				fclose($handle);

				$output=array();
				$return_var=0;
				$print_cmd='java -classpath "'.$fop_java_dir.'" -jar "'.$fop_java_file.'" -xml "'.$xml_file.'" -xsl "'.$xsl_file.'" -pdf "'.$pdf_file.'"';
				$last_line=exec($print_cmd,$output,$return_var);
				if (is_file($pdf_file) && ($return_var==0)) {
					$content=file_get_contents($pdf_file);
				}
				else {
					$content="";
					foreach($output as $line) {
						$content.=$line."{[newline]}";
					}
					$content.=$last_line;
					require_once("CORE/Lucterios_Error.inc.php");
					logAutre("ReportContent:\nXML:$xml_file\nPDF:$pdf_file\n$this->ReportContent");
					throw new LucteriosException( IMPORTANT,"Echec de l'impression!!{[newline]}$content");
				}
				unlink($xml_file);
				unlink($pdf_file);

				$this->ReportType=2;
				if ($InBase64)
					return base64_encode($content);
				else
					return $content;
			}
			else
				return $this->ReportContent;
		}
	}

	/**
	 * _ReponseXML
	 *
	 * @access private
	 * @return string
	 */
	function _ReponseXML()
	{
		$content=$this->getBodyContent();
		$xml_text=sprintf("<PRINT title='%s' type='%d' mode='%d' withTextExport='%d'><![CDATA[%s]]></PRINT>",$this->ReportTitle,$this->ReportType,$this->ReportMode,$this->withTextExport,$content);
		return $xml_text;
	}
}

/**
 * Classe de transfert de model d'impression
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Container_Template extends Xfer_Container_Abstract
{
	/**
	 * Titre du model de l'impression
	 *
	 * @var string
	 */
	var $title;
	/**
	 * contenu de l'XML exemple
	 *
	 * @var string
	 */
	var $m_xml_data;

	/**
	 * contenu du model
	 *
	 * @var string
	 */
	var $m_model;

	/**
	 * model id
	 *
	 * @access private
	 * @var integer
	 */
	var $m_model_id;

	/**
	 * Enter description here...
	 *
	 * @param unknown_type $extension
	 * @param unknown_type $action
	 * @param unknown_type $context
	 * @return Xfer_Container_Template
	 */
	function Xfer_Container_Template($extension,$action,$context=array())
	{
		$this->Xfer_Container_Abstract($extension,$action,$context);
		$this->m_observer_name="Core.Template";
		$this->m_xml_data='';
		$this->m_model='';
		$this->m_model_id=0;
	}

	/**
	 * Charge le model et les donnï¿½es
	 *
	 * @param string $extension
	 * @param string $printmodel
	 * @return boolean
	 */
	function selectModel($extension,$printmodel)
	{
		list($id,$model,$res) = checkDBModel($extension,$printmodel);
		$printfile="extensions/$extension/$printmodel.prt.php";
		require_once($printfile);
		return $this->setModel($extension,$printmodel,$id,$model,$Title);
	}

	/**
	 * Charge l'exemple XML depuis le fichier
	 *
	 * @param string $extension
	 * @param string $printmodel
	 * @param integer $id
	 * @param string $model
	 * @param string $title
	 * @return boolean
	 */
	function setModel($extension,$printmodel,$id,$model,$title)
	{
		if ($id!=0)
		{
			$this->title=$title;
			$this->m_model=$model;
			$this->m_idmodel=$id;

			$printfile="extensions/$extension/$printmodel.prt.php";
			require_once($printfile);
              $XmlDataFctName=$extension."_APAS_".$printmodel."_getXmlData";
			$this->m_xml_data=$XmlDataFctName();
		}
		return ($id!=0);
	}

	/**
	 * _ReponseXML
	 *
	 * @access private
	 * @return string
	 */
	function _ReponseXML()
	{
		$xml_text=sprintf("<TEMPLATE title='%s' model='%d'>",$this->title,$this->m_idmodel);
		$xml_text=$xml_text.sprintf("<XMLOBJECT><![CDATA[%s]]></XMLOBJECT>",$this->m_xml_data);
		$xml_text=$xml_text.sprintf("<XSLTEXT><![CDATA[%s]]></XSLTEXT>",$this->m_model);
		$xml_text=$xml_text."</TEMPLATE>";
		return $xml_text;
	}
}
//@END@
?>
