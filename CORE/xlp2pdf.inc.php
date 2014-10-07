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
require_once('CORE/fpdf.php');
require_once('CORE/XMLparse.inc.php');

class TextFormated
{
	private $nb_line=0;
	private $content=array(array());

	private $B;
	private $I;
	private $U;
	private $fontname;
	private $size;
	private $intersize;
	private $color;

	public $sesure_chars=array(' ','-','/');

	public $LIST_COLOR=array(
		'black'=>array(0,0,0),
		'red'=>array(255,0,0),
		'green'=>array(0,255,0),
		'blue'=>array(0,0,255),
		'yellow'=>array(255,255,0),
		'cyan'=>array(0,255,255),
		'purple'=>array(255,0,255),
		'grey'=>array(192,192,192),
		'white'=>array(255,255,255)
	);

	private function _addtext($text) {
		$text = str_replace("\n"," ",$text);
		$text = preg_replace('/\s\s+/', ' ',$text);
		$text = trim($text);
		$text = str_replace(array('&eacute;','&egrave;'),array('e','e'),$text);
		$text = iconv('utf-8', 'cp1252', $text);
		$style = '';
		foreach(array('B', 'I', 'U') as $s)
		{
			if($this->$s>0)
				$style .= $s;
		}
		$this->content[$this->nb_line][]=array($text,$style,$this->fontname,$this->size,$this->intersize,$this->color);
	}

	private function _sub_text($xmlitem) {
		$tagname = strtolower($xmlitem->getTagName());
		if ($tagname=='br') {
			$this->nb_line++;
			return;
		}
		$last_color=$this->color;
		$last_B=$this->B;
		$last_I=$this->I;
		$last_U=$this->U;
		if ($tagname=='font') {
			if ($xmlitem->getAttributeValue('Font-weight')=='bold')
				$this->B=1;
			if ($xmlitem->getAttributeValue('Font-style')=='italic')
				$this->I=1;
			if ($xmlitem->getAttributeValue('text-decoration')=='underline')
				$this->U=1;
			if ($xmlitem->getAttributeValue('color')!==false)
				$this->color=$xmlitem->getAttributeValue('color');
		}
		$child_list=$xmlitem->getChilds();
		for($idx=0;$idx<=count($child_list);$idx++) {
			if (array_key_exists($idx,$xmlitem->cdatas)) {
				$text=trim($xmlitem->cdatas[$idx]);
				if ($text!='') {
					$this->_addtext($text);
				}
			}
			if (isset($child_list[$idx])) {
				$this->_sub_text($child_list[$idx]);
			}
		}
		$this->B=$last_B;
		$this->I=$last_I;
		$this->U=$last_U;
		$this->color=$last_color;
	}

	function TextFormated($xmltest,$fontname,$size,$intersize) {
		$this->B = 0;
		$this->I = 0;
		$this->U = 0;
		$this->color='black';
		$this->fontname=$fontname;
		$this->size=$size;
		$this->intersize=$intersize;
   		$this->_sub_text($xmltest);
	}

	function _getNewTextlines($fpdf,$width) {
		$new_content=array();
		$width_lines=array();
		$line_idx=0;
		while($line_idx<count($this->content)) {
			$line=$this->content[$line_idx];
			$line_w=0;
			foreach($line as $item) {
				$fpdf->SetFont($item[2],$item[1],$item[3]);
				$line_w+=$fpdf->GetStringWidth($item[0]);
			}
			if ($line_w>$width) {
				$new_line=array_merge($line);
				$line_w=0;
				$item_idx=0;
				while ($item_idx<count($line)) {
					$item=$line[$item_idx];
					$fpdf->SetFont($item[2],$item[1],$item[3]);
					$item_w=$fpdf->GetStringWidth($item[0]);
					if (($item_w+$line_w)>$width) {
						$text_current=$item[0];
						$index=(int)(strlen($text_current)/2);
						while (($index<strlen($text_current)) && (!in_array($text_current[$index],$this->sesure_chars)))
							$index++;
						if ($index<strlen($text_current)) {
							$item_w=$fpdf->GetStringWidth(substr($text_current,0,$index+1));
							if (($item_w+$line_w)>$width)
								$index=strlen($text_current);
						}
						if ($index>=strlen($text_current)) {
							$index=(int)(strlen($text_current)/2);
							while (($index>0) && ($text_current[$index]!=' ') && (!in_array($text_current[$index],$this->sesure_chars)))
								$index--;
						}
						if ($index>0) {
							$new_line[$item_idx][0]=substr($text_current,0,$index+1);
							for($otherid=$item_idx+1;$otherid<count($line);$otherid++)
								unset($new_line[$otherid]);
							$this->content[$line_idx][$item_idx][0]=substr($text_current,$index+1);
							for($otherid=0;$otherid<$item_idx;$otherid++)
								unset($this->content[$line_idx][$item_idx]);
							$item_w=$fpdf->GetStringWidth($new_line[$item_idx][0]);
							$new_content[]=$new_line;
							$width_lines[]=$line_w+$item_w;
						}
						else {
							$new_content[]=$new_line;
							$width_lines[]=$line_w+$item_w;
							$line_idx++;
						}
					}
					$line_w=$line_w+$item_w;
					$item_idx++;
				}
			}
			else {
				$new_content[]=$line;
				$width_lines[]=$line_w;
				$line_idx++;
			}
		}
		return array($new_content,$width_lines);
	}

