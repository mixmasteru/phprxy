<? $assert=array('list'=>null, 'true'=>0, 'false'=>0);

global $req_dir, $req_pwd;
function dovars(){
	global $req_dir, $req_pwd;
	$req_dir=
		"http://{$_SERVER['HTTP_HOST']}/".
		preg_replace('/[^\/]*$/','',$_SERVER['REQUEST_URI']);
	$req_pwd=preg_replace('/[^\/]*\/$/','',$req_dir);
}

function dosandbox($_SANDBOX=array()){
	global
		$_rSERVER, $_rCOOKIE, $_rGET, $_rPOST, $_rFILES, $_rENV, $_rREQUEST,
		$_rSESSION,
		$_SERVER, $_COOKIE, $_GET, $_POST, $_FILES, $_ENV, $_REQUEST,
		$_SESSION;

	$_rSERVER=$_SERVER;
	$_rCOOKIE=$_COOKIE;
	$_rGET=$_GET;
	$_rPOST=$_POST;
	$_rFILES=$_FILES;
	$_rENV=$_ENV;
	$_rREQUEST=$_REQUEST;
	$_rSESSION=$_REQUEST;

	$_SERVER=array();
	$_COOKIE=array();
	$_GET=array();
	$_POST=array();
	$_rFILES=array();
	$_rENV=array();
	$_rREQUEST=array();
	$_rSESSION=array();

	$_SERVER['REQUEST_METHOD']='GET';
	$_SERVER['HTTP_HOST']='HTTP_HOST';
	$_SERVER['PHP_SELF']='/PHP_SELF.php';
	$_SERVER['HTTP_REFERER']='/';
	$_SERVER['HTTP_USER_AGENT']='HTTP_USER_AGENT';
	$_COOKIE['user']='COOKIEuser';

	if(!is_array($_SERVER) && count($_SANDBOX['_SERVER']>0))
		$_SERVER=array();
	if(!is_array($_COOKIE) && count($_SANDBOX['_COOKIE']>0))
		$_COOKIE=array();
	if(!is_array($_GET) && count($_SANDBOX['_GET']>0))
		$_GET=array();
	if(!is_array($_POST) && count($_SANDBOX['_POST']>0))
		$_POST=array();
	if(!is_array($_FILES) && count($_SANDBOX['_FILES']>0))
		$_FILES=array();
	if(!is_array($_ENV) && count($_SANDBOX['_ENV']>0))
		$_ENV=array();
	if(!is_array($_REQUEST) && count($_SANDBOX['_REQUEST']>0))
		$_REQUEST=array();
	if(!is_array($_SESSION) && count($_SANDBOX['_SESSION']>0))
		$_SESSION=array();

	$_SERVER=array_merge($_SERVER,$_SANDBOX['_SERVER']);
	$_COOKIE=array_merge($_COOKIE,$_SANDBOX['_COOKIE']);
	$_GET=array_merge($_GET,$_SANDBOX['_GET']);
	$_POST=array_merge($_POST,$_SANDBOX['_POST']);
	$_FILES=array_merge($_POST,$_SANDBOX['_FILES']);
	$_ENV=array_merge($_POST,$_SANDBOX['_ENV']);
	$_REQUEST=array_merge($_POST,$_SANDBOX['_REQUEST']);
	$_SESSION=array_merge($_POST,$_SANDBOX['_SESSION']);
}

function createsandbox($name){
	global
		$_SERVER, $_COOKIE, $_GET, $_POST, $_FILES, $_ENV, $_REQUEST,
		$_SESSION;
	$_SANDBOX=array();
	$_SANDBOX['_SERVER']=array();
	$_SANDBOX['_COOKIE']=array('PHPSESSID'=>'PHPSESSID');
	$_SANDBOX['_GET']=array();
	$_SANDBOX['_POST']=array();
	$_SANDBOX['_FILES']=array();
	$_SANDBOX['_ENV']=array();
	$_SANDBOX['_REQUEST']=array();
	$_SANDBOX['_SESSION']=array('sesspref'=>'sesspref');

	$sandboxfile="asserts/{$name}/sandbox.php";
	if(file_exists($sandboxfile))
		require($sandboxfile);

	# if magic quotes enabled, do the work of magic quotes for it
	if(
		ini_get('magic_quotes_sybase')==1 ||
		(ini_get('magic_quotes_sybase')=='' && get_magic_quotes_gpc())
	){
		function addslashes_recurse($var){
			if(is_array($var)) $var=array_map('addslashes_recurse',$var);
			else{
				if(ini_get('magic_quotes_sybase')==1 && get_magic_quotes_gpc())
					$var=str_replace('\'','\\\'',$var);
				else
					$var=addslashes($var);
			}
			return $var;
		}
		$_SANDBOX['_GET']=addslashes_recurse($_SANDBOX['_GET']);
		$_SANDBOX['_POST']=addslashes_recurse($_SANDBOX['_POST']);
		$_SANDBOX['_COOKIE']=addslashes_recurse($_SANDBOX['_COOKIE']);
	}
	dosandbox($_SANDBOX);
}

function get_real_contents($name){
	global $req_dir, $req_pwd;
	$_SERVER['QUERY_STRING']="={$req_dir}asserts/{$name}/index.php";
	ob_start();
	include("../index.php");
	$dump=ob_get_contents();
	ob_end_clean();
	return $dump;
}

function create_expected($name){
	global $_rSERVER;
	createsandbox($name);
	$expected=get_real_contents($name);
	$expected=
		str_replace($_rSERVER['HTTP_HOST'],'<?=$_GET[\'host\']?>',$expected);
	$fp=fopen("asserts/{$name}/expected.php",'w');
	fwrite($fp,$expected);
	fclose($fp);
}

function getjs($succ){
	return
		"<script language=\"javascript\">\n".
		"<!--\n".
		'	parent.endassert('.($succ?'true':'false').');'.
		'//-->'.
		'</script>';
}

function gethtml($name, $succ, $real, $expected){
	$out=
		'<font class="'.($succ?'true':'false').'">'.
		"	{$name}:&nbsp;".($succ?'Success':'Failure').
		'</font>';
	if(!$succ){
		$real=htmlentities($real);
		$expected=htmlentities($expected);
		$out.=
			"<div class=\"real\">{$real}</div>".
			"<div class=\"expected\">{$expected}</div>".
			'<div class="clrb"></div>';
	}
	return $out;
}

function doassert($name, $dispcode=true){
	global $out, $req_dir, $req_pwd;
	global $_rSERVER;

	createsandbox($name);
	$real=get_real_contents($name);
	$expected=file_get_contents(
		"{$req_dir}asserts/{$name}/expected.php?host={$_rSERVER['HTTP_HOST']}");

	$succ=$real==$expected;

	$out=getjs($succ);
	$out.=gethtml($name,$succ,$real,$expected);
}

function dump_result(){
	global $out;
	echo $out;
}

dovars();

?>
