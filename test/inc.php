<? $assert=array('list'=>null, 'true'=>0, 'false'=>0);

global $req_dir, $req_pwd;
function dovars(){
	global $req_dir, $req_pwd;
	$req_dir=
		"http://{$_SERVER['HTTP_HOST']}/".
		preg_replace('/[^\/]*$/','',$_SERVER['REQUEST_URI']);
	$req_pwd=preg_replace('/[^\/]*\/$/','',$req_dir);
}

function dosandbox(){
	global $_rSERVER, $_rCOOKIES, $_rPOST, $_rGET;
	$_rSERVER=$_SERVER;
	$_rCOOKIES=$_COOKIES;
	$_rPOST=$_POST;
	$_rGET=$_GET;
	$_SERVER=array();
	$_SERVER['PHP_SELF']='/PHP_SELF.php';
	$_SERVER['HTTP_HOST']='HTTP_HOST';
	$_SERVER['HTTP_USER_AGENT']='HTTP_USER_AGENT';
	$_SERVER['REQUEST_METHOD']='GET';
	$_COOKIE['user']='COOKIEuser';
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
	$expected=get_real_contents($name);
	$expected=str_replace($_rSERVER['HTTP_HOST'],'<?=$_GET[\'host\']?>',$expected);
	$fp=fopen("asserts/{$name}/expected.php",'w');
	fwrite($fp,$expected);
	fclose($fp);
}

function doassert($name, $dispcode=true){
	global $assert, $req_dir, $req_pwd;
	global $_rSERVER;
	$real=get_real_contents($name);
	$expected=file_get_contents("{$req_dir}asserts/{$name}/expected.php?host={$_rSERVER['HTTP_HOST']}");

	$fail=$real!=$expected;

	if($fail){
		$real=htmlentities($real);
		$expected=htmlentities($expected);
		$assert['list'].="\n<div class=\"false\">{$name}&nbsp;Failed</div>";
		if($dispcode){
			$assert['list'].=
				"\n<div class=\"real\">{$real}</div>".
				"\n<div class=\"expected\">{$expected}</div>";
				"\n<div class=\"clrb\"></div>";
		}
		$assert['false']++;
	}
	else{
		$assert['list'].="\n<div class=\"true\">{$name}&nbsp;Succeeded</div>";
		$assert['true']++;
	}
	$assert['total']++;
}

function dump_results(){
	global $assert;
	echo "\n<font class=\"false\">
		False:&nbsp;{$assert['false']}/{$assert['total']}
	</font>";
	echo "\n<b>&middot;</b>";
	echo "\n<font class=\"true\">
		True:&nbsp;{$assert['true']}/{$assert['total']}
	</font>";
	echo "\n<br /><br />";
	echo "\n<div class=\"h\">Details</div>";
	echo "\n<br />";
	echo $assert['list'];
}

dovars();

?>