	function fillPDF($fpdf,$posX,$width,$align,$simpleText=True) {
		list($new_content,$width_lines) = $this->_getNewTextlines($fpdf,$width);
		$interspace=0;
		$last_itersize=4;
		$min_y=$fpdf->y;
		$max_y=$fpdf->y;
		$line_idx=0;
		foreach($new_content as $line) {
			if ($line_idx>0)
				$fpdf->Ln($last_itersize);
			$line_w=$width_lines[$line_idx];
			if (($align=='left') || ($align=='start'))
				$currentX=$posX;
			else if ($align=='center')
				$currentX=$posX+($width-$line_w)/2;
			else
				$currentX=$posX+$width-$line_w;
			foreach($line as $item) {
				$current_text=$item[0];
				$item_color=$item[5];
				if (array_key_exists($item_color,$this->LIST_COLOR)) {
					$fpdf->SetTextColor($this->LIST_COLOR[$item_color][0],$this->LIST_COLOR[$item_color][1],$this->LIST_COLOR[$item_color][2]);
				}
				$fpdf->SetFont($item[2],$item[1],$item[3]);
				$fpdf->SetX($currentX);
				$fpdf->Write(0,$current_text);
				$last_itersize=$item[4]/$fpdf->k;
				if ($line_idx==0) {
					$min_y=min($min_y,$fpdf->y-($item[3]/$fpdf->k)/2);
				}
				$max_y=max($max_y,$fpdf->y+($item[3]/$fpdf->k)/2);
				$currentX+=$fpdf->GetStringWidth($current_text);
			}
			if (!$simpleText && ($line_idx>0))
				$interspace+=$last_itersize;
			$line_idx++;
		}
		if ($simpleText && (abs($min_y-$max_y)<0.001)) {
			$min_y=$fpdf->y-($this->size/$fpdf->k)/2;
			$max_y=$fpdf->y+($this->size/$fpdf->k)/2;
		}
		$fpdf->SetY($max_y);
		return array($min_y,$max_y,$interspace);
	}
}

class LucteriosPDF extends FPDF
{
	private $_xml;

	private $current_page;

	private $y_offset;

	private function _init() {
		$this->AddFont('sans-serif','','FreeSans.php');
		$this->AddFont('sans-serif','B','FreeSansB.php');
		$this->AddFont('sans-serif','I','FreeSansI.php');
		$this->AddFont('sans-serif','BI','FreeSansBI.php');
		$model=$this->xml->getResult();
		$this->w = (float)$model->getAttributeValue('page_width');
		$this->h = (float)$model->getAttributeValue('page_height');
		if ($this->w > $this->h) {
			$this->DefOrientation = 'L';
			$this->DefPageSize = array($this->h,$this->w);
		}
		else {
			$this->DefOrientation = 'P';
			$this->DefPageSize = array($this->w,$this->h);
		}
		$this->CurPageSize = $this->DefPageSize;
		$this->CurOrientation = $this->DefOrientation;
		$this->wPt = $this->w*$this->k;
		$this->hPt = $this->h*$this->k;
		$this->lMargin = (float)$model->getAttributeValue('margin_left');
		$this->tMargin = (float)$model->getAttributeValue('margin_top');
		$this->rMargin = 0;

		$page=$model->getChildsByTagName('page');
		$header=$page[0]->getChildsByTagName('header');
		$this->header_h=(float)$header[0]->getAttributeValue('extent');
		$bottom=$page[0]->getChildsByTagName('bottom');
		$this->bottom_h=(float)$bottom[0]->getAttributeValue('extent');

		$bo_m=(float)$model->getAttributeValue('margin_bottom');
		$this->SetAutoPageBreak(true,$this->bottom_h+$bo_m);
		$this->SetFont('Helvetica','',12);

	}

