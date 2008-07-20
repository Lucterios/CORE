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
require_once "PrintStructureItem.inc.php";

class PrintArea extends PrintAbstract
{
	var $extent=0;
	var $name='';
	var $data;
	var $containers=array();

	var $_SpaceHeight;
	var $_XMLNode=null;

	function __init()
	{
    	$this->extent=0;
    	$this->containers=array();
	}

	function read($DomNode)
	{
		PrintAbstract::read($DomNode);
 		if ($DomNode->has_child_nodes())
 		{
			$cmp_list=array('text'=>'PrintText','table'=>'PrintTable','image'=>'PrintImage');
			foreach($DomNode->child_nodes() as $SubNodes)
			{
			   $node_name=$SubNodes->node_name();
           	   if (array_key_exists($node_name,$cmp_list))
           	   {
           	   		$class=$cmp_list[$node_name];
           	   		$obj=new $class();
           	   		$obj->read($SubNodes);
        	   		array_push($this->containers,$obj);
           	   }
			}
       	}

	}

	function PrintArea($name='body')
	{
		$this->PrintAbstract();
		$this->__init();
		$this->name=$name;
	}

	function declareToFo()
	{
		$fostring='';
		if ($this->name=='body')
		{
			$fostring.="\t\t".'<fo:region-body ';
			if ($this->DocOwner->header->extent>0)
				$fostring.='margin-top="'.$this->DocOwner->header->extent.'mm" ';
			if ($this->DocOwner->bottom->extent>0)
				$fostring.='margin-bottom="'.$this->DocOwner->bottom->extent.'mm" ';
			if ($this->DocOwner->left->extent>0)
				$fostring.='margin-left="'.$this->DocOwner->left->extent.'mm" ';
			if ($this->DocOwner->rigth->extent>0)
				$fostring.='margin-right="'.$this->DocOwner->rigth->extent.'mm" ';
			$fostring.="/>\n";
		}
		else if ($this->extent>0)
			$fostring.="\t\t".'<fo:region-'.$this->name.' extent="'.$this->extent.'mm"/>'."\n";
		return $fostring;
	}

	function __TansformBefore()
	{
		$fostring='';
		if ($this->name=='body')
			$fostring.="\t".'<fo:flow flow-name="xsl-region-body">'."\n";
		else if (($this->extent>0) && (count($this->containers)>0))
			$fostring.="\t".'<fo:static-content flow-name="xsl-region-'.$this->name.'">'."\n";
		return $fostring;
	}
	function __Tansform()
	{
                $data_itemlist=$this->DumpToList($this->_XMLNode);
		$fostring='';
		if (($this->name=='body') || (($this->extent>0) && (count($this->containers)>0)))
			foreach($this->containers as $contain)
			{
				$contain->DocOwner=&$this->DocOwner;
                                if (strtoupper(get_class($contain))=="PRINTTEXT")
				    $contain->__print_text->data_itemlist=$data_itemlist;
				$contain->AreaOwner=&$this;
				$fostring.=$contain->TansformToFo();
			}
		if (($this->name=='body') && (count($this->containers)==0))
		{
			$obj=new PrintText();
			$obj->DocOwner=&$this->DocOwner;
			$obj->AreaOwner=&$this;
			$fostring.=$obj->TansformToFo();
		}
		return $fostring;
	}
	function __TansformAfter()
	{
		$fostring='';
		if ($this->name=='body')
			$fostring.="\t".'</fo:flow>'."\n";
		else if (($this->extent>0) && (count($this->containers)>0))
			$fostring.="\t".'</fo:static-content>'."\n";
		return $fostring;
	}

	function getXMLNode($KeyName)
	{
		$xml_node=null;
		if ($this->DocOwner->XMLDataDom!=null)
		{
			if ($KeyName!='')
			{
				if ($KeyName[0]=='/')
				{
					$KeyName=substr($KeyName,1);
					$RootDomNode = $this->DocOwner->XMLDataDom->document_element();
				}
				else
					$RootDomNode = $this->_XMLNode;
				if ($RootDomNode!=null)
				{
					$key_list=split('/',$KeyName);
					if ($RootDomNode->node_name()==array_shift($key_list))
					{
						$xml_node=$RootDomNode;
						foreach($key_list as $key_item)
						{
							$nodes=$xml_node->get_elements_by_tagname($key_item);
							if (count($nodes)>0)
								$xml_node=$nodes[0];
							else
							{
								$xml_node=null;
								break;
							}
						}
					}
				}
			}
			else
				$xml_node=$this->_XMLNode;
		}
		return $xml_node;
	}

