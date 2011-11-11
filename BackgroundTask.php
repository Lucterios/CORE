<?

if (!isset($_SERVER['HTTP_HOST'])) {

    global $login;
    $login="admin";

    require_once("conf/cnf.inc.php");
    require_once("CORE/dbcnx.inc.php");
    require_once("CORE/log.inc.php");
    if (is_file("CORE/securityLock.inc.php")) {
	    global $GLOBAL;
	    $GLOBAL['ses']=posix_getpid().'@'.time();
	    require_once("CORE/securityLock.inc.php");
	    $SECURITY_LOCK=new SecurityLock();
	    if ($SECURITY_LOCK->isLock()==-1) {
		    echo "<!-- [".date('d/m/Y h:i:s')."] ----- V�ROUX S�CURIT� ACTIF ----- -->\n";
		    return;
	    }
    }
    $logContent="";
    require_once("CORE/xfer.inc.php");
    $logContent.="[".date('d/m/Y h:i:s')."] ------- BACKGROUND TASK -------\n";

    $obj=new Xfer_Object;
    $obj->signal("TIME_EVENT",$logContent);

    $logContent.="[".date('d/m/Y h:i:s')."] ----------------- FIN -----------------------\n";

    file_put_contents('conf/backgroundTask.log',$logContent);
}


?> 
	