	private function getTopComponent($xmlitem) {
		$currentY=$this->y_offset+(float)$xmlitem->getAttributeValue('top');
		$spacing=(float)$xmlitem->getAttributeValue('spacing');
		if (abs($spacing)>0.001) {
			if (abs($this->y-$this->y_offset)>0.001)
				$currentY=max($this->y_offset,$this->y+$spacing);
			else
				$currentY=$this->y_offset;
		}
		return $currentY;
	}

	private function parse_text($xmltext) {
		$currentY=$this->getTopComponent($xmltext);
		$txt = new TextFormated($xmltext,$xmltext->getAttributeValue('font_family'),
			$xmltext->getAttributeValue('font_size'), $xmltext->getAttributeValue('line_height'));
		$this->SetY($currentY);
		list($min_y,$max_y,$interspace)=$txt->fillPDF($this,$this->lMargin+(float)$xmltext->getAttributeValue('left'),
			(float)$xmltext->getAttributeValue('width'),$xmltext->getAttributeValue('text_align'));
		$this->Ln(max($interspace,$max_y-$min_y));
	}

	private function parse_image($xmlimage,$posY=0,$posX=0,$weight=0) {
		if ($xmlimage->getAttributeValue('top')!==false)
			$currentY=$this->getTopComponent($xmlimage);
		else
			$currentY=$posY;
		if ($xmlimage->getAttributeValue('left')!==false)
			$currentX=$this->lMargin+(float)$xmlimage->getAttributeValue('left');
		else
			$currentX=$posX;
		if ($xmlimage->getAttributeValue('width')!==false) {
			$currentW=(float)$xmlimage->getAttributeValue('width');
			$currentH=(float)$xmlimage->getAttributeValue('height');
		}
		else {
			$currentW=$weight;
			$currentH=0;
		}
		$img_content=trim($xmlimage->getCData());
		$is_base64=(substr($img_content,0,20)=='data:image/*;base64,');
		if ($is_base64) {
				global $tmpPath;
				$img_file=realpath(tempnam($tmpPath,'img'));
				file_put_contents($img_file,base64_decode(substr($img_content,20)));
		}
		else {
			$img_file=$img_content;
		}
		if (is_file($img_file)) {
			$type=image_type_to_extension(exif_imagetype($img_file));
			$type=str_replace('.','',$type);
			$this->setY($currentY);
			$this->Image($img_file,$currentX,null,$currentW,$currentH,$type);
			if ($is_base64)
				unlink($img_file);
		}
		$max_y=$this->y;
		$min_y=$currentY;
		$interspace=($max_y-$min_y);
		return array($min_y,$max_y,$interspace);
	}

