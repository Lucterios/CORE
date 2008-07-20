<?php
// library file write by SDK tool
// --- Last modification: Date 16 June 2008 21:50:08 By  ---

//@BEGIN@
/**
* Classe d'element d'impression
*
*/abstract
class PrintItem {
	
	public $tab = 0;
	
	public $x = 0;
	
	public $y = 0;
	
	public $colspan = 1;
	
	public $rowspan = 1;
	
	public $sizeX = 0;
	
	public $height = 0;
	
	public $Left = 0;
	
	public $top = 0;
	
	public $width = 0;
	
	protected $value = null;
	
	protected $printAction;
	
	public function __construct($xfer_comp,$printAction) {
		$this->printAction = $printAction;
		$this->tab = $xfer_comp->tab;
		$this->x = $xfer_comp->x;
		$this->y = $xfer_comp->y;
		$this->colspan = $xfer_comp->colspan;
		$this->rowspan = $xfer_comp->rowspan;
		$this->sizeX = 0;
		$this->height = 0;
		$this->width = 0;
		$this->Left = 0;
		$this->top = 0;
	}
	
	protected function removeFormat($xml_text) {
		$xml_text = str_replace('<b>','',$xml_text);
		$xml_text = str_replace('<i>','',$xml_text);
		$xml_text = str_replace('<u>','',$xml_text);
		$xml_text = str_replace('</b>','',$xml_text);
		$xml_text = str_replace('</i>','',$xml_text);
		$xml_text = str_replace('</u>','',$xml_text);
		$xml_text = str_replace('{[newline]}','',$xml_text);
		$xml_text = str_replace('{[bold]}','',$xml_text);
		$xml_text = str_replace('{[/bold]}','',$xml_text);
		$xml_text = str_replace('{[italic]}','',$xml_text);
		$xml_text = str_replace('{[/italic]}','',$xml_text);
		$xml_text = str_replace('{[underline]}','',$xml_text);
		$xml_text = str_replace('{[/underline]}','',$xml_text);
		return $xml_text;
	}
	
	public function calculPosition() {
		$this->Left = 0;
		$this->width = 0;
		$this->top = $this->printAction->m_top;
		for($i = 0;
		$i<$this->x;
		$i++)$this->Left = $this->Left+$this->printAction->m_colWidth[$i];
		for($i = $this->x;
		$i<($this->x+$this->colspan);
		$i++)$this->width = $this->width+$this->printAction->m_colWidth[$i];
	}
	
	protected function getTextSize($text) {
		$sizeX = 0;
		$sizeY = 0;
		$text = str_replace(array('{[newline]}'),"\n",$text);
		$text_lines = split("\n",$text);
		foreach($text_lines as $line) {
			$sizeX = max($sizeX, strlen($this->removeFormat($line)));
			$sizeY = $sizeY+1;
		}
		return array($sizeX,$sizeY);
	}
	
	public function getspacing() {
		if($this->printAction->Extended) {
			$last_top = $this->printAction->m_last_top;
			$spacing = $this->top-$last_top;
			if($spacing == 0)$spacing = 0.1;
			return $spacing;
		}
		else
		return 0;
	}
	
	public function convertValue($value) {
		if($value == '---')$value = '';
		$value = str_replace('%','°/o',$value);
		return $value;
	}
	
	public function getReportPart() {
	}
}
/**
* Classe d'element d'impression de label
*
*/
class PrintLabel extends PrintItem {
	
	public function __construct($XferLabel,$printAction) { parent:: __construct($XferLabel,$printAction);
		$this->value = $XferLabel->m_value;
		list($this->sizeX,$sizeY) = $this->getTextSize($this->value);
		$this->height = 5*$sizeY;
	}
	
	public function getReportPart() {
		$value = $this->convertValue($this->value);
		$content = sprintf('<text height="%d.0" width="%d.0" top="%d.0" left="%d.0" padding="1.0" spacing="%.1f" border_color="black" border_style="" border_width="0.2" xdisplay_align="left" text_align="left" line_height="10" font_family="sans-serif" font_weight="" font_size="10">',$this->height,$this->width,$this->top,$this->Left,$this->getspacing());
		$content .= ModelConverter:: convertApasFormat($value);
		$content .= '</text>';
		return $content;
	}
}
/**
* Classe d'element d'impression de label
*
*/
class PrintTab extends PrintItem {
	
	public function __construct($XferTab,$printAction) { parent:: __construct($XferTab,$printAction);
		$this->value = "{[italic]}{[underline]}".$XferTab->m_value."{[/underline]}{[/italic]}";
		list($this->sizeX,$sizeY) = $this->getTextSize($this->value);
		$this->height = 6*$sizeY;
	}
	
