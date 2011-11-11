<?php

include_once("CORE/fpdf.php");
include_once("CORE/XMLparse.inc.php");

class LPDF extends FPDF
{
	var $xmlModel;
	var $currentPage;

	
	var $My_Font; //[(B-I-U-A),..]
	var $My_Font_Name;
	var $My_Font_Size;

	var $zone;

	function LPDF($xmlContent)
	{
	    // Appel au constructeur parent
	    $this->FPDF('P', 'mm', 'A4');
	    // Initialisation
	    $this->clearFont('Arial',12,0,0,0,0);

	    $xmlParse=new COREParser();
	    $xmlParse->setInputString($xmlContent);
	    $xmlParse->parse();
	    $this->xmlModel=$xmlParse->getResult();
	    $this->initialModel();
	}

	function Error($msg)
	{
		// Fatal error
		throw new Exception($msg);
	}

	function GetStringHeight()
	{
		$ut = &$this->CurrentFont['ut'];
		return ceil($ut*$this->FontSizePt/120);
	}

	function clearFont($name,$size,$B,$I,$U,$A)
	{
	    if ($name=='sans-serif') $name='Arial';
	    $this->My_Font=array(array($B,$I,$U,$A));
	    $this->My_Font_Name=$name;
	    $this->My_Font_Size=$size;
	    $this->affectStyle();
	}

	function initialModel()
	{
	    $margin_right=$this->xmlModel->getAttributeValue('margin_right');
	    $margin_left=$this->xmlModel->getAttributeValue('margin_left');
	    $margin_bottom=$this->xmlModel->getAttributeValue('margin_bottom');
	    $margin_top=$this->xmlModel->getAttributeValue('margin_top');
	    $page_width=$this->xmlModel->getAttributeValue('page_width');
	    $page_height=$this->xmlModel->getAttributeValue('page_height');
	    $this->SetMargins($margin_left,$margin_top,$margin_right);
	    $this->w = $page_width;
	    $this->h = $page_height;
	    if ($this->w<$this->h) {
		$this->DefOrientation = 'P';
		$size=array($this->w,$this->h);
	    }
	    else {
		$this->DefOrientation = 'L';
		$size=array($this->h,$this->w);
	    }
	    $this->DefPageSize = $size;
	    $this->CurPageSize = $size;
	    $this->CurOrientation = $this->DefOrientation;
	    $this->wPt = $this->w*$this->k;
	    $this->hPt = $this->h*$this->k;
	    $this->SetAutoPageBreak(true,$margin_bottom);
	    $this->AliasNbPages();
	}

