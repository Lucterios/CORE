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
// --- Last modification: Date 04 February 2010 21:43:53 By  ---

//@BEGIN@
require_once 'CORE/ConvertPrintModel.inc.php';

class PrintListing
{
	/**
	 * Hauteur de page (mm)
	 *
	 * @var int
	 */
	public $Height=297;
	/**
	 * Largeur de page (mm)
	 *
	 * @var int
	 */
	public $Width=210;
	/**
	 * Largeur des marges verticales de page (mm)
	 *
	 * @var int
	 */
	public $MargeVertical=10;
	/**
	 * Hauteur des marges horizontales de page (mm)
	 *
	 * @var int
	 */
	public $MargeHorizontal=10;
	/**
	 * Hauteur des entetes et des pieds de page (mm)
	 *
	 * @var int
	 */
	public $HeaderFooterHeight=10;

	/**
	 * Titre du listing
	 *
	 * @var string
	 */
	public $Title="";
	/**
	 * Entête du listing
	 *
	 * @var string
	 */
	public $Header="";
	/**
	 * Pied de page du listing
	 *
	 * @var string
	 */
	public $Footer="";

	/**
	 * Taille police entete/pied page
	 *
	 * @var int
	 */
	public $SizeHeader=11;
	/**
	 * Taille police colonne table
	 *
	 * @var int
	 */
	public $SizeColumn=9;
	/**
	 * Taille police corps table
	 *
	 * @var int
	 */
	public $SizeRow=6;

	/**
	 * description des colonnes du tableaux => Array de  (Nom,Taille)
	 *
	 * @var array
	 */
	public $GridHeader=Array();
	/**
	 * description du contenu du tableaux => Array de Array de text
	 *
	 * @var array
	 */
	public $GridContent=Array();

	/**
	 * Constructeur PrintListing
	 *
	 * @return PrintListing
	 */
	public function __construct($aTitle)
	{
		$this->Title=$aTitle;
	}

	private function initDocument()
	{
		$content=sprintf('<model margin_right="%1$d.0" margin_left="%1$d.0" margin_bottom="%2$d.0" margin_top="%2$d.0" page_width="%3$d.0" page_height="%4$d.0">', $this->MargeHorizontal,$this->MargeVertical,$this->Width,$this->Height);
		return $content;
	}

	private function getListHeader()
	{
		$content=sprintf('<header extent="%d.0" name="before">',$this->HeaderFooterHeight);
		$content.=sprintf('<text height="%1$d.0" width="%2$d.0" top="0.0" left="0.0" padding="1.0" spacing="0.0" border_color="black" border_style="" border_width="0.2" text_align="center" line_height="%3$d" font_family="sans-serif" font_weight="" font_size="%4$d">',$this->HeaderFooterHeight,$this->Width-2*$this->MargeHorizontal,$this->SizeHeader+1,$this->SizeHeader);
		$content.=ModelConverter::convertApasFormat($this->Header);
		$content.='</text>';
		$content.='</header>';
		return $content;
	}

	private function getListBody()
	{
		$content='<body extent="0.0" data="" name="body">';
		$content.=sprintf('<table height="%1$d.0" width="%2$d.0" top="10.0" left="0.0" padding="1.0" spacing="10.0" border_color="black" border_style="" border_width="0.2">', $this->Height-2*$this->MargeVertical-2*$this->HeaderFooterHeight, $this->Width-2*$this->MargeHorizontal);
		foreach($this->GridHeader as $column) {
			$content.=sprintf('<columns width="%d.0" data="">',$column[1]);
			$content.=sprintf('<cell data="" display_align="center" border_color="black" border_style="solid" border_width="0.2" text_align="center" line_height="%1$d" font_family="sans-serif" font_weight="" font_size="%2$d">',$this->SizeColumn+1,$this->SizeColumn);
			$content.=ModelConverter::convertApasFormat($column[0]);
			$content.='</cell>';
			$content.='</columns>';
		}
		foreach($this->GridContent as $row) {
			$content.='<rows>';
			foreach($row as $cell) {
				if (substr($cell,0,20)=="data:image/*;base64,") $img='image="1"'; else $img='image="0"';
				$content.=sprintf('<cell data="" display_align="center" border_color="black" border_style="solid" border_width="0.2" text_align="start" line_height="%1$d" font_family="sans-serif" font_weight="" font_size="%2$d" '.$img.'>',$this->SizeRow+1,$this->SizeRow);
				$content.=ModelConverter::convertApasFormat($cell);
				$content.='</cell>';
			}
			$content.='</rows>';
		}

		$content.='</table>';
		$content.='</body>';
		return $content;


	}

	private function getListFooter()
	{
		$content=sprintf('<bottom extent="%d.0" name="after">',$this->HeaderFooterHeight);
		$content.=sprintf('<text height="%1$d.0" width="%2$d.0" top="0.0" left="0.0" padding="1.0" spacing="0.0" border_color="black" border_style="" border_width="0.2" text_align="center" line_height="%3$d" font_family="sans-serif" font_weight="" font_size="%4$d">',$this->HeaderFooterHeight,$this->Width-2*$this->MargeHorizontal,$this->SizeHeader+1,$this->SizeHeader);
		$content.=ModelConverter::convertApasFormat($this->Footer);
		$content.='</text>';
		$content.='</bottom>';
		return $content;
	}

	public function generate()
	{
		$content='<?xml version="1.0" encoding="ISO-8859-1"?>';
		$content.=$this->initDocument();
		$content.='<page>';
		$content.=$this->getListHeader();
		$content.=$this->getListFooter();
		$content.='<left extent="0.0" name="start"/>';
		$content.='<rigth extent="0.0" name="end"/>';
		$content.=$this->getListBody();
		$content.='</page>';
		$content.='</model>';
		$content=str_replace('>',">\n",$content);
		logAutre("PrintListing:\n".$content);
		return $content;
	}
}
//@END@
?>
