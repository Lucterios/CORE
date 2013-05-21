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
 * fichier gérant le parsing XML
 *
 * @author Pierre-Oliver Vershoore/Laurent Gay
 * @version 0.10
 * @package Lucterios
 * @subpackage Outils
 */

/**
 * Classe de presentation d'un element XML
 *
 * @package Lucterios
 * @subpackage Outils
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class XmlElement
{
	protected $tagName;
	protected $cdata;
	protected $attribs;
	protected $childs;
	protected $father;
	protected $num_cdata=0;

	/**
	 * Constructeur
	 * @param string $tagName Nom du tag
	 */
	public function __construct($tagName) {
		$this->tagName = $tagName;
		$this->attribs = array();
		$this->childs = array();
		$this->cdatas=array("");
	}

	/**
	 * Associe un parent
	 * @param XmlElement $father Parent
	 * @return bool
	 */
	public function father(&$father) {
		if("xmlelement" == strtolower(get_class($father))) {
			$this->father =& $father;
			return true;
		}
		else return false;
	}

	/**
	 * Associe une donnee
	 * @param string $cdata Donnee CData
	 * @return bool
	 */
	public function setCData($cdata) {
		if (!isset($this->cdatas[$this->num_cdata]))
		    $this->cdatas[$this->num_cdata]="";
		$this->cdatas[$this->num_cdata]=$this->cdatas[$this->num_cdata].$cdata;
		return true;
	}

	/**
	 * Ajoute un attribut
	 * @param string $attrName Nom de l'attribut
	 * @param string $attrVal Valeur de l'attribut
	 * @return bool
	 */
	public function addAttribute($attrName, $attrVal) {
		$this->attribs[strtolower($attrName)] = $attrVal;
		return true;
	}

	/**
	 * Ajoute une liste d'attributs
	 * @param array $attribs dictionnaire d'attributs
	 * @return bool
	 */
	public function addAttributeArray($attribs) {
		foreach($attribs as $attr_name=>$attr_value)
			$this->addAttribute($attr_name,$attr_value);
		return true;
	}

	/**
	 * Ajoute un fils
	 * @param XmlElement $child Donnee CData
	 * @return bool
	 */
	public function addChild(&$child) {
		if("xmlelement" == strtolower(get_class($child))) {
			$nb = count($this->childs);
			$child->father($this);
			$this->childs[$nb] =& $child;
			$this->num_cdata++;
			return true;
		}
		else return false;
	}

	/**
	 * Retourne le nom du tag
	 * @return string
	 */
	public function getTagName() {
		return $this->tagName;
	}

	/**
	 * Retourne le parent
	 * @return XmlElement
	 */
	public function &getParent() {
		return $this->father;
	}

	/**
	 * Retourne le text du premier fils
	 * @param string $tagName tag recherche
	 * @return string
	 */
	public function getCDataOfChild($tagName) {
		$childs=$this->getChildsByTagName($tagName);
		if (count($childs)==1) {
			$child=$childs[0];
			return $child->getCData();
		}
		else
			return "";
	}

	/**
	 * Retourne le text des fils
	 * @return string
	 */
	public function getCData() {
		return implode('',$this->cdatas);
	}

	/**
	 * Retourne la valeur d'un attribut
	 * @param string $attrName nom de l'attribut
	 * @return string
	 */
	public function getAttributeValue($attrName) {
		if(array_key_exists(strtolower($attrName), $this->attribs))
			return $this->attribs[strtolower($attrName)];
		else return false;
	}

	/**
	 * Retourne les fils
	 * @param string $tagName tag recherche
	 * @return array
	 */
	public function getChildsByTagName($tagName) {
		$result = array();
		foreach($this->childs as $child) {
			if(strtolower($tagName) == strtolower($child->getTagName())) {
				array_push($result, $child);
			}
			$result = array_merge($result, $child->getChildsByTagName($tagName));
		}
		return $result;
	}

	/**
	 * Retourne tous les fils
	 * @return array
	 */
	public function getChilds() {
		return $this->childs;
	}
}

