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


class PrintAbstract
{
	var $DocOwner;
	var $AreaOwner;

	function PrintAbstract()
	{
		$this->DocOwner=null;
		$this->AreaOwner=null;
		$this->__init();
	}

	function __init(){}

 	function __TansformBefore(){}
	function __Tansform(){}
	function __TansformAfter(){}

	function TansformToFo()
	{
        $fostring='';
		$fostring.=$this->__TansformBefore();
		$fostring.=$this->__Tansform();
		$fostring.=$this->__TansformAfter();
		return $fostring;
	}

	var $__ReadProperty=array();
	var $__NoAttributProperty=array();

	function getNodeDump($SubNodes)
	{
		$domNode = domxml_new_xmldoc("1.0");
		$cloneNode = $SubNodes->clone_node(true);
		$domNode->append_child($cloneNode);
		$result=$domNode->dump_mem(true,"ISO-8859-1");
		$pos = strpos($result,"?>");
		if ($pos!==false)
			$result = substr($result,$pos+2);
		return $result;
	}

	function getXMLNodeDump($xml_node)
	{
		$xml_value='';
		if ($xml_node!=null)
		{
			$owner_document = $xml_node->owner_document();
			if ($xml_node->has_child_nodes())
				foreach($xml_node->child_nodes() as $SubNodes)
					$xml_value.=$this->getNodeDump($SubNodes);
		}
		return trim($xml_value);
	}

	function read($DomNode)
	{
		$this->__init();
		if ($DomNode->has_attributes())
		{
			$prop_values=get_object_vars($this);
			$Array = $DomNode->attributes();
			foreach ($Array as $DomAttribute)
			{
				$att_name=$DomAttribute->name();
				if(array_key_exists($att_name,$prop_values))
					$this->$att_name=$DomAttribute->value();
			}
		}
 		if ($DomNode->has_child_nodes())
 		{
           foreach($DomNode->child_nodes() as $SubNodes)
           {
           	   $node_name=$SubNodes->node_name();
           	   if (array_key_exists($node_name,$this->__ReadProperty))
           	   {
           	   		$class=$this->__ReadProperty[$node_name];
           	   		if (is_array($this->$node_name))
           	   		{
           	   			$obj=new $class();
						$obj->DocOwner=& $this->DocOwner;
						$obj->AreaOwner=& $this->AreaOwner;
           	   			$obj->read($SubNodes);
           	   			array_push($this->$node_name,$obj);
           	   		}
           	   		else
           	   			$this->$node_name->read($SubNodes);
           	   }
           }
       	}
	}
}

class PrintBorder extends PrintAbstract
{
	var $border_color="black";
	var $border_style="";
	var $border_width=0.2;

	function __init()
	{
		$this->border_color="black";
		$this->border_style="";
		$this->border_width=0.2;
	}

	function getBordure()
	{
		$fostring='';
		if ($this->border_style!='')
		{
			$fostring.='border-color="'.$this->border_color.'" ';
			$fostring.='border-style="'.$this->border_style.'" ';
			$fostring.='border-width="'.$this->border_width.'mm" ';
		}
		return $fostring;
	}
}

class PrintContainer extends PrintBorder
{
	var $height=10;
	var $width=10;
	var $top=0;
	var $left=0;
	var $padding=1;
	var $spacing=0;

	function __init()
	{
		PrintBorder::__init();
		$this->height=10;
		$this->width=10;
		$this->top=0;
		$this->left=0;
		$this->padding=1;
		$this->spacing=0;
	}