	public function calculPosition() {
		$sep_height = 0;
		if($this->printAction->TabChangePage && ($this->printAction->m_last_top != 0)) {
			$this->printAction->changePage( false);
			$this->printAction->m_last_top = 0;
		}
		else $sep_height = 3;
		$this->Left = 10;
		$this->printAction->m_top = $this->printAction->m_top+$sep_height;
		$this->top = $this->printAction->m_top;
		$this->width = $this->printAction->largeur_page-2*$this->printAction->marge_horizontal-$this->Left;
	}
	
	public function getReportPart() {
		$content = sprintf('<text height="%d.0" width="%d.0" top="%d.0" left="%d.0" padding="1.0" spacing="%.1f" border_color="black" border_style="" border_width="0.2" xdisplay_align="left" text_align="left" line_height="13" font_family="sans-serif" font_weight="" font_size="13">',$this->height,$this->width,$this->top+$sep_height,$this->Left,$this->getspacing());
		$content .= ModelConverter:: convertApasFormat($this->value);
		$content .= '</text>';
		return $content;
	}
}
/**
* Classe d'element d'impression de Image
*
*/
class PrintImage extends PrintItem {
	var$ImgSize;
	var$DPI = 0.34;
	
	public function __construct($XferImage,$printAction) { parent:: __construct($XferImage,$printAction);
		$this->value = $XferImage->m_value;
		$this->size = 0;
		$this->ImgSize = getimagesize($XferImage->m_value);
		$this->height = (int)($this->ImgSize[1]*$this->DPI);
	}
	
	public function getReportPart() {
		if(($this->ImgSize[0]*$this->DPI)<$this->width) {
			$this->width = (int)($this->ImgSize[0]*$this->DPI);
			$this->height = (int)($this->ImgSize[1]*$this->DPI);
		}
		else $this->height = (int)($this->width*$this->ImgSize[1]/$this->ImgSize[0]);
		$file_size = filesize($this->value);
		$handle = fopen($this->value,'r');
		$img = fread($handle,$file_size);
		$img = chunk_split( base64_encode($img));
		$f = fclose($handle);
		$content = sprintf('<image height="%d.0" width="%d.0" top="%d.0" left="%d.0" padding="1.0" spacing="%.1f" border_color="black" border_style="" border_width="0.2" xdisplay_align="left" text_align="left" line_height="10" font_family="sans-serif" font_weight="" font_size="10">',$this->height,$this->width,$this->top,$this->Left,$this->getspacing());
		$content .= "data:image/*;base64,$img";
		$content .= '</image>';
		return $content;
	}
}
/**
* Classe d'element d'impression de Table
*
*/
class PrintTable extends PrintItem {
	
	private $columns = array();
	
	private $rows = array();
	
	public function __construct($XferTable,$printAction) { parent:: __construct($XferTable,$printAction);
		$size_rows = array(0);
		for($i = 0;
		$i< count($XferTable->m_records);
		$i++) {
			$size_rows[$i+1] = 0;
			$this->rows[$i] = array();
		}
		foreach($XferTable->m_headers as $name => $header) {
			list($sizeX,$sizeY) = $this->getTextSize($header->m_descript);
			$column = array($header->m_descript,$sizeX);
			$size_rows[0] = max($size_rows[0],$sizeY);
			$idx = 0;
			foreach($XferTable->m_records as $record) {
				$value = $this->convertValue("".$record[$name]);
				list($sizeX,$sizeY) = $this->getTextSize($value);
				$size_rows[$idx+1] = max($size_rows[$idx+1],$sizeY);
				$column[1] = max($column[1],$sizeX);
				$this->rows[$idx][] = $value;
				$idx++;
			}
			$this->columns[] = $column;
		}
		if( count($this->rows) == 0) {
			$row = array();
			foreach($this->columns as $column)$row[] = '';
			$this->rows[] = $row;
			list($sizeX,$sizeY) = $this->getTextSize('');
			$size_rows[] = $sizeY;
		}
		$this->sizeX = 0;
		foreach($this->columns as $column)$this->sizeX = $this->sizeX+$column[1];
		$sizeY = 0;
		foreach($size_rows as $size_row)$sizeY = $sizeY+$size_row;
		$this->height = 6*$sizeY;
	}
	