	function getXMLNodes($KeyName)
	{
            $key_last="";
            $key_root="";
            $nodes=array();
            if (($KeyName!=null) && (trim($KeyName)!=""))
            {
                $pos = strrpos($KeyName,"/");
                if ($pos === false)
                {
                    $key_last=$KeyName;
                    $key_root="";
                }
                else
                {
                    $key_last= substr($KeyName,$pos+1);
                    $key_root= substr($KeyName,0,$pos);
                }
                $xml_node=$this->getXMLNode($key_root);
                if (($xml_node!=null) && ($xml_node->has_child_nodes()))
                            foreach($xml_node->child_nodes() as $SubNodes)
                            if ($SubNodes->node_name()==$key_last)
                                        array_push($nodes,$SubNodes);
            }
            return $nodes;
	}


	function getXMLValue($KeyName)
	{
		$xml_node=$this->getXMLNode($KeyName);
		return $this->getXMLNodeDump($xml_node);
	}

	function DumpToList($node)
	{
		$xml_item=array();
		if (($node!=null) && ($node->has_child_nodes()))
			foreach($node->child_nodes() as $SubNodes)
			{
				$name=$SubNodes->node_name();
				if (array_key_exists($name,$xml_item))
					$xml_item[$name][]=$SubNodes;
				else
					$xml_item[$name]=array($SubNodes);
			}
		return $xml_item;
	}

	function getXMLList($KeyName)
	{
		$xml_list=array();
		$nodes=$this->getXMLNodes($KeyName);
		foreach($nodes as $SubNodes)
			array_push($xml_list,$this->DumpToList($SubNodes));
		return $xml_list;
	}

 	function evalText($xml_text,$DataList=array())
 	{
 		$pos1=strpos($xml_text,'[(');
 		while (!($pos1===false))
 		{
 			$pos2=strpos($xml_text,')]',$pos1);
 			if ($pos2)
 			{
                            $var_name=substr($xml_text,$pos1+2,$pos2-$pos1-2);
                            $xml_text=substr($xml_text,0,$pos1).$this->getXMLValue($var_name).substr($xml_text,$pos2+2);
 			}
 			else
                            $xml_text=substr($xml_text,0,$pos1).substr($xml_text,$pos1+2);
 			$pos1=strpos($xml_text,'[(');
 		}

 		$pos1=strpos($xml_text,'[{');
 		while (!($pos1===false))
 		{
 			$pos2=strpos($xml_text,'}]',$pos1);
 			if ($pos2)
 			{
 			    $var_name=substr($xml_text,$pos1+2,$pos2-$pos1-2);
 			    if (array_key_exists($var_name,$DataList))
 			    	$xml_text=substr($xml_text,0,$pos1).$this->getXMLNodeDump($DataList[$var_name][0]).substr($xml_text,$pos2+2);
		            else
 			    	$xml_text=substr($xml_text,0,$pos1).substr($xml_text,$pos2+2);
 			}
 			else
 				$xml_text=substr($xml_text,0,$pos1).substr($xml_text,$pos1+2);
 			$pos1=strpos($xml_text,'[{');
 		}
 		return $xml_text;
 	}
}

class PrintPage extends PrintAbstract
{
	var $margin_right=10;
	var $margin_left=10;
	var $margin_bottom=10;
	var $margin_top=10;
	var $page_width=210;
	var $page_height=297;

	var $header;
	var $bottom;
	var $left;
	var $rigth;
	var $body=array();

	var $__ReadProperty=array('header'=>'PrintArea','bottom'=>'PrintArea','left'=>'PrintArea','rigth'=>'PrintArea','body'=>'PrintArea');

	var $XMLDataDom;

	function __init()
	{
		$this->margin_right=10;
		$this->margin_left=10;
		$this->margin_bottom=10;
		$this->margin_top=10;
		$this->page_width=210;
		$this->page_height=297;

		$this->header=new PrintArea('before');
		$this->bottom=new PrintArea('after');
		$this->left=new PrintArea('start');
		$this->rigth=new PrintArea('end');
		$this->body=array();
	}

	function PrintPage()
	{
		$this->PrintAbstract($this);
		$this->__init();
		$this->XMLDataDom=null;
	}

	function Load($printString)
	{
		$RootDomNode=null;
		$DomDocument = domxml_open_mem("<?xml version='1.0' encoding='iso-8859-1'?>\n".$printString);
		if ($DomDocument!=null)
		{
			$RootDomNode = $DomDocument->document_element();
			if ($RootDomNode->node_name()=='model')
				$this->read($RootDomNode);
		}
		return $RootDomNode;
	}

	function setXML($XMLString)
	{
		if (($XMLString!=null) && (trim($XMLString)!=""))
			$this->XMLDataDom=domxml_open_mem("<?xml version='1.0' encoding='iso-8859-1'?>".$XMLString);
		else
			$this->XMLDataDom=null;
	}