	function __TansformBefore()
	{
		$fostring='';
		if ($this->spacing==0)
		{
			$my_owner=$this->AreaOwner;
			if (is_object($my_owner))
				$space_heigth=$my_owner->_SpaceHeight;
			else
				$space_heigth=0;
			$top_heigth=$this->top+$this->height;
			if (($this->top>=0) && ($this->height>=0) && ($space_heigth<$top_heigth))
			{
				$Space=$top_heigth-$space_heigth;
				$fostring.="\t\t".'<fo:block space-before="'.$Space.'mm"/>'."\n";
				$my_owner->_SpaceHeight=($this->top+$this->height);
			}
			$fostring.="\t\t".'<fo:block-container '.$this->getBordure();
			$fostring.='height="'.$this->height.'mm" ';
			$fostring.='width="'.$this->width.'mm" ';
			if ($this->top>=0)
				$fostring.='top="'.$this->top.'mm" ';
			if ($this->left>=0)
				$fostring.='left="'.$this->left.'mm" ';
			$fostring.='padding="'.$this->padding.'mm" ';
			$fostring.='position="absolute">'."\n";
		}
		else
		{
			$fostring.="\t\t".'<fo:block '.$this->getBordure();
			$fostring.='padding="'.$this->padding.'mm" ';
			$fostring.='space-before="'.$this->spacing.'mm" ';
			$fostring.='>'."\n";
		}
		return $fostring;
	}
	function __Tansform()
	{
		return '';
	}
	function __TansformAfter()
	{
		if ($this->spacing==0)
            return "\t\t".'</fo:block-container>'."\n";
        else
            return "\t\t".'</fo:block>'."\n";
	}
}

class PrintAbstractText extends PrintAbstract
{
	var $text_align="start";
	var $line_height=0;
	var $font_family="sans-serif";
	var $font_weight="";
	var $font_size=12;
	var $content="";
//	var $orientation="";

	var $margeleft=-1;
	var $data_itemlist=array();
 	var $Container=null;

	function __init()
	{
		$this->text_align="start";
		$this->line_height=0;
		$this->font_family="sans-serif";
		$this->font_weight="";
		$this->font_size=12;
		$this->content="";
                $this->space=0;
        $this->NoAttributProperty[]="content";
	}

	function read($DomNode)
	{
		PrintAbstract::read($DomNode);
		$this->content=$this->getXMLNodeDump($DomNode);
	}

	function getSVGText($y=0)
	{
		$txt="\t\t\t<text x=\"0\" y=\"$y\" style=\"";
		$txt.='font-size='.$this->font_size;
		$txt.=';font-family='.$this->font_family;
		if ($this->font_weight!='')
			$txt.=';font-weight='.$this->font_weight;
		$txt.="\">\n";
		return $txt;
	}
	
 	function __TansformBefore()
 	{
		/*if ($this->orientation!="")
		{
			$fostring="\t\t\t<fo:instream-foreign_object content-height=\"25mm\" content-width=\"5mm\">\n";
			$fostring.="\t\t\t<svg height=\"25mm\" width=\"5mm\">\n";
			$fostring.="\t\t\t".'<g transform="rotate('.$this->orientation.')">'."\n";
			$fostring.=$this->getSVGText();
		}
		else*/
		{
			$fostring="\t\t\t".'<fo:block ';
			if ($this->margeleft>=0)
				$fostring.='margin-left="'.$this->margeleft.'mm" ';
			$fostring.='text-align="'.$this->text_align.'" ';
			if ($this->line_height>0)
				$fostring.='line-height="'.$this->line_height.'pt" ';
			$fostring.='font-family="'.$this->font_family.'" ';
			if ($this->font_weight!='')
				$fostring.='font-weight="'.$this->font_weight.'" ';
			$fostring.='font-size="'.$this->font_size.'pt">'."\n";
		}
		return $fostring;
 	}
	function __Tansform()
 	{
 		$xml_text=$this->content;
		$my_owner=$this->AreaOwner;
 		$xml_text=$my_owner->evalText($xml_text,$this->data_itemlist);
 		$xml_text=str_replace('{[newline]}','<br/>',$xml_text);
 		$xml_text=str_replace('{[bold]}','<b>',$xml_text);
 		$xml_text=str_replace('{[/bold]}','</b>',$xml_text);
 		$xml_text=str_replace('{[italic]}','<i>',$xml_text);
 		$xml_text=str_replace('{[/italic]}','</i>',$xml_text);
 		$xml_text=str_replace('{[underline]}','<u>',$xml_text);
 		$xml_text=str_replace('{[/underline]}','</u>',$xml_text);
 		$xml_text=str_replace('<b>','<fo:inline Font-weight="bold">',$xml_text);
 		$xml_text=str_replace('<i>','<fo:inline Font-style="italic">',$xml_text);
 		$xml_text=str_replace('<u>','<fo:inline text-decoration="underline">',$xml_text);
 		$xml_text=str_replace('</b>','</fo:inline>',$xml_text);
 		$xml_text=str_replace('</i>','</fo:inline>',$xml_text);
 		$xml_text=str_replace('</u>','</fo:inline>',$xml_text);
 		$xml_text=str_replace('<font','<fo:inline',$xml_text);
 		$xml_text=str_replace('</font>','</fo:inline>',$xml_text);
		$pos=strpos($xml_text,'<br/>');
		if ($pos)
		{
			//$y=20;
			//if ($this->orientation=="")
			$ret_line="\n".$this->__TansformAfter().$this->__TansformBefore();
			while ($pos)
			{
				$xml_text2=substr($xml_text,0,$pos);
				//if ($this->orientation!="")
				//	$xml_text2.="\t\t\t</text>\n".$this->getSVGText($y);
				//else
					$xml_text2.=$ret_line;
				$xml_text2.=trim(substr($xml_text,$pos+5));
				$xml_text=$xml_text2;
				$pos=strpos($xml_text,'<br/>');
				//$y=$y+20;
			}
		}
		return "\t\t\t".$xml_text."\n";
 	}

