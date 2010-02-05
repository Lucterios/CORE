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
//  // Method file write by SDK tool
// --- Last modification: Date 04 February 2010 22:01:58 By  ---

require_once('CORE/xfer_exception.inc.php');
require_once('CORE/rights.inc.php');

//@TABLES@
require_once('CORE/etiquettes.tbl.php');
//@TABLES@

//@DESC@
//@PARAM@ premEtiquette
//@PARAM@ etiquetteValues

function etiquettes_APAS_getReport(&$self,$premEtiquette,$etiquetteValues)
{
//@CODE_ACTION@
if ($self->id>0) {

$etiquette_values=array();
for($idx=1;$idx<$premEtiquette;$idx++)
	$etiquette_values[]="";
foreach($etiquetteValues as $ettiquetteValue)
	$etiquette_values[]=$ettiquetteValue;

if (count($etiquette_values)==0)
	$etiquette_values[]="";
$marge_inf=0;
$marge_droite=0;
$content='<?xml version="1.0" encoding="ISO-8859-1"?>';
$content.=sprintf('<model margin_right="%d.0" margin_left="%d.0" margin_top="%d.0" margin_bottom="%d.0" page_width="%d.0" page_height="%d.0">', $self->marge_gauche,$marge_droite,$self->marge_sup,$marge_inf,$self->largeur_page,$self->hauteur_page);

$index=0;
foreach($etiquette_values as $ettiquetteValue)
{
	if (($index % ($self->colonnes*$self->lignes))==0)
	{
		if ($index!=0) $content.='</body></page>';
		$content.='<page>';
		$content.='<header extent="0.0" name="before"/>';
		$content.='<bottom extent="0.0" name="after"/>';
		$content.='<left extent="0.0" name="start"/>';
		$content.='<rigth extent="0.0" name="end"/>';
		$content.='<body extent="0.0" data="" name="body">';
		$index=0;
	}

	$col_num=($index % $self->colonnes);
	$row_num=(int)($index / $self->colonnes);
	$left=$col_num*$self->ecart_horizontal;
	$top=$row_num*$self->ecart_vertical;
	$content.=sprintf('<text height="%d.0" width="%d.0" top="%d.0" left="%d.0" padding="1.0" spacing="0.0" border_color="black" border_style="" border_width="0.2" xdisplay_align="center" text_align="center" line_height="10" font_family="sans-serif" font_weight="" font_size="9">',$self->hauteur,$self->largeur,$top,$left);
	$content.=ModelConverter::convertApasFormat("&#160;{[newline]}".$ettiquetteValue);
	$content.='</text>';
	$index++;
}
$content.='</body></page></model>';

$content=str_replace('>',">\n",$content);
return $content;
}
else
	throw new LucteriosException(GRAVE,"Pas d'étiquette séléctionnée!");
//@CODE_ACTION@
}

?>
