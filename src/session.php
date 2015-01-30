<?php
/**
 * phprxy
 *
 * @author Ulrich Pech
 * @link https://github.com/mixmasteru
 *
 * @copyright Surrogafier, Author: Brad Cable, Email: brad@bcable.net
 * @license BSD
 */
# COOKIE & SESSION SETUP {{{

//$totstarttime=microtime(true); # BENCHMARK

# set time and memory limits to their defined values, if not in safe mode
if(!ini_get('safe_mode')) set_time_limit($CONFIG['TIME_LIMIT']);
if(!ini_get('safe_mode')) ini_set('memory_limit', $CONFIG['MEMORY_LIMIT']);

# use gzip compression if available and enabled
if($CONFIG['GZIP_PROXY_USER'] && extension_loaded('zlib') &&
!ini_get('zlib.output_compression')
) ob_start('ob_gzhandler');

# reverse magic quotes if enabled
if(
ini_get('magic_quotes_sybase')==1 ||
(ini_get('magic_quotes_sybase')=='' && get_magic_quotes_gpc())
){
	function stripslashes_recurse($var){
		if(is_array($var)) $var=array_map('stripslashes_recurse',$var);
		else{
			if(ini_get('magic_quotes_sybase')==1 && get_magic_quotes_gpc())
				$var=str_replace('\\\'','\'',$var);
			else
				$var=stripslashes($var);
		}
		return $var;
	}
	$_GET=stripslashes_recurse($_GET);
	$_POST=stripslashes_recurse($_POST);
	$_COOKIE=stripslashes_recurse($_COOKIE);
}

# script environment constants
if($CONFIG['PROTO']===false)
	$CONFIG['PROTO']=($_SERVER['HTTPS']=='on'?'https':'http');
	define('VERSION','0.1');
	define('THIS_SCRIPT',
	$CONFIG['PROTO']."://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}");

	# randomized cookie prefixes
	function gen_randstr($len){
		$chars=null;
		for($i=0;$i<$len;$i++){
			$char=rand(0,25);
			$char=chr($char+97);
			$chars.=$char;
		}
		return $chars;
	}

	function dosetcookie($cookname,$cookval,$expire=null){
		$_COOKIE[$cookname]=$cookval;
		if($expire===null) setcookie($cookname,$cookval);
		else setcookie($cookname,$cookval,$expire);
	}

	if(!isset($_SESSION)) session_start();

	if(empty($_SESSION['sesspref'])){
		$sesspref=gen_randstr(30);
		$_SESSION['sesspref']=$sesspref;
	}
	else $sesspref=$_SESSION['sesspref'];

	if(empty($_COOKIE['user'])){
		$cookpref=gen_randstr(12);
		dosetcookie('user',$cookpref);
	}
	else $cookpref=$_COOKIE['user'];

	define('SESS_PREF',$sesspref);
	define('COOK_PREF',$cookpref);
	define('COOKIE_SEPARATOR','__'.COOK_PREF.'__');
	unset($sesspref,$cookpref);

	# ssl domains array handling
	if(!empty($_GET[COOK_PREF.'_ssl_domain'])){
		if(!is_array($_SESSION['ssl_domains'])) $_SESSION['ssl_domains']=array();
		$_SESSION['ssl_domains'][]=$_GET[COOK_PREF.'_ssl_domain'];
		exit();
	}

	# }}}