	function __TansformAfter()
 	{
		/*if ($this->orientation!="")
		{
			$fostring="\t\t\t</text>\n";
			$fostring.="\t\t\t</g>\n";
			$fostring.="\t\t\t</svg>\n";
			$fostring.="\t\t\t</fo:instream-foreign_object>";
		}
		else*/
			$fostring="\t\t\t".'</fo:block>'."\n";
		return $fostring;
 	}
}

class PrintText extends PrintContainer
{
	var $__print_text;
	function __init()
	{
		PrintContainer::__init();
		$this->__print_text=new PrintAbstractText();
		$this->__print_text->DocOwner=& $this->DocOwner;
		$this->__print_text->AreaOwner=& $this->AreaOwner;
	}

	function read($DomNode)
	{
		PrintAbstract::read($DomNode);
		$this->__print_text->read($DomNode);
		$this->__print_text->Container=& $this;
	}

 	function __TansformBefore()
 	{
		if (($this->spacing>0) && ($this->left>=0))
			$this->__print_text->margeleft=$this->left;
		else
			$this->__print_text->margeleft=-1;
		$fostring=Printcontainer::__TansformBefore();
		$this->__print_text->Container=& $this;
		$fostring.=$this->__print_text->__TansformBefore();
		return $fostring;
 	}
	function __Tansform()
 	{
		$this->__print_text->DocOwner=&$this->DocOwner;
		$this->__print_text->AreaOwner=&$this->AreaOwner;
		$this->__print_text->Container=& $this;
		return $this->__print_text->__Tansform();
 	}
	function __TansformAfter()
 	{
		$this->__print_text->Container=& $this;
		$fostring=$this->__print_text->__TansformAfter();
		$fostring.=Printcontainer::__TansformAfter();
		return $fostring;
 	}
}

class PrintCell extends PrintBorder
{
	var $__print_text;
	var $data;
        var $display_align='center';

	function __init()
	{
		PrintBorder::__init();
		$this->__print_text=new PrintAbstractText();
		$this->__print_text->DocOwner=& $this->DocOwner;
		$this->__print_text->AreaOwner=& $this->AreaOwner;
		$this->data="";
                $this->display_align='center';
	}
	function read($DomNode)
	{
		PrintAbstract::read($DomNode);
		$this->__print_text->read($DomNode);
	}

