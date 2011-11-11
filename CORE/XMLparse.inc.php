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

class XmlElement
{
	protected $tagName;
	protected $cdata;
	protected $attribs;
	protected $childs;
	protected $father;
	protected $num_cdata=0;

	public function __construct($tagName) {
		$this->tagName = $tagName;
		$this->attribs = array();
		$this->childs = array();
		$this->cdatas=array("");
	}

	public function father(&$father) {
		if("xmlelement" == strtolower(get_class($father))) {
			$this->father =& $father;
			return true;
		}
		else return false;
	}

	public function setCData($cdata) {
		if (!isset($this->cdatas[$this->num_cdata]))
		    $this->cdatas[$this->num_cdata]="";
		$this->cdatas[$this->num_cdata]=$this->cdatas[$this->num_cdata].$cdata;
		return true;
	}

	public function addAttribute($attrName, $attrVal) {
		$this->attribs[strtolower($attrName)] = $attrVal;
		//$this->attribs[$attrName] = $attrVal;
		return true;
	}

	public function addAttributeArray($attribs) {
		foreach($attribs as $attr_name=>$attr_value)
			$this->addAttribute($attr_name,$attr_value);
		return true;
	}

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

	public function getTagName() {
		return $this->tagName;
	}

	public function &getParent() {
		return $this->father;
	}

	public function getCDataOfChild($tagName) {
		$childs=$this->getChildsByTagName($tagName);
		if (count($childs)==1) {
			$child=$childs[0];
			return $child->getCData();
		}
		else
			return "";
	}

	public function getCData() {
		return implode('',$this->cdatas);
	}

	public function getAttributeValue($attrName) {
		if(array_key_exists(strtolower($attrName), $this->attribs))
			return $this->attribs[strtolower($attrName)];
		//if(array_key_exists($attrName, $this->attribs))
		//	return $this->attribs[$attrName];
		else return false;
	}

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

	public function getChilds() {
		return $this->childs;
	}
}

class COREParser
{
	protected $content="";
	protected $document;
	protected $opentags;

	public function __construct() {
		$this->opentags = "empty";
		$this->document = "empty";
	}

	public function debug($msg) {
		//printf("%s: %s<br>\n", $this->id, $msg/*, $this->nbopentags*/);
		/*print_r($this->document);
		print "opentags: ";
		print_r($this->opentags);
		print "<br>\n";
		if("xmlelement" == strtolower(get_class($this->opentags))) print "objet actuel: ".$this->opentags->getTagName()."<br>\n";*/
	}


 /**
  * gestion de l'élément ouvrant
  *
  * @access private
  * @param  resource  ressource de l'analyseur XML
  * @param  string    nom de l'élément
  * @param  array     attributs
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
  * gestion de l'élément fermant
  *
  * @access private
  * @param  resource  ressource de l'analyseur XML
  * @param  string    nom de l'élément
  */
	public function endHandler($xp, $name) {
		$this->opentags =& $this->opentags->getParent();
		$this->debug("fermeture de balise $name");
	}

	public function cdataHandler($xp, $cdata) {
		$this->opentags->setCData($cdata);
	}

	public function affich() {
		print_r($this->document);
	}

	public function getResult() {
		return $this->document;
	}

	public function getByTagName($tagName) {
		return $this->document->getChildsByTagName($tagName);
	}

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
  
	public function setInputString($content) {
		$this->setInput($content);
	}
 
	public function setInput($content) {
		$this->content=$content;
	}

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