	public function getReportPart() {
		$content = sprintf('<table height="%d.0" width="%d.0" top="%d.0" left="%d.0" padding="1.0" spacing="%.1f" border_color="black" border_style="" border_width="0.2">',$this->height,$this->width,$this->top,$this->Left,$this->getspacing());
		foreach($this->columns as $column) {
			$size_col = (int)($this->width*$column[1]/$this->sizeX);
			$content .= sprintf('<columns width="%d.0" data=""><cell data="" display_align="center" border_color="black" border_style="solid" border_width="0.2" text_align="center" line_height="10" font_family="sans-serif" font_weight="" font_size="10">',$size_col);
			$value = $this->convertValue($column[0]);
			$content .= ModelConverter:: convertApasFormat($value);
			$content .= '</cell></columns>';
		}
		foreach($this->rows as $row) {
			$content .= '<rows data="">';
			foreach($row as $value) {
				$content .= '<cell data="" display_align="center" border_color="black" border_style="solid" border_width="0.2" text_align="start" line_height="11" font_family="sans-serif" font_weight="" font_size="10">';
				$content .= ModelConverter:: convertApasFormat($value);
				$content .= '</cell>';
			}
			$content .= '</rows>';
		}
		$content .= '</table>';
		return $content;
	}
}
/**
* Classe de gestion d'impression d'action
*
*/
class PrintAction {
	/**
	 * extension
	 * @access private
	 */
	private $m_extension = '';
	/**
	 * action
	 * @access private
	 */
	private $m_action = '';
	/**
	 * params
	 * @access private
	 */
	private $m_params = array();
	/**
	 * titre
	 */
	public $Title = "";
	/**
	 * largeur_page
	 */
	public $largeur_page = 210;
	/**
	 * hauteur_page
	 */
	public $hauteur_page = 297;
	/**
	 * marge_horizontal
	 */
	public $marge_horizontal = 10;
	/**
	 * marge_vertical
	 */
	public $marge_vertical = 10;
	/**
	 * Nouvelle page pour un TAB
	 */
	public $TabChangePage = false;
	/**
	 * Grille en mode étendu
	 */
	public $Extended = false;
	/**
	 * Constructeur PrintAction
	 *
	 * @param string $extension
	 * @param string $action
	 * @param array $params
	 * @return PrintAction
	 */
	public function __construct($extension,$action,$params) {
		$this->m_extension = $extension;
		$this->m_action = $action;
		$this->m_params = $params;
		$this->m_params['PRINT'] = 1;
	}
	
	private function getActionCustom() {
		global $login;
		require_once("CORE/Lucterios_Error.inc.php");
		$action = $this->m_action;
		$CURRENT_PATH = ".";
		if( strtoupper($this->m_extension) == "CORE")$EXT_FOLDER = "$CURRENT_PATH/CORE";
		else $EXT_FOLDER = "$CURRENT_PATH/extensions/".$this->m_extension;
		$ACTION_FILE_NAME = "$EXT_FOLDER/$action.act.php";
		if(! is_dir($EXT_FOLDER))
		throw new LucteriosException( CRITIC,"Extension '".$this->m_extension."' inconnue !");
		else if(! is_file($ACTION_FILE_NAME))
		throw new LucteriosException( CRITIC,"Action '$action' inconnue !");
		else if( checkRight($login,$this->m_extension,$action)) {
			require_once$ACTION_FILE_NAME;
			if(! function_exists($action))
			throw new LucteriosException( CRITIC,"Function inconnue !");
			else
			return $action($this->m_params);
		}
		else
		throw new LucteriosException( IMPORTANT,"Acces interdit !");
	}
	
	private function getPrepPage() {
		$content = '<page>';
		if($this->Title != "") {
			$content .= '<header extent="12.0" name="before">';
			$content .= sprintf('<text height="12.0" width="%d.0" top="0.0" left="0.0" padding="1.0" spacing="0.0" border_color="black" border_style="" border_width="0.2" xdisplay_align="center" text_align="center" line_height="20" font_family="sans-serif" font_weight="" font_size="20">',$this->largeur_page-2*$this->marge_horizontal);
			$content .= ModelConverter:: convertApasFormat("{[bold]}{[underline]}".$this->Title."{[/underline]}{[/bold]}");
			$content .= '</text>';
			$content .= '</header>';
		}
		else $content .= '<header extent="0.0" name="after"/>';
		$content .= '<bottom extent="0.0" name="after"/>';
		$content .= '<left extent="0.0" name="start"/>';
		$content .= '<rigth extent="0.0" name="end"/>';
		$content .= '<body extent="0.0" data="" name="body">';
		return $content;
	}
	