	private function _addTableLine($cells,$width_columns,$posX,$posY,$lineTop) {
		$col_x=0;
		$col_interspace=0;
		$col_min_y=$posY;
		$col_max_y=$posY;
		for($cell_idx=0;$cell_idx<count($cells);$cell_idx++) {
			$width_column=$width_columns[$cell_idx];
			$cell=$cells[$cell_idx];
			$is_image=$cell->getAttributeValue('image');
			if ($is_image=='1') {
				list($min_y,$max_y,$interspace)=$this->parse_image($cell,$posY+1,$posX+$col_x+1,$width_column-2);
			}
			else {
				$txt = new TextFormated($cell,$cell->getAttributeValue('font_family'),
					$cell->getAttributeValue('font_size')-(($lineTop<0)?0.3:0),$cell->getAttributeValue('line_height'));
				$this->SetY($posY+2.5);
				$txt->sesure_chars=array(' ','/');
				list($min_y,$max_y,$interspace)=$txt->fillPDF($this,$posX+$col_x-0.2,
					$width_column-1.2,$cell->getAttributeValue('text_align'),False);
			}
			$col_x+=$width_column;
			$col_min_y=min($col_min_y,$min_y-0.5);
			$col_max_y=max($col_max_y,$max_y+0.5);
			$col_interspace=max($col_interspace,$interspace+0.5);
		}
		$col_x=0;
		if ($lineTop>=0) {
			$col_min_y=$lineTop;
			$col_max_y+=0.5;
			$col_interspace+=1.2;
		}
		$this->Line($posX, $col_min_y, $posX, $col_max_y);
		foreach($width_columns as $width_column) {
			$col_x+=$width_column;
			$this->Line($posX+$col_x, $col_min_y, $posX+$col_x, $col_max_y);
		}
		if ($lineTop<0)
			$this->Line($posX, $col_min_y, $posX+$col_x, $col_min_y);
		$this->Line($posX, $col_max_y, $posX+$col_x, $col_max_y);
		$this->Ln($col_interspace);
		return array($col_min_y,$col_max_y);
	}

	private function parse_table($xmltable) {
		$currentY=$this->getTopComponent($xmltable);
		$currentX=(float)$xmltable->getAttributeValue('left');
		$width_columns=array();
		$xmlcolumns=$xmltable->getChildsByTagName('columns');
		$cellcolumns=array();
		foreach($xmlcolumns as $xmlcolumn) {
			$width_columns[]=(float)$xmlcolumn->getAttributeValue('width');
			$cells=$xmlcolumn->getChildsByTagName('cell');
			$cellcolumns[]=$cells[0];
		}
		list($last_top,$last_bottom)=$this->_addTableLine($cellcolumns,$width_columns,$currentX+$this->lMargin,$currentY,-1);
		$rows=$xmltable->getChildsByTagName('rows');
		foreach($rows as $row) {
			if(($this->y+($last_bottom-$last_top)*1.1)>$this->PageBreakTrigger)
			{
				$this->AddPage();
				$this->y=$this->y_offset;
				$currentY=$this->y_offset;
				list($last_top,$last_bottom)=$this->_addTableLine($cellcolumns,$width_columns,$currentX+$this->lMargin,$currentY,-1);
			}
			$cells=$row->getChildsByTagName('cell');
			list($last_top,$last_bottom)=$this->_addTableLine($cells,$width_columns,$currentX+$this->lMargin,$this->y,$last_bottom);
		}
	}

	private function _parse_comp($comp,$y_offset) {
		$last_y_offset=$this->y_offset;
		$this->y_offset=$y_offset;
		$this->y=$y_offset;
		foreach($comp->getChilds() as $child) {
			$tagname = strtolower($child->getTagName());
			$mtd = 'parse_'.$tagname;
			if(method_exists($this,$mtd))
				$this->$mtd($child);
			else
				$this->Error("Unsupported component: $tagname");
		}
		$this->y_offset=$last_y_offset;
	}

	public function Header() {
		$header=$this->current_page->getChildsByTagName('header');
		$this->_parse_comp($header[0],$this->tMargin);
	}

	public function Footer() {
		$bottom=$this->current_page->getChildsByTagName('bottom');
		$this->_parse_comp($bottom[0],-1*$this->bMargin);
	}

	public function Execute($xml_content) {
		$this->xml = new COREParser();
		$this->xml->setInputString($xml_content);
		$this->xml->parse();
		$this->_init();
		$pages=$this->xml->getResult()->getChildsByTagName('page');
		foreach($pages as $page) {
			$this->current_page=$page;
			$bodies=$this->current_page->getChildsByTagName('body');
			foreach($bodies as $body) {
				$this->AddPage();
				$this->_parse_comp($body,$this->header_h+$this->tMargin);
			}
		}
	}
}

function transforme_xml2pdf($xml_content) {
	$lpdf = new LucteriosPDF();
	$lpdf->Execute($xml_content);
	return $lpdf->Output('','S');
}

function transform_file_xml2pdf($xml_file,$pdf_file) {
	$pdf_content = transforme_xml2pdf(file_get_contents($xml_file));
	@unlink($pdf_file);
	if ($pdf_content != '')
		file_put_contents($pdf_file, $pdf_content);
	return is_file($pdf_file);
}
//@END@
?>
