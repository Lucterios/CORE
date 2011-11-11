<?php
// 	This file is part of Lucterios/Diacamma, a software developped by "Le Sanglier du Libre" (http://www.sd-libre.fr)
// 	Thanks to have payed a retribution for using this module.
// 
// 	Lucterios/Diacamma is free software; you can redistribute it and/or modify
// 	it under the terms of the GNU General Public License as published by
// 	the Free Software Foundation; either version 2 of the License, or
// 	(at your option) any later version.
// 
// 	Lucterios/Diacamma is distributed in the hope that it will be useful,
// 	but WITHOUT ANY WARRANTY; without even the implied warranty of
// 	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// 	GNU General Public License for more details.
// 
// 	You should have received a copy of the GNU General Public License
// 	along with Lucterios; if not, write to the Free Software
// 	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
// 
// 		Contributeurs: Fanny ALLEAUME, Pierre-Olivier VERSCHOORE, Laurent GAY// table file write by SDK tool
// --- Last modification: Date 26 October 2011 6:04:44 By  ---

require_once('CORE/DBObject.inc.php');

class DBObj_CORE_etiquettes extends DBObj_Basic
{
	public $Title="";
	public $tblname="etiquettes";
	public $extname="CORE";
	public $__table="CORE_etiquettes";

	public $DefaultFields=array(array('@refresh@'=>false, 'nom'=>'Planche 2x4', 'hauteur_page'=>'297', 'largeur_page'=>'210', 'hauteur'=>'70', 'largeur'=>'105', 'colonnes'=>'2', 'lignes'=>'4', 'marge_gauche'=>'0', 'marge_sup'=>'8', 'ecart_horizontal'=>'105', 'ecart_vertical'=>'70'), array('@refresh@'=>false, 'nom'=>'Planche 2x5', 'hauteur_page'=>'297', 'largeur_page'=>'210', 'hauteur'=>'57', 'largeur'=>'105', 'colonnes'=>'2', 'lignes'=>'5', 'marge_gauche'=>'0', 'marge_sup'=>'6', 'ecart_horizontal'=>'105', 'ecart_vertical'=>'57'), array('@refresh@'=>false, 'nom'=>'Planche 2x6', 'hauteur_page'=>'297', 'largeur_page'=>'210', 'hauteur'=>'49', 'largeur'=>'105', 'colonnes'=>'2', 'lignes'=>'6', 'marge_gauche'=>'0', 'marge_sup'=>'0', 'ecart_horizontal'=>'105', 'ecart_vertical'=>'49'), array('@refresh@'=>false, 'nom'=>'Planche 2x8', 'hauteur_page'=>'297', 'largeur_page'=>'210', 'hauteur'=>'35', 'largeur'=>'105', 'colonnes'=>'2', 'lignes'=>'8', 'marge_gauche'=>'0', 'marge_sup'=>'8', 'ecart_horizontal'=>'105', 'ecart_vertical'=>'35'), array('@refresh@'=>false, 'nom'=>'Planche 3x8', 'largeur_page'=>'210', 'hauteur_page'=>'297', 'largeur'=>'70', 'hauteur'=>'35', 'colonnes'=>'3', 'lignes'=>'8', 'marge_gauche'=>'0', 'marge_sup'=>'9', 'ecart_horizontal'=>'70', 'ecart_vertical'=>'35'), array('@refresh@'=>false, 'nom'=>'Planche 3x10', 'largeur_page'=>'210', 'hauteur_page'=>'297', 'largeur'=>'70', 'hauteur'=>'28', 'colonnes'=>'3', 'lignes'=>'10', 'marge_gauche'=>'0', 'marge_sup'=>'9', 'ecart_horizontal'=>'70', 'ecart_vertical'=>'28'));
	public $NbFieldsCheck=1;
	public $Heritage="";
	public $PosChild=-1;

	public $nom;
	public $largeur_page;
	public $hauteur_page;
	public $largeur;
	public $hauteur;
	public $colonnes;
	public $lignes;
	public $marge_gauche;
	public $marge_sup;
	public $ecart_horizontal;
	public $ecart_vertical;
	public $__DBMetaDataField=array('nom'=>array('description'=>'Nom', 'type'=>2, 'notnull'=>true, 'params'=>array('Size'=>80, 'Multi'=>false)), 'largeur_page'=>array('description'=>'Largeur page (mm)', 'type'=>0, 'notnull'=>false, 'params'=>array('Min'=>1, 'Max'=>9999)), 'hauteur_page'=>array('description'=>'Hauteur page (mm)', 'type'=>0, 'notnull'=>false, 'params'=>array('Min'=>1, 'Max'=>9999)), 'largeur'=>array('description'=>'Largeur (mm)', 'type'=>0, 'notnull'=>false, 'params'=>array('Min'=>1, 'Max'=>9999)), 'hauteur'=>array('description'=>'Hauteur (mm)', 'type'=>0, 'notnull'=>false, 'params'=>array('Min'=>1, 'Max'=>9999)), 'colonnes'=>array('description'=>'Colonnes', 'type'=>0, 'notnull'=>false, 'params'=>array('Min'=>1, 'Max'=>99)), 'lignes'=>array('description'=>'Lignes', 'type'=>0, 'notnull'=>false, 'params'=>array('Min'=>1, 'Max'=>99)), 'marge_gauche'=>array('description'=>'Marge gauche (mm)', 'type'=>0, 'notnull'=>false, 'params'=>array('Min'=>0, 'Max'=>9999)), 'marge_sup'=>array('description'=>'Marge supérieure (mm)', 'type'=>0, 'notnull'=>false, 'params'=>array('Min'=>0, 'Max'=>9999)), 'ecart_horizontal'=>array('description'=>'Ecart horizontal (mm)', 'type'=>0, 'notnull'=>false, 'params'=>array('Min'=>0, 'Max'=>9999)), 'ecart_vertical'=>array('description'=>'Ecart vertical (mm)', 'type'=>0, 'notnull'=>false, 'params'=>array('Min'=>0, 'Max'=>9999)));

	public $__toText='$nom';
}

?>
