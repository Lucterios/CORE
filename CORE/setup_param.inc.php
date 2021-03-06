
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
// --- Last modification: Date 28 February 2008 23:05:45 By  ---

//@BEGIN@
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


class Param_Depencies
{
	public $name='';
	public $version_majeur_max=0;
	public $version_mineur_max=0;
	public $version_majeur_min=0;
	public $version_mineur_min=0;
	public $optionnal=false;
	public function __construct($name,$version_majeur_max,$version_mineur_max,$version_majeur_min=-1,$version_mineur_min=-1,$optionnal=false)
	{
		$this->name=$name;
		$this->version_majeur_max=$version_majeur_max;
		$this->version_mineur_max=$version_mineur_max;
		if ($version_majeur_min==-1)
			$this->version_majeur_min=$version_majeur_max;
		else
			$this->version_majeur_min=$version_majeur_min;
		if ($version_mineur_min==-1)
			$this->version_mineur_min=$version_mineur_max;
		else
			$this->version_mineur_min=$version_mineur_min;
		$this->optionnal=$optionnal;
	}
}

class Param_Menu
{
	public $description;
	public $help;
	public $act;
	public $pere;
	public $icon;
	public $shortcut;
	public $position;
	public $modal;
	public function __construct($description,$pere="",$act="",$icon="",$shortcut="",$position=0,$modal=0,$help='')
	{
		$this->description=$description;
		$this->act=$act;
		$this->pere=$pere;
		$this->icon=$icon;
		$this->shortcut=$shortcut;
		$this->position=$position;
		$this->modal=$modal;
		$this->help=$help
;
	}
}

class Param_Action
{
	public $action;
	public $rightNumber;
	public $description;

	public function __construct($description, $action, $rightNumbers) {
		$this->description = $description;
		$this->action = $action;
		$this->rightNumber = $rightNumbers;
	}
}

class Param_Rigth
{
	public $description;
	public $weigth;
	public function __construct($description,$weigth=50)
	{
		$this->description=$description;
		$this->weigth=$weigth;
	}
}

define('PARAM_TYPE_STR', 0);
define('PARAM_TYPE_INT', 1);
define('PARAM_TYPE_REAL', 2);
define('PARAM_TYPE_BOOL', 3);
define('PARAM_TYPE_ENUM', 4);

class Param_Parameters
{
	public $name;
	public $defaultvalue;
	public $description;
	public $type;
	public $extend;
	public function __construct($name,$defaultvalue,$description="",$type=PARAM_TYPE_STR,$extend=array())
	{
		$this->name=$name;
		$this->defaultvalue=$defaultvalue;

		$this->description=$description;
		$this->type=$type;
		$this->extend=$extend;
	}

        public static function ArrayToString($array)
        {
                $result="";
                if (!is_array($array))
                        $result.=$array;
                else
                foreach($array as $key=>$val)
                {
                        if ($result!="")
                                $result.=", ";
                        if (is_string($key))
                        {
                                $result.="'$key'=>";
                        }
                        if (is_string($val))
                        {
                                $val= str_replace("'","\'",$val);
                                $result.="'$val'";
                        }
                        elseif (is_array($val))
                        {
                                $result.=Param_Parameters::ArrayToString($val);
                        }
                        elseif (is_bool($val))
                        {
                                if ($val)
                                        $result.="true";
                                else
                                        $result.="false";
                        }
                        else
                                $result.=$val;
                }
                $result="array(".$result.")";
                return $result;
        }

	public function getExtendToText($Complete=true)
	{
		require_once 'debug_tools.php';
		$extent_text=Param_Parameters::ArrayToString($this->extend);
		if (!$Complete)
		{
			$extent_text=trim($extent_text);
			$extent_text=substr($extent_text,6);
			$extent_text=substr($extent_text,0,-1);
			$extent_text=trim($extent_text);
		}
		return $extent_text;
	}
}
//@END@
?>