	function WriteHTML($html)
	{
	    // Parseur HTML
	    $html = str_replace("\n",' ',$html);
	    $a = preg_explode('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
	    foreach($a as $i=>$e)
	    {
		if($i%2==0)
		{
		    // Texte
		    $this->Write($this->GetStringHeight(),iconv('UTF-8', 'windows-1252', $e));
		}
		else
		{
		    // Balise
		    if($e[0]=='/')
			$this->CloseTag(strtoupper(substr($e,1)));
		    else
		    {
			// Extraction des attributs
			$a2 = explode(' ',$e);
			$tag = strtoupper(array_shift($a2));
			$attr = array();
			foreach($a2 as $v)
			{
			    if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
				$attr[strtoupper($a3[1])] = $a3[2];
			}
			$this->OpenTag($tag,$attr);
		    }
		}
	    }
	    $this->Ln($this->GetStringHeight());
	}

	function OpenTag($tag, $attrib)
	{
	    // Balise ouvrante
	    if($tag=='FONT') {
		list($B,$I,$U,$A)=$this->My_Font[count($this->My_Font)-1];	
		$keys=array_keys($attrib);
		foreach($keys as $key) {
			if (strpos($key,'FONT-WEIGHT')>0) $B=1;
			if (strpos($key,'FONT-STYLE')>0) $B=1;
			if (strpos($key,'TEXT-DECORATION')>0) $B=1;
		}
		$newFont=array($B,$I,$U,$A);
		//echo "att:".print_r($attrib,true)." |$keys[0]|  B:'".$attrib[$keys[0]]."' -F=".print_r($newFont,true);
		$this->My_Font[]=$newFont;
		$this->affectStyle();
	    }
	}

	function CloseTag($tag)
	{
	    // Balise fermante
	    if($tag=='FONT') {
		  unset($this->My_Font[count($this->My_Font)-1]);
		  $this->affectStyle();
	    }
	}

	function affectStyle()
	{
	    // Modifie le style et sÃ©lectionne la police correspondante
	    $last_font=$this->My_Font[count($this->My_Font)-1];
	    $style = '';
	    $list=array('B', 'I', 'U');
	    foreach($list as $id=>$val) {
		if ($last_font[$id]>0)
		    $style.=$val;
	    }
	    echo "New style:$style - last_font=".print_r($last_font,true)."\n";
	    $this->SetFont($this->My_Font_Name,$style,$this->My_Font_Size);
	}

	function getTextXML($item) {
	      $cdatas=$item->cdatas;
	      $subitems=$item->getChilds();	      
	      $id=0;
	      $value=$cdatas[$id++];
	      foreach($subitems as $subitem) {
		  $name=strtoupper($subitem->getTagName());
		  $value.="<$name";
		  foreach($subitem->attribs as $atName=>$atVal)
		      $value.=" '$atName'='$atVal'";
		  $value.='>';
		  $value.=$this->getTextXML($subitem);
		  $value.="</$name>";
		  $value.=$cdatas[$id++];
	      }
	      return $value;
	}

	function addText($item){
	    $text_align=$item->getAttributeValue('text_align');
	    $align=($text_align=='center')?1:($text_align=='end')?2:0;
	    $b=($item->getAttributeValue('font_weight')=='bold')?1:0;
	    $this->clearFont($item->getAttributeValue('font_family'),$item->getAttributeValue('font_size'),$b,0,0,$align);

	    $XMLtext=$this->getTextXML($item);
	    $XMLtext=str_replace(array('</BR>'),'',$XMLtext);
	    $XMLtext_list=explode('<BR>',$XMLtext);
	    foreach($XMLtext_list as $XMLtext_item) {
			$this->WriteHTML($XMLtext_item);
	    }
	}

	function addImage($item){
	}

	function addTable($item){
	}

	function addItems($items) {
		$val="";
		foreach($items as $item) {
			$name=strtoupper($item->getTagName());
			$this->setPosition($item->getAttributeValue('left'),$item->getAttributeValue('top'));
			if ($name=='TEXT')
			      $this->addText($item);
			if ($name=='IMAGE')
			      $this->addImage($item);
			if ($name=='TABLE')
			      $this->addTable($item);
		}
	}

	function setPosition($posX,$posY) {
		switch($this->zone) {
			case 0:// header
				$this->x=$this->rMargin+$posX;
				$this->y=$this->tMargin+$posY;
				break;
			case 1:// body
				$this->x=$this->rMargin+$posX;
				$this->y=$this->tMargin+$posY;
				$headers=$this->currentPage->getChildsByTagName("header");
				if (count($headers)==1) {
					$header=$headers[0];
					$this->y+=$header->getAttributeValue('extent');
				}
				break;
			case 2:// footer
				$this->x=$this->rMargin+$posX;
				$this->y=$this->h-$this->bMargin+$posY;
				$bottoms=$this->currentPage->getChildsByTagName("bottom");
				if (count($bottoms)==1) {
					$bottom=$bottoms[0];
					$this->y-=$bottom->getAttributeValue('extent');
				}
				break;
		}
	}

	// En-tête
	function Header()
	{
		$this->zone=0;
		$headers=$this->currentPage->getChildsByTagName("header");
		if (count($headers)==1) {
			$header=$headers[0];
			$this->addItems($header->getChilds());
			$this->y=$this->tMargin+$header->getAttributeValue('extent');
		}
	}

	// Pied de page
	function Footer()
	{
		$this->zone=2;
		$bottoms=$this->currentPage->getChildsByTagName("bottom");
		if (count($bottoms)==1) {
			$bottom=$bottoms[0];
			$this->y=$this->h-$this->bMargin-$bottom->getAttributeValue('extent');
			$this->addItems($bottom->getChilds());
		}
	}

	function run() 
	{
		$pages=$this->xmlModel->getChildsByTagName("page");
		foreach($pages as $page) {
			$this->currentPage=$page;
			$this->AddPage();
			$this->zone=1;
			$bodys=$page->getChildsByTagName("body");
			if (count($bodys)==1) {
				$body=$bodys[0];
				$this->addItems($body->getChilds());
			}
		}
	}
}


?>