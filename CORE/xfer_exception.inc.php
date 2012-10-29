<?php
// This file is part of Lucterios/Diacamma, a software developped by 'Le Sanglier du Libre' (http://www.sd-libre.fr)
// thanks to have payed a retribution for using this module.
// 
// Lucterios/Diacamma is free software; you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License, or
// (at your option) any later version.
// 
// Lucterios/Diacamma is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
// 
// You should have received a copy of the GNU General Public License
// along with Lucterios; if not, write to the Free Software
// Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// library file write by Lucterios SDK tool

//@BEGIN@
/**
 * fichier gérant les exceptions transferées
 *
 * @author Pierre-Oliver Vershoore/Laurent Gay
 * @version 0.10
 * @package Lucterios
 * @subpackage Xfer
 */
require_once'xfer.inc.php';

/**
 * Verifie l'existance d'un ensemble de parametre.
 * Remonter une erreur si non trouvé.
 *
 * @param string $extension
 * @param string $action
 * @param array $params
 * @return null|Xfer_Container_Exception
 */
function checkParams($extension,$action,$params) {
	$ret = null;
	if( is_array($params)) {
		$numargs = func_num_args();
		$error = "";
		for($arg = 3;
		$arg<= func_num_args();
		$arg++) {
			if(! array_key_exists($numargs[$arg-1],$params))$error .= " ".$numargs[$arg-1];
		}
		$error = trim($error);
		if($error != "")$ret = xfer_returnError($extension,$action,$param,"Paramètres '$error' inconnus!");
	}
	else $ret = xfer_returnError($extension,$action,$param,"Paramètres incorrects!");
	return $ret;
}

/**
 * Extrait une valeur dans un tableu si elle exists
 *
 * @param array $params
 * @param string $name
 * @param type $default
 * @return type
 */
function getParams($params,$name,$default = null) {
	if( array_key_exists($name,$params))
	return $params[$name];
	else
	return $default;
}

/**
 * Classe containaire d'une exception
 *
 * @package Lucterios
 * @subpackage Xfer
 */
class Xfer_Container_Exception extends Xfer_Container_Abstract {
	/**
	 * Erreur PEAR
	 *
	 * @var Exception
	 */
	public $m_error = "";

	/**
	 * Description
	 *
	 * @var string
	 */
	public $m_text = "";

	/**
	 * Constructor
	 *
	 * @param string $extension
	 * @param string $action
	 * @param array $context
	 * @return Xfer_Container_Exception
	 */
	public function __construct($extension,$action,$context = array()) {
		parent::__construct($extension,$action,$context);
		$this->m_observer_name = "CORE.Exception";
	}

	/**
	 * Assigne valeurs
	 *
	 * @param PEAR_Error or Exception $pear_error
	 * @param string $text
	 */
	public function setData($error,$text = "") {
		$this->m_error = $error;
		$this->m_text = $text;
	}

	/**
	 * affichage
	 *
	 * @return string
	 */
	public function toString() {
		$ret = $this->m_text;
		$ret .= "[".$this->m_pear_error->getMessage().";".$this->m_pear_error->getCode().";".$this->m_pear_error->getMode()."]";
		return $ret;
	}

	public function getLucteriosFile($File) {
		if($File == $_SERVER['SCRIPT_FILENAME'])
		return "[init]";
		else {
			$path_info = pathinfo($File);
			$base = $path_info['filename'];
			$base = str_replace('_APAS_','::',$base);
			$base = str_replace('.','(',$base);
			$base .= ")";
			$sub_dir = basename($path_info['dirname']);
			$new_File = "[$sub_dir]$base";
			return $new_File;
		}
	}

	public function getErrorTrace() {
		$trace = "";
		if( is_subclass_of($this->m_error,'Exception')) {
			foreach($this->m_error->getTrace() as $num => $trace_line) {
				if($num == 0) {
					$trace_line['file'] = $this->m_error->getFile();
					$trace_line['line'] = $this->m_error->getLine();
				}
				$trace .= $num."|";
				$trace .= $this->getLucteriosFile($trace_line['file'])."|";
				$trace .= $trace_line['line']."|";
				$trace .= str_replace('_APAS_','::',$trace_line['function'])."{[newline]}";
			}
		}
		return $trace;
	}

	/**
	 * _ReponseXML
	 *
	 * @access private
	 * @return string
	 */
	protected function _ReponseXML() {
		$xml_text = "<EXCEPTION>\n";
		if( is_string($this->m_error)) {
			$xml_text = $xml_text."\t<MESSAGE><![CDATA[".$this->m_error."]]></MESSAGE>\n";
			$xml_text = $xml_text."\t<CODE><![CDATA[0]]></CODE>\n";
			$xml_text = $xml_text."\t<MODE><![CDATA[0]]></MODE>\n";
			$xml_text = $xml_text."\t<DEBUG_INFO><![CDATA[0]]></DEBUG_INFO>\n";
			$xml_text = $xml_text."\t<TYPE><![CDATA[0]]></TYPE>\n";
			$xml_text = $xml_text."\t<USER_INFO><![CDATA[0]]></USER_INFO>\n";
		}
		if( is_subclass_of($this->m_error,'Exception') || ( get_class($this->m_error) == 'Exception')) {
			$xml_text = $xml_text."\t<MESSAGE><![CDATA[".$this->m_error->getMessage()."]]></MESSAGE>\n";
			$xml_text = $xml_text."\t<CODE><![CDATA[".$this->m_error->getCode()."]]></CODE>\n";
			$xml_text = $xml_text."\t<MODE><![CDATA[0]]></MODE>\n";
			$xml_text = $xml_text."\t<DEBUG_INFO><![CDATA[".$this->getErrorTrace()."]]></DEBUG_INFO>\n";
			$xml_text = $xml_text."\t<TYPE><![CDATA[". get_class($this->m_error)."]]></TYPE>\n";
			$xml_text = $xml_text."\t<USER_INFO><![CDATA[]]></USER_INFO>\n";
		}
		if($this->m_text != '')$xml_text = $xml_text."<TEXT>".$this->m_text."</TEXT>\n";
		$xml_text = $xml_text."</EXCEPTION>\n";
		return $xml_text;
	}
}
/**
 * Remonte une exception
 *
 * @param string $extension
 * @param string $action
 * @param array $context
 * @param Exceotion $error
 * @return string
 */
function xfer_returnError($extension,$action,$context,$error) {
	$error_rep = new Xfer_Container_Exception($extension,$action,$context);
	$error_rep->setData($error,"");
	return $error_rep->getReponseXML();
}
//@END@
?>
