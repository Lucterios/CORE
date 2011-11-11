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
// --- Last modification: Date 03 January 2008 22:48:46 By Laurent GAY ---

//@BEGIN@

/**
 * fichier gérant les classe de menu
 *
 * @author Pierre-Oliver Vershoore/Laurent Gay
 * @version 0.10
 * @package Lucterios
 * @subpackage Xfer
 */

require_once 'xfer.inc.php';

/**
 * Classe élément d'un menu
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Menu_Item extends Xfer_Action
{
	/**
	 * Identifiant de menu
	 *
	 * @var string
	 */
	public $m_id="";
	/**
	 * Liste des sous menu
	 *
	 * @var array
	 */
	public $m_sub_menus=array();
	/**
	 * Shortcut de menu
	 *
	 * @var string
	 */
	public $m_Shortcut="";

	/**
	 * Aide de menu
	 *
	 * @var string
	 */
	public $m_Help="";

	/**
	 * Constructor
	 *
	 * @param string $id
	 * @param string $title
	 * @param string $icon
	 * @param string $extension
	 * @param string $action
	 * @param integer $modal
	 * @param string $Help
	 * @return Xfer_Menu_Item
	 */
	public function __construct($id,$title,$icon="",$extension="",$action="",$modal="",$shortcut="",$help="")
	{
		parent::__construct($title,$icon,$extension,$action,$modal);
		$this->m_id = $id;
		$this->m_Shortcut = $shortcut;
		$this->m_Help = urlencode(htmlentities($help));
		if ($this->m_id=='')
		{
			$this->_begin_tag="<MENUS>";
			$this->_end_tag="</MENUS>";
		}
		else
		{
			$this->_begin_tag=sprintf("<MENU id='%s' shortcut='%s'",$this->m_id,$this->m_Shortcut)."%s>";
			$this->_end_tag="</MENU>";
		}
	}
	/**
	 * Ajoute un sous menu
	 *
	 * @param Xfer_Menu_Item $submenu
	 */
	public function addSubMenu($submenu)
	{
		array_push($this->m_sub_menus,$submenu);
	}

	/**
	 * _ReponseXML
	 *
	 * @access private
	 * @return string
	 */
	protected function _getContent()
	{
		$xml_text = "";
		if ($this->m_title!="")
			$xml_text="<![CDATA[".$this->m_title."]]>";
		if ($this->m_Help!="")
			$xml_text.="<HELP><![CDATA[".$this->m_Help."]]></HELP>";
		if (count($this->m_sub_menus)!=0)
			foreach($this->m_sub_menus as $sub_menu)
				$xml_text=$xml_text.$sub_menu->getReponseXML();
		return $xml_text;
	}
}

/**
 * Classe containaire d'un menu
 *
 * @package Lucterios
 * @subpackage Xfer
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class Xfer_Container_Menu extends Xfer_Container_Abstract
{
	/**
	 * Ensemble des menus principaux
	 *
	 * @var Xfer_Menu_Item
	 */
	public $m_main_menus;
	/**
	 * Constructor
	 *
	 * @param string $extension
	 * @param string $action
	 * @param array $context
	 * @return Xfer_Container_Menu
	 */
	public function __construct($extension,$action,$context=array())
	{
		parent::__construct($extension,$action,$context);
		$this->m_observer_name="CORE.Menu";
		$this->m_main_menus = &new Xfer_Menu_Item("","","");
	}
	/**
	 * Ajoute un menu principale
	 *
	 * @param Xfer_Menu_Item $submenu
	 */
	public function addSubMenu($submenu)
	{
		$this->m_main_menus->addSubMenu($submenu);
	}

	/**
	 * _ReponseXML
	 *
	 * @access private
	 * @return string
	 */
	protected function _ReponseXML()
	{
		return $this->m_main_menus->getReponseXML();
	}
}
















//@END@
?>
