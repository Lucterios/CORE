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
if (is_dir("./cms")) {
	header('Location: ./cms');
	exit;
}
if (is_dir("./joomla")) {
	header('Location: ./joomla');
	exit;
}
if (is_dir("./web")) {
	header('Location: ./web');
	exit;
}


header('Content-Type: text/html; charset=ISO-8859-1');

$appli_dir="./applis";
if (!is_dir($appli_dir)) $appli_dir="./extensions/applis";

if (is_file($appli_dir."/application.inc.php")) {
	include $appli_dir."/application.inc.php";
	$copy_right=application_CopyRight();
}
else
	$copy_right='CopyRight Lucterios.org';

if (is_file($appli_dir."/setup.inc.php")) {
	include "./CORE/setup_param.inc.php";
	include $appli_dir."/setup.inc.php";
} else {
	$extention_description='Lucterios';
	$extention_appli='Lucterios';
}

function getSubFolder($dir)
{
	$list=array();
	$dh = opendir($dir);
	while (($file = readdir($dh)) != false)
	{
		if(($file[0]!='.') && is_dir($dir.$file) && is_file($dir.$file.'/index.php'))
			$list[]=array($file,$dir.$file);
	}
	return $list;
}
$list_sub_folder=array_merge(getSubFolder('./UpdateClients/'),getSubFolder('./'));

?>
<html>
<head>
  <title><?php echo $extention_description;?></title>
	<style type="text/css">
	<!--
		BODY {
		background-color: white;
		}
		
		h1 {
		font-family : Helvetica, serif;
		font-size : 10mm;
		font-weight : bold;
		text-align : center;
		vertical-align : middle;
		}
		
		h2 {
		font-size : 8mm;
		font-style : italic;
		font-weight : lighter;
		text-align : center;
		}
		
		h3 {
		font-size : 6mm;
		font-style : italic;
		text-decoration : underline;
		text-align : center;
		}
		
		img {
		border-style: none;
		}
		
		TABLE.main {
		width: 100%;
		height: 95%;
		background-color: rgb(18, 0, 130);
		}
		
		/* banniere */
		
		TR.banniere {
		}
		
		TD.banniere {
		width: 980px;
		height: 70px;
		border-style: solid;
		border-color: orange;
		border-width: 1px;
		}
		
		h1.banniere {
		color: orange;
		}

		TD.menu {
		width: 120px;
		vertical-align: top;
		color: white;
		}	
			
		/* corps */
		TR.corps {
		width: 980px;
		}
		
		TD.corps {
		width: 860px;
		vertical-align: top;
		background-color: white;
		}
		
		/* pied */
		TR.pied {
		width: 980px;
		height: 15px;
		background-color: rgb(100, 100, 200);
		}
		
		TD.pied {
		width: 980px;
		height: 15px;
		color: rgb(230, 230, 230);
		font-size: 9px;
		text-align: right;
		font-weight: bold;
		}	
		
		.go {
		font-family : verdana,helvetica;
		font-size : 10pt;
		font-style : oblique;
		text-decoration : none;
		text-indent : 2cm;
		}
		
		TD {
		font-size: 10pt;
		font-family: verdana,helvetica;
		}
		
		a.invisible {
		color: rgb(18, 0, 130);
		font-size: 0px;
		}
		
		li {
		}
		
		ul {
		font-style : italic;
		}
	-->
	</style>
</head>
<body>
<table class="main">
    <tr class="banniere">
        <td colspan="2" class="banniere">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td>
                        <img src='<?php echo $appli_dir."/images/logo.gif";?>' alt='logo' />
                    </td>
                    <td align="center">
                        <h1 class="banniere"><?php echo $extention_description;?></h1>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr class="corps">	
        <td class="menu">
        </td>
        <td class="corps">
		<br>
		<h3>Clients de connnexion</h3>
		<center>
		<h4>Séléctionnez le client que vous voulez utiliser.</h4>
		<br>
		<table>
		<?php
			foreach($list_sub_folder as $sub_folder) {
				$name=$sub_folder[0];
				$index=$sub_folder[1].'/index.php';
				if (is_file($sub_folder[1].'/version.txt'))
					$version=str_replace(' ','.',trim(file_get_contents($sub_folder[1].'/version.txt')));
				else
					$version='?.?.?.?';
				echo "<tr><td width='50%'><a href='$index'>$name</a></td><td>Version $version</td></tr>";
			}	
		?>
		</table>
		</center>
        </td>
    </tr>
    <tr class="pied">
        <td colspan="2" class="pied">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td class="pied">
                        <?php echo $copy_right;?> - Mise à jour <?php echo date ("d/m/Y", filemtime("index.php")); ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
 
