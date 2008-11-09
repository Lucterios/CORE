<?

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function otherCheckRight($login, $extension, $action) {
	return true;
}

class AssertException extends Exception {
}

class TestItem {
	var $classname;
	var $name;
	var $timeBegin;
	var $timeEnd;
	var $errorObj;
	var $list=array();

	function TestItem($classname,$name) {
		$this->classname=$classname;
		$this->name=$name;
		$this->timeBegin=microtime_float();
		$this->timeEnd=microtime_float();
		$this->errorObj=null;
	}

	function CallAction($extension,$action,$params,$class_name)
	{
		require_once("CORE/Lucterios_Error.inc.php");
		$xfer_result=null;
		if (strtoupper($extension)=="CORE")
			$EXT_FOLDER="CORE";
		else
			$EXT_FOLDER="extensions/$extension";
		$ACTION_FILE_NAME = "$EXT_FOLDER/$action.act.php";
		if (!is_dir($EXT_FOLDER))
			throw new LucteriosException(CRITIC,"Extension '$extension' inconnue !");
		else if (!is_file($ACTION_FILE_NAME))
			throw new LucteriosException(CRITIC,"Action '$action' inconnue !");
		else
		{
			require_once $ACTION_FILE_NAME;
			if (!function_exists($action))
				throw new LucteriosException(CRITIC,"Function '$action' inconnue !");
			else
				$xfer_result=call_user_func($action,$params);
		}
		$this->assertClass($class_name,$xfer_result,"Mauvaise classe retournée");
		return $xfer_result;
	}
    	function assertClass($expected, $actual, $message = '')
	{
		$this->assertEquals(strtolower($expected),strtolower(get_class($actual)),$message);
	}
    	function assertEquals($expected, $actual, $message = '')
	{
		if (strcmp(gettype($expected),gettype($actual))!=0)
			throw new AssertException($message.' Type différent - Attendu "'.gettype($expected).'" mais "'.gettype($actual).'" retourné');
		if (is_array($expected)) {
			$this->assertEquals(count($expected),count($actual),$message." tableaux de tailles différents");
			for($i=0;$i<count($expected);$i++)
				$this->assertEquals($expected[$i],$actual[$i],$message." |$i");
		}
		elseif (is_object($expected)) {
			$this->assertEquals(get_class($expected),get_class($actual),$message." classe différentes");
			$this->assertEquals(get_object_vars($expected),get_object_vars($actual),$message." objets différents");
		}
		elseif (strcmp("$expected","$actual")!=0)
			throw new AssertException($message.' Attendu "'.print_r($expected,true).'" mais "'.print_r($actual,true).'" retourné');
	}

	function runTest($extDir,$extName,$fileName) {
		try {
			$file_to_include="$extDir/$fileName.test.php";
			$function_name=$extName."_".$fileName;
			include_once $file_to_include;
			if (function_exists($function_name))
				$function_name($this);
			$this->success();
		} catch (Exception $e) {
			$this->error($e);
		}
	}
	function success() {
		$this->timeEnd=microtime_float();
	}
	function error($errorObj) {
		$this->success();
		$this->errorObj=$errorObj;
	}
	function toString() {
		$time=$this->timeEnd-$this->timeBegin;
		$string_text="\t<testcase classname='$this->classname' name='$this->name' time='$time'>\n";
		if (is_string($this->errorObj))
    			$string_text.="\t\t<error message='Error' type='Error'>".utf8_encode($this->errorObj)."</error>\n";
		elseif (is_subclass_of($this->errorObj, 'Exception')) {
			if (get_class($this->errorObj)=='AssertException') {
				$string_text.="\t\t<failure message='".utf8_encode($this->errorObj->getMessage())."' type='".get_class($this->errorObj)."'>\n";
				$string_text.="\t\t\t".utf8_encode($this->errorObj->getTraceAsString())."\n";
				$string_text.="\t\t</failure>\n";
			}
			else {
				$string_text.="\t\t<error message='".utf8_encode($this->errorObj->getMessage())."' type='".get_class($this->errorObj)."'>\n";
				$string_text.="\t\t\t".utf8_encode($this->errorObj->getTraceAsString())."\n";
				$string_text.="\t\t</error>\n";
			}
		}
  		$string_text.="\t</testcase>\n";
		return $string_text;
	}
	function addTests($item) {
		$this->list[]=$item;
	}
	function AllTests() {
		$this->success();
		$time=$this->timeEnd-$this->timeBegin;
		$nbTest=count($this->list);
		$nbError=0;
		foreach($this->list as $item)
			if ($item->errorObj!=null)
				$nbError+=1;
		$string_text="<testsuite errors='$nbError' name='$this->classname' tests='$nbTest' time='$time'>\n";
		foreach($this->list as $item)
			$string_text.=$item->toString();
  		$string_text.="</testsuite>\n";
		return $string_text;
	}
}

?>