 	function __TansformBefore()
 	{
		$fostring="\t\t\t      ".'<fo:table-cell '.$this->getBordure().' padding="2pt"  display-align="'.$this->display_align.'" >'."\n";
		$fostring.="        ".$this->__print_text->__TansformBefore();
		return $fostring;
 	}
	function __Tansform()
 	{
		$this->__print_text->DocOwner=&$this->DocOwner;
		$this->__print_text->AreaOwner=&$this->AreaOwner;
		return "        ".$this->__print_text->__Tansform();
 	}
	function __TansformAfter()
 	{
		$fostring="        ".$this->__print_text->__TansformAfter();
		$fostring.="\t\t\t      ".'</fo:table-cell>'."\n";
		return $fostring;
 	}
}

class PrintRow extends PrintAbstract
{
	var $cell=array();
	var $col_in_supp=false;
	var $data;

	var $data_itemlist=array();

	function __init()
	{
		$this->cells=array();
		$this->data='';
	}
	function newCell($text)
	{
		$new_cell=new PrintCell();
		$new_cell->__print_text->content=$text;
		$new_cell->__print_text->DocOwner=& $this->DocOwner;
		$new_cell->__print_text->AreaOwner=& $this->AreaOwner;
		array_push($this->cells,$new_cell);
		return $new_cell;
	}

	var $__ReadProperty=array('cell'=>'PrintCell');

 	function __TansformBefore()
 	{
 		$fostring="\t\t\t    ".'<fo:table-row>'."\n";
		return $fostring;
 	}
	function __Tansform()
	{
		$fostring='';
		if ($this->col_in_supp)
			$fostring="\t\t\t      ".'<fo:table-cell/>'."\n";
		foreach($this->cell as $cell_item)
		{
			$cell_item->DocOwner=&$this->DocOwner;
			$cell_item->AreaOwner=&$this->AreaOwner;

			$list=array();
			if ($cell_item->data=="")
				$list[]=$this->data_itemlist;
			elseif (array_key_exists($cell_item->data,$this->data_itemlist))
			{
				$items=$this->data_itemlist[$cell_item->data];
				foreach($items as $item)
					$list[]=$this->AreaOwner->DumpToList($item);
			}

			foreach($list as $val)
			{
				$cell_item->__print_text->data_itemlist=$val;
				$fostring.=$cell_item->TansformToFo();
			}
		}
		return $fostring;
	}
	function __TansformAfter()
	{
		$fostring="\t\t\t    ".'</fo:table-row>'."\n";
		return $fostring;
	}
}

class PrintColumn extends PrintAbstract
{
	var $width=10;
	var $cell;
	var $data;

	var $__ReadProperty=array('cell'=>'PrintCell');

	function __init()
	{
		$this->width=10;
		$this->cell=new PrintCell();
		$this->cell->__print_text->DocOwner=& $this->DocOwner;
		$this->cell->__print_text->AreaOwner=& $this->AreaOwner;
		$this->data='';
	}
	function PrintColumn($width=10)
	{
		$this->width=$width;
	}

	function GetColList()
	{
		$data_list=$this->AreaOwner->getXMLList($this->data);
		if (count($data_list)==0)
                    $data_list[""]=array($this->AreaOwner->_XMLNode);
		return $data_list;
	}

 	function __TansformBefore()
 	{
		$this->cell->DocOwner=&$this->DocOwner;
		$this->cell->AreaOwner=&$this->AreaOwner;
		return "";
 	}
	function __Tansform()
	{
		$fostring='';
		foreach($this->GetColList() as $data_itemlist)
		{
			$this->cell->__print_text->data_itemlist=$data_itemlist;
			$fostring.=$this->cell->TansformToFo();
		}
		return $fostring;
	}
	function __TansformAfter()
	{
		return "";
	}
}

class PrintTable extends Printcontainer
{
	var $columns=array();
	var $rows=array();

	var $__ReadProperty=array('columns'=>'PrintColumn','rows'=>'PrintRow');

	function __init()
	{
		Printcontainer::__init();
		$this->columns=array();
		$this->rows=array();
	}