	function __TansformBefore()
	{
		$fostring='';
		$fostring.='<fo:root xmlns:fo="http://www.w3.org/1999/XSL/Format">';
		$fostring.='<fo:layout-master-set>'."\n";
		$fostring.="\t".'<fo:simple-page-master
		margin-right="'.$this->margin_right.'mm"
		margin-left="'.$this->margin_left.'mm"
		margin-bottom="'.$this->margin_bottom.'mm"
		margin-top="'.$this->margin_top.'mm"
		page-width="'.$this->page_width.'mm"
		page-height="'.$this->page_height.'mm"
		master-name="page">'."\n";
		$fostring.=$this->header->declareToFo();
		$fostring.=$this->left->declareToFo();
		$fostring.=$this->rigth->declareToFo();
		$fostring.=$this->bottom->declareToFo();
		$bd=new PrintArea();
		$bd->DocOwner=&$this;
		$fostring.=$bd->declareToFo();
		$fostring.="\t".'</fo:simple-page-master>';
		$fostring.='</fo:layout-master-set>';
		return $fostring;
	}
	function __Tansform()
	{
		$fostring='';
		$this->header->DocOwner=&$this;
		$this->left->DocOwner=&$this;
		$this->rigth->DocOwner=&$this;
		$this->bottom->DocOwner=&$this;
		foreach($this->body as $body_item)
		{
			$body_item->DocOwner=&$this;
			$nodes=$body_item->getXMLNodes($body_item->data);
			if (count($nodes)==0)
				array_push($nodes,null);
			foreach($nodes as $XMLNode)
			{
				$body_item->_XMLNode=&$XMLNode;
				$this->header->_XMLNode=&$XMLNode;
				$this->left->_XMLNode=&$XMLNode;
				$this->rigth->_XMLNode=&$XMLNode;
				$this->bottom->_XMLNode=&$XMLNode;
				$fostring.='<fo:page-sequence master-reference="page">'."\n";
				$fostring.=$this->header->TansformToFo();
				$fostring.=$this->left->TansformToFo();
				$fostring.=$this->rigth->TansformToFo();
				$fostring.=$this->bottom->TansformToFo();
				$fostring.=$body_item->TansformToFo();
				$fostring.='</fo:page-sequence>';
			}
		}
		return $fostring;
	}
	function __TansformAfter()
	{
		$fostring='';
		$fostring.='</fo:root>';
		return $fostring;
	}
}

function getDBModel($extension,$printmodel,$reference=0)
{
    require_once("CORE/printmodel.tbl.php");
    $print_model_obj=new DBObj_CORE_printmodel;
    $print_model_obj->extensionid=$extension;
    $print_model_obj->identify=$printmodel;
    $print_model_obj->reference=$reference;
    if ($print_model_obj->find()>0)
    {
        $print_model_obj->fetch();
        $row=array($print_model_obj->id,$print_model_obj->model);
        return $row;
    }
    else
    {
        return array(0,'');
    }
}

function checkDBModel($extension,$printmodel,$reference=0)
{
    global $rootPath;
    if (!isset($rootPath))
        $rootPath="./";
    $model='';
    $id=0;
    $printfile=$rootPath."extensions/$extension/$printmodel.prt.php";
    if (is_file($printfile))
    {
        list($id,$model) = getDBModel($extension,$printmodel,$reference);
        if ($id==0)
        {
            require_once $printfile;
            require_once("CORE/printmodel.tbl.php");
            $model=new DBObj_CORE_printmodel;
            $model->extensionid=$extension;
            $model->identify=$printmodel;
            $model->reference=$reference;
            $model->titre=$Title;
            $model->model=trim($MODEL_DEFAULT);
            $model->insert();
            list($id,$model) = getDBModel($extension,$printmodel,$reference);
        }
    }
    //else
    return array($id,$model);
}

function getDBReport($extension,$printmodel,$reference)
{
    require_once("CORE/finalreport.tbl.php");
    $report=new DBObj_CORE_finalreport;
    $report->extensionid=$extension;
    $report->identify=$printmodel;
    $report->reference=$reference;
    if ($report->find()>0)
    {
        $report->fetch();
        $row=array($report->report,$report->titre);
	return $row;
    }
    else
	return false;
}

function LoadOrBuildReport($extension,$printmodel,$modelRef,$printRef,$params,$overwrite,$title)
{
    $printfile="extensions/$extension/$printmodel.prt.php";
    if (($printRef>0) && !$overwrite)
        $report=getDBReport($extension,$printmodel,$printRef);
    else
        $report=false;
    if ($overwrite || !is_array($report))
    {
        list($id,$model)=checkDBModel($extension,$printmodel,$modelRef);
        if ($id>0)
        {
            require_once $printfile;
            $prt=new PrintPage();
            $root=$prt->Load($model);

            $XmlDataFctName=$extension."_APAS_".$printmodel."_getXmlData";
            $xml_data=$XmlDataFctName($params);

            $prt->setXML($xml_data);
            $report=array($prt->TansformToFo(),$title);
        }
        else
            $report=false;
        if (($printRef>0) && is_array($report))
        {
            $rep=str_replace('"',"'",$report[0]);
            $report=new DBObj_CORE_finalreport;
            $report->extensionid=$extension;
            $report->identify=$printmodel;
            $report->reference=$printRef;
            $report->report=$rep;
            $report->titre=$report[1];
            $report->insert();
        }
    }
    return $report;
}



//@END@
?>
