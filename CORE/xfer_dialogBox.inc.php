<?php
//
//  This file is part of Lucterios.
//
//  Lucterios is free software; you can redistribute it and/or modify
//  it under the terms of the GNU General Public License as published by
//  the Free Software Foundation; either version 2 of the License, or
//  (at your option) any later version.
//
//  Lucterios is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License for more details.
//
//  You should have received a copy of the GNU General Public License
//  along with Lucterios; if not, write to the Free Software
//  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
//
//	Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY
//
//@BEGIN@

/**
 * fichier gerant une boite de dialogue transferee
 *
 * @author Pierre-Oliver Vershoore/Laurent Gay
 * @version 0.10
 * @package Lucterios
 * @subpackage Xfer
 */

/**
 * Objets Xfer
 */
require_once('CORE/xfer.inc.php');

/**
 * Information
 *
 */
define("XFER_DBOX_INFORMATION",1);
/**
 * Confirmation
 *
 */
define("XFER_DBOX_CONFIRMATION",2);
/**
 * Avertissement
 *
 */
define("XFER_DBOX_WARNING",3);
/**
 * Erreur
 *
 */
define("XFER_DBOX_ERROR",4);

/**
 * Classe containaire d'une boite de dialogue
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Container_DialogBox extends Xfer_Container_Abstract
{
	/**
	 * Type de dialogue
	 *
	 * @var integer
	 */
	public $m_type=0;
	/**
	 * Message du dialogue
	 *
	 * @var string
	 */
	public $m_text="";
	/**
	 * Liste de actions
	 *
	 * @var array
	 */
	public $m_actions=array();
	/**
	 * Constructor
	 *
	 * @param string $extension
	 * @param string $action
	 * @param array $context
	 * @return Xfer_Container_DialogBox
	 */
	public function __construct($extension,$action,$context=array())
	{
		parent::__construct($extension,$action,$context);
		$this->m_observer_name="Core.DialogBox";
	}
	/**
	 * Assigne le type et le message a la boite de dialogue
	 *
	 * @param string $text
	 * @param integer $type
	 */
	public function setTypeAndText($text,$type)
	{
		$this->m_type=$type;
		$this->m_text=$text;
	}
	/**
	 * Ajoute une action a la boite de dialogue
	 *
	 * @param Xfer_Action $action
	 */
	public function addAction($action)
	{
		array_push($this->m_actions,$action);
	}
	/**
	 * _ReponseXML
	 *
	 * @access protected
	 * @return string
	 */
	protected function _ReponseXML()
	{
		$xml_text=sprintf("<TEXT type='%d'><![CDATA[%s]]></TEXT>",$this->m_type,$this->m_text);
		if (count($this->m_actions)!=0)
		{
			$xml_text=$xml_text."<ACTIONS>";
			foreach($this->m_actions as $action)
				$xml_text=$xml_text.$action->getReponseXML();
			$xml_text=$xml_text."</ACTIONS>";
		}
		return $xml_text;
	}
}

//@END@
?>