	function newRow()
	{
		$new_row=new PrintRow();
		$new_row->DocOwner=& $this->DocOwner;
		$new_row->AreaOwner=& $this->AreaOwner;
		array_push($this->Body,$new_row);
		return $new_row;
	}

 	function __TansformBefore()
 	{
		$fostring=Printcontainer::__TansformBefore();
		$fostring.="\t\t\t".'<fo:table table-layout="fixed">'."\n";
		if (($this->spacing>0) && ($this->left>=0))
			$fostring.="\t\t\t  ".'<fo:table-column column-width="'.$this->left.'mm"/>'."\n";
		foreach($this->columns as $col)
		{
			$col->DocOwner=&$this->DocOwner;
			$col->AreaOwner=&$this->AreaOwner;
			foreach($col->GetColList() as $col_item)
				$fostring.="\t\t\t  ".'<fo:table-column column-width="'.$col->width.'mm"/>'."\n";
		}
		return $fostring;
 	}

	function __Tansform()
 	{
		$fostring='';
		// Header
		$fostring.="\t\t\t  ".'<fo:table-header>'."\n";
 		$fostring.="\t\t\t    ".'<fo:table-row>'."\n";
		if (($this->spacing>0) && ($this->left>=0))
			$fostring.="\t\t\t      ".'<fo:table-cell/>'."\n";
		foreach($this->columns as $col)
		{
			$col->DocOwner=&$this->DocOwner;
			$col->AreaOwner=&$this->AreaOwner;
			$fostring.=$col->TansformToFo();
		}
 		$fostring.="\t\t\t    ".'</fo:table-row>'."\n";
		$fostring.="\t\t\t  ".'</fo:table-header>'."\n";
		// Body
		$fostring.="\t\t\t  ".'<fo:table-body>'."\n";
		foreach($this->rows as $row)
		{
			$row->col_in_supp=(($this->spacing>0) && ($this->left>=0));
			$data_list=$this->AreaOwner->getXMLList($row->data);
			if (count($data_list)==0)
				array_push($data_list,array());
			foreach($data_list as $data_itemlist)
			{
				$row->data_itemlist=$data_itemlist;
				$row->DocOwner=&$this->DocOwner;
				$row->AreaOwner=&$this->AreaOwner;
				$fostring.=$row->TansformToFo();
			}
		}
		$fostring.="\t\t\t  ".'</fo:table-body>'."\n";
		return $fostring;
 	}
	function __TansformAfter()
 	{
		$fostring="\t\t\t".'</fo:table>'."\n";
		$fostring.=Printcontainer::__TansformAfter();
		return $fostring;
 	}
}

class PrintImage extends PrintContainer
{
	var $picture="";

	function __init()
	{
		Printcontainer::__init();
		$this->picture="";
        $this->NoAttributProperty[]="picture";
	}

	function read($DomNode)
	{
		PrintAbstract::read($DomNode);
		$this->picture=$this->getXMLNodeDump($DomNode);
	}

 	function __TansformBefore()
 	{
		$fostring=Printcontainer::__TansformBefore();
		$fostring.="\t\t\t".'<fo:block ';
		if (($this->spacing>0) && ($this->left>=0))
			$fostring.='margin-left="'.$this->left.'mm" ';
		$fostring.=">\n";
		return $fostring;
 	}
	function __Tansform()
 	{
 		$fostring='';
		$current_file="http://".$_SERVER["SERVER_NAME"].$_SERVER["PHP_SELF"];
		$folder= substr($current_file,0,-1*strlen(strrchr($current_file,"/"))+1);
		$image_url=$this->AreaOwner->evalText($this->picture);
		$image_url=$folder.$image_url;
		$fostring.="\t\t\t\t".'<fo:external-graphic src="url('.$image_url.')"/>'."\n";
		return $fostring;
 	}
	function __TansformAfter()
 	{
		$fostring="\t\t\t".'</fo:block>'."\n";
		$fostring.=Printcontainer::__TansformAfter();
		return $fostring;
 	}
}

?>