	public function changePage($checkTopValue = true) {
		if(!$checkTopValue || ($this->m_top>($this->hauteur_page-2*$this->marge_vertical))) {
			$this->m_content .= '</body></page>';
			$this->m_content .= $this->getPrepPage();
			$this->m_top = 0;
		}
	}
	
	public $m_last_top = 0;
	
	public $m_top = 0;
	
	public $m_colWidth = 0;
	
	public $m_content;
	
	private function getHeightOfRowspan($PrintItemList,$printItem) {
		$value = 0;
		$last_y = -1;
		$current_height = 0;
		foreach($PrintItemList as $item) {
			if(($item->tab == $printItem->tab) && ($item->rowspan == 1) && ($item->y >= $printItem->y) && ($item->y<($printItem->y+$printItem->rowspan))) {
				if($last_y == $item->y) {
					$current_height = max($current_height,$item->height);
				}
				else {
					$value += $current_height;
					$current_height = $item->height;
					$last_y = $item->y;
				}
			}
		}
		$value += $current_height;
		if($value<$printItem->height)$value = $printItem->height-$value;
		return $value;
	}
	
	public function generate() {
		$this->m_content = '<?xml version="1.0" encoding="ISO-8859-1"?>';
		$this->m_content .= sprintf('<model margin_right="%d.0" margin_left="%d.0" margin_bottom="%d.0" margin_top="%d.0" page_width="%d.0" page_height="%d.0">',$this->marge_horizontal,$this->marge_horizontal,$this->marge_vertical,$this->marge_vertical,$this->largeur_page,$this->hauteur_page);
		$this->m_content .= $this->getPrepPage();
		$xfer_custom = $this->getActionCustom();
		$this->m_colWidth = array();
		$ColSize = array();
		$PrintItemList = array();
		$max_col = 0;
		$components_list = $xfer_custom->getSortComponents();
		foreach($components_list as $components) {
			$new_item = null;
			if( strtolower( get_class($components)) == 'xfer_comp_image')$new_item = new PrintImage($components,$this);
			else if( strtolower( get_class($components)) == 'xfer_comp_grid')$new_item = new PrintTable($components,$this);
			else if( strtolower( get_class($components)) == 'xfer_comp_tab')$new_item = new PrintTab($components,$this);
			else if( in_array( strtolower( get_class($components)),array('xfer_comp_label','xfer_comp_linklabel','xfer_comp_labelform')))$new_item = new PrintLabel($components,$this);
			//'Xfer_Comp_Edit','Xfer_Comp_Date','Xfer_Comp_Time','Xfer_Comp_Memo','Xfer_Comp_MemoForm','Xfer_Comp_Float'
			if($new_item != null) {
				$new_sizeX = $new_item->sizeX/$new_item->colspan;
				for($i = 0;
				$i<($new_item->x+$new_item->colspan);
				$i++) {
					if(! array_key_exists($i,$ColSize))$ColSize[$i] = 0;
					if($i >= $new_item->x)$ColSize[$i] = max($ColSize[$i],$new_sizeX);
				}
				$max_col = max($max_col,$new_item->x+$new_item->colspan);
				$PrintItemList[] = $new_item;
			}
		}
		if($max_col == 0)
		throw new LucteriosException( IMPORTANT,"Pas de Colonne à imprimer");
		$total_size = 0;
		for($i = 0;
		$i<$max_col;
		$i++)$total_size = $total_size+$ColSize[$i];
		$largeur = $this->largeur_page-2*$this->marge_horizontal;
		for($i = 0;
		$i<$max_col;
		$i++)$this->m_colWidth[$i] = (int)($largeur*$ColSize[$i]/$total_size);
		$this->m_top = 0;
		$last_y = -1;
		$current_height = 0;
		$this->m_last_top = 0;
		foreach($PrintItemList as $item) {
			if($last_y == $item->y) {
				if($item->rowspan == 1)$current_height = max($current_height,$item->height);
				else $current_height = max($current_height,$this->getHeightOfRowspan($PrintItemList,$item));
			}
			else {
				$this->m_top = $this->m_top+$current_height;
				$this->changePage();
				if($item->rowspan == 1)$current_height = $item->height;
				else $current_height = $this->getHeightOfRowspan($PrintItemList,$item);
				$last_y = $item->y;
			}
			$item->calculPosition();
			$this->m_content .= $item->getReportPart();
			$this->m_last_top = $item->top+$item->height;
		}
		$this->m_content .= '</body></page></model>';
		$this->m_content = str_replace('>',">\n",$this->m_content);
		return $this->m_content;
	}
}

//@END@
?>