/**
 * Classe de parsing XML
 *
 * @package Lucterios
 * @subpackage Outils
 * @author Pierre-Oliver Vershoore/Laurent Gay
 */
class COREParser
{
	protected $content="";
	protected $document;
	protected $opentags;

	/**
	 * Constructeur
	 */
	public function __construct() {
		$this->opentags = "empty";
		$this->document = "empty";
	}

	private function debug($msg) {
		//printf("%s: %s<br>\n", $this->id, $msg/*, $this->nbopentags*/);
		/*print_r($this->document);
		print "opentags: ";
		print_r($this->opentags);
		print "<br>\n";
		if("xmlelement" == strtolower(get_class($this->opentags))) print "objet actuel: ".$this->opentags->getTagName()."<br>\n";*/
	}

	private function affich() {
		print_r($this->document);
	}

	/**
	  * gestion de l'element ouvrant
	  *
	  * @param  resource  $xp ressource de l'analyseur XML
	  * @param  string    $name nom de l'element
	  * @param  array     $attribs attributs
	  */
	public function startHandler($xp, $name, $attribs) {
		if(!is_object($this->document) || ("xmlelement" != strtolower(get_class($this->document)))) {
			$this->document =new XmlElement($name);
			$this->document->addAttributeArray($attribs);
			$this->opentags =& $this->document;
			$this->debug("creation du doc");
		}
		else {
			$newElem =new XmlElement($name);
			$newElem->addAttributeArray($attribs);
			$this->opentags->addChild($newElem);
			$this->opentags =& $newElem;
			$this->debug("ouverture de balise $name");
		}
	}

	/**
	  * gestion de l'element fermant
	  *
	  * @param  resource  $xp ressource de l'analyseur XML
	  * @param  string    $name nom de l'element
	  */
	public function endHandler($xp, $name) {
		$this->opentags =& $this->opentags->getParent();
		$this->debug("fermeture de balise $name");
	}

	/**
	  * gestion des donnees
	  *
	  * @param  resource  $xp ressource de l'analyseur XML
	  * @param  string    $cdata donnee
	  */
	public function cdataHandler($xp, $cdata) {
		$this->opentags->setCData($cdata);
	}

	/**
	  * Retourne l'element racine
	  * @return XmlElement
	  */
	public function getResult() {
		return $this->document;
	}

	/**
	  * Retourne les elements desirees
	  * @param  string    $tagName nom de l'element
	  * @return array
	  */
	public function getByTagName($tagName) {
		return $this->document->getChildsByTagName($tagName);
	}

	/**
	  * Retourne les commentaire
	  * @return array()
	  */
	public function getComment() {
		$comment=array();
		$xml_input=$this->fp;
		$pos1=strpos($xml_input,'<!--');
		$pos2=strpos($xml_input,'-->');
		while (($pos1 >= 0) && ($pos2 > $pos1)) {
			$line=trim(substr($xml_input,$pos1+4,$pos2-$pos1-8));
			$comment[]=$line;
			$xml_input=substr($xml_input,$pos2+3);
			$pos1=strpos($xml_input,'<!--');
			$pos2=strpos($xml_input,'-->');
		}
		return $comment;
	}
  
	/**
	  * Assigne le text XML
	  * @param  string    $content XML
	  */
	public function setInputString($content) {
		$this->setInput($content);
	}
 
	/**
	  * Assigne le text XML
	  * @param  string    $content XML
	  */
	public function setInput($content) {
		$this->content=$content;
	}

	/**
	  * Parse le XML
	  */
	public function parse() {
		$parser=@xml_parser_create();
		xml_set_object($parser, $this);
		xml_set_element_handler($parser, 'startHandler', 'endHandler');
		xml_set_character_data_handler($parser, 'cdataHandler');
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, true);
		xml_parse($parser, $this->content, false);
		xml_parser_free($parser);
	}
  
}


//@END@
?>
