<?php

#
# Surrogafier v1.1-devel
#
# Author: Brad Cable
# Email: brad@bcable.net
# License: Modified BSD
# License Details:
# http://bcable.net/license.php
#


# CONFIG {{{

global $CONFIG;
$CONFIG=array();

# Default to simple mode when the page is loaded. [false]
$CONFIG['DEFAULT_SIMPLE']=false;
# Force the page to always be in simple mode (no advanced mode option). [false]
$CONFIG['FORCE_SIMPLE']=false;
# Width for the URL box when in simple mode (CSS "width" attribute). [300px]
$CONFIG['SIMPLE_MODE_URLWIDTH']='300px';
# Disables POST and COOKIES for a much leaner script, at the expense of
# functionality. [false]
$CONFIG['DISABLE_POST_COOKIES']=false;

# Default value for tunnel server. []
$CONFIG['DEFAULT_TUNNEL_IP']='';
# Default value for tunnel port. []
$CONFIG['DEFAULT_TUNNEL_PORT']='';
# Force the default values of the tunnel fields, and disallow user input.
# [false]
$CONFIG['FORCE_DEFAULT_TUNNEL']=false;
# Default value for User-Agent. []
$CONFIG['DEFAULT_USER_AGENT']='';
# Force the default value of the user agent field, and disallow user input.
# [false]
$CONFIG['FORCE_DEFAULT_USER_AGENT']=false;

# Default value for "Persistent URL" checkbox. [true]
$CONFIG['DEFAULT_URL_FORM']=true;
# Force the default value of the "Persistent URL" field, and disallow user
# input. [false]
$CONFIG['FORCE_DEFAULT_URL_FORM']=false;
# Default value for "Remove Cookies" checkbox. [false]
$CONFIG['DEFAULT_REMOVE_COOKIES']=false;
# Force the default value of the "Remove Cookies" field, and disallow user
# input. [false]
$CONFIG['FORCE_DEFAULT_REMOVE_COOKIES']=false;
# Default value for "Remove Referer Field" checkbox. [false]
$CONFIG['DEFAULT_REMOVE_REFERER']=false;
# Force the default value of the "Remove Referer Field" field, and disallow user
# input. [false]
$CONFIG['FORCE_DEFAULT_REMOVE_REFERER']=false;
# Default value for "Remove Scripts" checkbox. [false]
$CONFIG['DEFAULT_REMOVE_SCRIPTS']=false;
# Force the default value of the "Remove Scripts" field, and disallow user
# input. [false]
$CONFIG['FORCE_DEFAULT_REMOVE_SCRIPTS']=false;
# Default value for "Remove Objects" checkbox. [false]
$CONFIG['DEFAULT_REMOVE_OBJECTS']=false;
# Force the default value of the "Remove Objects" field, and disallow user
# input. [false]
$CONFIG['FORCE_DEFAULT_REMOVE_OBJECTS']=false;
# Default value for "Encrypt URLs" checkbox. [false]
$CONFIG['DEFAULT_ENCRYPT_URLS']=false;
# Force the default value of the "Encrypt URLs" field, and disallow user input.
# [false]
$CONFIG['FORCE_DEFAULT_ENCRYPT_URLS']=false;
# Default value for "Encrypt Cookies" checkbox. [false]
$CONFIG['DEFAULT_ENCRYPT_COOKIES']=false;
# Force the default value of the "Encrypt Cookies" field, and disallow user
# input. [false]
$CONFIG['FORCE_DEFAULT_ENCRYPT_COOKIES']=false;

/*/ Address Blocking Notes \*\

Formats for address blocking are as follows:

  1.2.3.4     - plain IP address
  1.0.0.0/16  - subnet blocking
  1.0/16      - subnet blocking
  1/8         - subnet blocking
  php.net     - domain blocking

Default Value: '10/8','172/8','192.168/16','127/8','169.254/16'

\*\ End Address Blocking Notes /*/

$CONFIG['BLOCKED_ADDRESSES']=
	array('10/8','172/8','192.168/16','127/8','169.254/16');

# }}}

# ADVANCED CONFIG {{{

# The following options alter the way documents are parsed on the page, and how
# the internals of th escript actually function.
# ONLY EDIT THIS STUFF IF YOU REALLY KNOW WHAT YOU ARE DOING!

# 500 is the most reasonable number I could come up with as a maximum URL length
# limit.  I ran into a 1200+ character long URL once and it nearly melted the
# processor on my laptop trying to parse it.  Honestly, who needs this long of a
# URL anyway? [500]
$CONFIG['MAXIMUM_URL_LENGTH']=500;

# Time limit in seconds for a single request and parse. [10]
$CONFIG['TIME_LIMIT']=10;
# Time limit in minutes for a DNS entry to be kept in the cache. [10]
$CONFIG['DNS_CACHE_EXPIRE']=10;

# Use gzip (if possible) to compress the connection between the proxy and the
# user (less bandwidth, more CPU). [false]
$CONFIG['GZIP_PROXY_USER']=false;
# Use gzip (if possible) to compress the connection between the proxy and the
# server (less bandwidth, more CPU). [false]
$CONFIG['GZIP_PROXY_SERVER']=false;

# Protocol that proxy is running on.  Change this to a value other than false
# to manually define it.  If you leave this value as false, the code detects
# if you are running on an HTTPS connection.  If you are, then 'https' is used
# as the PROTO value, otherwise 'http' is used.
$CONFIG['PROTO']=false;

# }}}

# LABEL {{{

global $LABEL;
$LABEL=array();

# TITLE: title text above form
$LABEL['TITLE']='Surrogafier';
# URL: text for URL text field
$LABEL['URL']='URL:';
# TUNNEL: text for tunnel proxy text fields
$LABEL['TUNNEL']='Tunnel Proxy:';
# USER_AGENT: text for user-agent select field
$LABEL['USER_AGENT']='User-Agent:';
# USER_AGENT_CUSTOM: text for user-agent custom text field
$LABEL['USER_AGENT_CUSTOM']='';
# URL_FORM: text for persistent URL form checkbox
$LABEL['URL_FORM']='Persistent URL Form';
# REMOVE_COOKIES: text for remove cookies checkbox
$LABEL['REMOVE_COOKIES']='Remove Cookies';
# REMOVE_REFERER: text for remove referer checkbox
$LABEL['REMOVE_REFERER']='Remove Referer Field';
# REMOVE_SCRIPTS: text for remove scripts checkbox
$LABEL['REMOVE_SCRIPTS']='Remove Scripts (JS, VBS, etc)';
# REMOVE_OBJECTS: text for remove objects checkbox
$LABEL['REMOVE_OBJECTS']='Remove Objects (Flash, Java, etc)';
# ENCRYPT_URLS: text for encrypt URLs checkbox
$LABEL['ENCRYPT_URLS']='Encrypt URLs';
# ENCRYPT_COOKIES: text for encrypt cookies checkbox
$LABEL['ENCRYPT_COOKIES']='Encrypt Cookies';
# SUBMIT_MAIN: text for the main submit button
$LABEL['SUBMIT_MAIN']='Surrogafy';
# SUBMIT_SIMPLE: text for the simple submit button
$LABEL['SUBMIT_SIMPLE']='Surrogafy';

# }}}

# STYLE {{{

global $STYLE;
$STYLE=array();

# body of whole document
$STYLE['body']='
	font-family: bitstream vera sans, arial;
	margin: 0px;
	padding: 0px;
';

# <form>
$STYLE['form#proxy_form']='
	margin: 0px;
	padding: 0px;
';

# <table>
$STYLE['table#proxy_table']='
	margin: 0px;
	padding: 0px;
	margin-left: auto;
	margin-right: auto;
';

# the title text above form
$STYLE['td#proxy_title']='
	font-weight: bold;
	font-size: 1.4em;
	text-align: center;
';

# class for all text fields
$STYLE['input.proxy_text']='
	width: 100%;
	border: 1px solid #000000;
';

# class for all select fields
$STYLE['select.proxy_select']='
	width: 100%;
	border: 1px solid #000000;
';

# class for all proxy defined links
$STYLE['a.proxy_link']='
	color: #000000;
';

# class for all submit buttons
$STYLE['input.proxy_submit']='
	border: 1px solid #000000;
	background-color: #FFFFFF;
';

# the simple submit button
$STYLE['input#proxy_submit_simple']='';

# the main submit button
$STYLE['input#proxy_submit_main']='
	width: 100%;
';

# the tunnel proxy ip field
$STYLE['input#proxy_tunnel_ip']='
	float: left;
	width: 73%;
';

# the tunnel proxy port field
$STYLE['input#proxy_tunnel_port']='
	float: right;
	width: 23%;
';

# the link for script information and a link to the author
$STYLE['a#proxy_link_author']='
	float: left;
';

# the link for toggling modes
$STYLE['a#proxy_link_mode']='
	float: right;
';

# }}}

# STYLE_URL_FORM {{{

global $STYLE_URL_FORM;
$STYLE_URL_FORM=array();

# }}}



// DON'T EDIT ANYTHING AFTER THIS POINT \\


#
# (unless you absolutely know what you are doing...)
#


# USER CONFIG {{{

define('THIS_FILE',"{$_SERVER['DOCUMENT_ROOT']}{$_SERVER['PHP_SELF']}");
$file_ext_pos=strrpos(THIS_FILE,'.');
define('CONFIG_FILE',
	substr(THIS_FILE,0,$file_ext_pos).
	'.conf'.
	substr(THIS_FILE,$file_ext_pos)
);
if(file_exists(CONFIG_FILE))
	include(CONFIG_FILE);

# }}}

# COOKIE & SESSION SETUP {{{

//$totstarttime=microtime(true); # BENCHMARK
$CONFIG['BLOCKED_ADDRESSES']=array(); # DEBUG
#$CONFIG['BLOCKED_ADDRESSES']=array('127.0.0.1','localhost'); # PRODUCTION

# set error level to not display notices
error_reporting(E_ALL^E_NOTICE);

# set time limit to the defined time limit, if not in safe mode
if(!ini_get('safe_mode')) set_time_limit($CONFIG['TIME_LIMIT']);

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
define('VERSION','1.1-devel');
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

# ENVIRONMENT SETUP {{{

global $postandget,$dns_cache_array;
$postandget=array_merge($_GET,$_POST);

define('PAGETYPE_MINIREGEXP','(=[_\.\-]?\&=|=)?');
define('PAGETYPE_REGEXP','/^'.PAGETYPE_MINIREGEXP.'(.*)$/');
if(!empty($postandget[COOK_PREF])) $oenc_url=$postandget[COOK_PREF];
else{
	$pagetype_str=preg_replace(PAGETYPE_REGEXP,'\1',$_SERVER['QUERY_STRING']);
	define('QUERY_STRING',
		substr($_SERVER['QUERY_STRING'],
		strlen($pagetype_str),
		strlen($_SERVER['QUERY_STRING'])-strlen($pagetype_str)));
	define('PAGETYPE_NULL',0);
	define('PAGETYPE_FORCE_MAIN',1);
	define('PAGETYPE_FRAME_TOP',2);
	define('PAGETYPE_FRAMED_PAGE',3);
	# framing children for crimes isn't very nice, but the script does it anyway
	define('PAGETYPE_FRAMED_CHILD',4);
	switch($pagetype_str){
		case '=&=': define('PAGETYPE_ID',PAGETYPE_FRAME_TOP); break;
		case '=_&=': define('PAGETYPE_ID',PAGETYPE_FRAMED_PAGE); break;
		case '=-&=': define('PAGETYPE_ID',PAGETYPE_FORCE_MAIN); break;
		case '=.&=': define('PAGETYPE_ID',PAGETYPE_FRAMED_CHILD); break;
# this is one more unencoded string for future features
#		case '=*&=': define('PAGETYPE_ID',); break;
		default: define('PAGETYPE_ID',PAGETYPE_NULL); break;
	}
	unset($pagetype_str);

	define('NEW_PAGETYPE_FRAME_TOP',(
		PAGETYPE_ID===PAGETYPE_FRAMED_CHILD?
		PAGETYPE_FRAMED_CHILD:PAGETYPE_FRAME_TOP
	));
	define('NEW_PAGETYPE_FRAMED_PAGE',(
		PAGETYPE_ID===PAGETYPE_FRAMED_CHILD?
		PAGETYPE_FRAMED_CHILD:PAGETYPE_FRAMED_PAGE
	));

	$oenc_url=QUERY_STRING;
}

if(
	strpos(substr($oenc_url,0,6),'%')!==false ||
	strpos($oenc_url,'%')<strpos($oenc_url,'/') ||
	strpos($oenc_url,'%')<strpos($oenc_url,':')
) $oenc_url=urldecode($oenc_url);

define('OENC_URL',preg_replace('/^([^\?\&]+)\&/i','\1?',$oenc_url));
unset($oenc_url);
define('ORIG_URL',proxdec(OENC_URL));
global $curr_url;
$curr_url=ORIG_URL;

define('PAGE_FRAMED',
	PAGETYPE_ID===PAGETYPE_FRAMED_PAGE ||
	PAGETYPE_ID===PAGETYPE_FRAMED_CHILD ||
	QUERY_STRING=='js_regexps_framed' ||
	QUERY_STRING=='js_funcs_framed'
);

# ENVIRONMENT SETUP: OPTIONS {{{

global $OPTIONS;
$OPTIONS=array();

define('IS_FORM_INPUT',!empty($postandget[COOK_PREF.'_set_values']));

# registers an option with the OPTIONS array
function register_option(
	$config_type,
	$config_name,
	$cookie_name=null
){
	if($cookie_name==null)
		$cookie_name=strtolower($config_name);

	global $CONFIG,$OPTIONS,$postandget;

	# get user input
	$user_input=(
		IS_FORM_INPUT?
		$postandget[COOK_PREF."_{$cookie_name}"]:
		$_COOKIE[COOK_PREF."_{$cookie_name}"]
	);

	# option parsers
	switch($config_type){
		# integer option
		case 2:
			$user_input=intval($user_input);
			break;

		# true/false option
		case 1:
			$user_input=(
				IS_FORM_INPUT?
				!empty($user_input):
				$user_input=='true'
			);
			break;

		# standard option
		case 0:
		default:
			break;
	}

	# set option value
	$OPTIONS[$config_name]=(
		$CONFIG["FORCE_DEFAULT_{$config_name}"] || (
			!IS_FORM_INPUT && !isset($_COOKIE[COOK_PREF."_{$cookie_name}"])
		)?
		$CONFIG["DEFAULT_{$config_name}"]:
		$user_input
	);

	# set cookies
	if(IS_FORM_INPUT){
		dosetcookie(COOK_PREF."_{$cookie_name}",false,0);

		if($OPTIONS[$config_name]!=$CONFIG["DEFAULT_{$config_name}"]){
			if($config_type==1)
				dosetcookie(
					COOK_PREF."_{$cookie_name}",
					($OPTIONS[$config_name]?'true':'false')
				);
			else
				dosetcookie(COOK_PREF."_{$cookie_name}",$OPTIONS[$config_name]);
		}
	}
}

# register standard options
register_option(0,'TUNNEL_IP');
register_option(1,'URL_FORM');
register_option(1,'REMOVE_COOKIES');
register_option(1,'REMOVE_REFERER');
register_option(1,'REMOVE_SCRIPTS');
register_option(1,'REMOVE_OBJECTS');
register_option(1,'ENCRYPT_URLS');
register_option(1,'ENCRYPT_COOKIES');

# register custom defined options
$OPTIONS['USER_AGENT']=(
	$CONFIG['FORCE_DEFAULT_USER_AGENT'] || empty($_COOKIE['_useragent'])?
	$CONFIG['DEFAULT_USER_AGENT']:(
		$_COOKIE[COOK_PREF.'_useragent']=='1'?
		$_COOKIE[COOK_PREF.'_useragent_custom']:
		$_COOKIE[COOK_PREF.'_useragent']
	)
);

register_option(2,'TUNNEL_PORT');
if($OPTIONS['TUNNEL_PORT']<1 || $OPTIONS['TUNNEL_PORT']>65535)
	$OPTIONS['TUNNEL_PORT']=null;

$OPTIONS['SIMPLE_MODE']=$CONFIG['DEFAULT_SIMPLE'] || $CONFIG['FORCE_SIMPLE'];

# }}}

# }}}

# PHP DECODING FUNCTIONS {{{

function my_base64_decode($string){
	return base64_decode(str_replace(' ','+',urldecode($string)));
}

function proxdec($url){
	if($url{0}!='~' && strtolower(substr($url,0,3))!='%7e') return $url;
	#while(strpos($url,'%')!==false) $url=urldecode($url);
	#$url=urldecode($url);
	while($url{0}=='~' || strtolower(substr($url,0,3))=='%7e'){
		$url=substr($url,1);
		$url=my_base64_decode($url);
		$new_url=null;
		for($i=0;$i<strlen($url);$i++){
			$char=ord($url{$i});
			$char-=ord(substr(SESS_PREF,$i%strlen(SESS_PREF),1));
			while($char<32) $char+=94;
			$new_url.=chr($char);
		}
		$url=$new_url;
	}
	return urldecode($url);
}

# }}}

# FIRST PAGE DISPLAYED WHEN ACCESSING PROXY {{{

if(
	PAGETYPE_ID===PAGETYPE_FORCE_MAIN ||
	(substr(QUERY_STRING,0,3)!='js_' && ORIG_URL==null)
){

$useragent_platforms=array(
	array('Windows', 'windows', 'win32'),
	array('Linux', 'linux'),
	array('Macintosh', 'macintosh', 'mac_powerpc'),
	array('BSD', 'bsd')
);

$useragent_browsers=array(
	'firefox' => 'Firefox',
	'iceweasel' => 'Iceweasel',
	'konqueror' => 'Konqueror',
	'msie' => 'Internet Explorer',
	'netscape' => 'Netscape',
	'opera' => 'Opera',
	'safari' => 'Safari',
	'seamonkey' => 'SeaMonkey'
);

$useragentinfo=null;

# parse platform
$dobreak=false;
foreach($useragent_platforms as $platform){
	for($i=1; $i<count($platform); $i++){
		if(stristr($_SERVER['HTTP_USER_AGENT'], $platform[$i])!==false){
			$useragentinfo.=$platform[0];
			$dobreak=true;
			break;
		}
	}

	if($dobreak)
		break;
}

if(!$dobreak)
	$useragentinfo.='Unknown';

# separator
$useragentinfo.=' / ';

# parse browser
$found=false;
foreach($useragent_browsers as $substr=>$browser){
	if(stristr($_SERVER['HTTP_USER_AGENT'],$browser)!==false){
		$useragentinfo.=$browser;
		$found=true;
		break;
	}
}
if(!$found)
	$useragentinfo.='Unknown';

# construct useragent options
$ver=array(
	'dillo' => '0.8.6',
	'firefox' => '2.0',
	'gecko' => '20061024',
	'konq' => '3.5',
	'konq_minor' => '3.5.5',
	'links' => '2.1pre19',
	'lynx' => '2.8.5rel.1',
	'moz_rev' => '1.8.1',
	'msie6' => '6.0',
	'msie7' => '7.0',
	'opera' => '9.02',
	'safari' => '3.0',
	'webkit' => '521.25',
	'wget' => '1.10.2',
	'windows' => 'NT 5.1'
);

$useragent_array=array(
	array(null,"Actual ({$useragentinfo})"),
	array('-1',' [ Don\'t Send ] '),
	array("Mozilla/5.0 (Windows; U; Windows {$ver['windows']}; en-US; ".
	      "rv:{$ver['moz_rev']}) Gecko/{$ver['gecko']} Firefox/".
	      $ver['firefox'],
	      "Windows XP / Firefox {$ver['firefox']}"),
	array("Mozilla/4.0 (compatible; MSIE {$ver['msie7']}; Windows ".
	      "{$ver['windows']}; SV1)", 'Windows XP / Internet Explorer 7'),
	array("Mozilla/4.0 (compatible; MSIE {$ver['msie6']}; Windows ".
	      "{$ver['windows']}; SV1)", 'Windows XP / Internet Explorer 6'),
	array("Opera/{$ver['opera']} (Windows {$ver['windows']}; U; en)",
	      "Windows XP / Opera {$ver['opera']}"),
	array("Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en-US; rv:".
	      "{$ver['moz_rev']}) Gecko/{$ver['gecko']} Firefox/{$ver['firefox']}",
	      "Mac OS X / Firefox {$ver['firefox']}"),
	array("Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/".
	      "{$ver['webkit']} (KHTML, like Gecko) Safari/{$ver['webkit']}",
	      'Mac OS X / Safari 3.0'),
	array("Opera/{$ver['opera']} (Macintosh; PPC Mac OS X; U; en)",
	      "Mac OS X / Opera {$ver['opera']}"),
	array("Mozilla/5.0 (X11; U; Linux i686; en-US; rv:{$ver['moz_rev']}) ".
	      "Gecko/{$ver['gecko']} Firefox/{$ver['firefox']}",
	      "Linux / Firefox {$ver['firefox']}"),
	array("Opera/{$ver['opera']} (X11; Linux i686; U; en)",
	      "Linux / Opera {$ver['opera']}"),
	array("Mozilla/5.0 (compatible; Konqueror/{$ver['konq']}; Linux) KHTML/".
	      "{$ver['konq_minor']} (like Gecko)",
	      "Linux / Konqueror {$ver['konq_minor']}"),
	array("Links ({$ver['links']}; Linux 2.6 i686; x)",
	      "Linux / Links ({$ver['links']})"),
	array("Lynx/{$ver['lynx']}","Any / Lynx {$ver['lynx']}"),
	array("Dillo/{$ver['dillo']}","Any / Dillo {$ver['dillo']}"),
	array("Wget/{$ver['wget']}","Any / Wget {$ver['wget']}"),
	array('1',' [ Custom ]')
);

define('IPREGEXP',
	'/^((?:[0-2]{0,2}[0-9]{1,2}\.){3}[0-2]{0,2}[0-9]{1,2})\:([0-9]{1,5}$/');

$checkbox_array=array(
	'URL_FORM',
	'REMOVE_COOKIES',
	'REMOVE_REFERER',
	'REMOVE_SCRIPTS',
	'REMOVE_OBJECTS',
	'ENCRYPT_URLS',
	'ENCRYPT_COOKIES'
);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>

<head>

<title><?php echo($LABEL['TITLE']); ?></title>
<link rel="stylesheet" type="text/css"
      href="<?php echo(THIS_SCRIPT); ?>?css_main" />

<style>
	input#proxy_submit_simple {
		display: <?php echo(($OPTIONS['SIMPLE_MODE']?'inline':'none')); ?>;
	}
</style>

<noscript><style>
	input#proxy_url { display: none; }
	a#proxy_link_author { float: none; }
	a#proxy_link_mode { display: none; }
	td#proxy_links_td { text-align: center; }
</style></noscript>

<script type="text/javascript"
        src="<?php echo(THIS_SCRIPT); ?>?js_funcs_nowrap"></script>

<script type="text/javascript" language="javascript"><!--
var advanced_mode=<?php echo(($OPTIONS['SIMPLE_MODE']?'false':'true')); ?>;
//--></script>

</head>

<body>

<form method="post" id="proxy_form" onsubmit="return main_submit_code();">
<input type="hidden" name="<?php echo(COOK_PREF); ?>_set_values" value="1" />
<input type="hidden" id="proxy_url_hidden" disabled="disabled"
       name="<?php echo(COOK_PREF); ?>" />
<table id="proxy_table" cellpadding="0" cellspacing="4">

<tr>
	<td colspan="2" id="proxy_title"><?php echo($LABEL['TITLE']); ?></td>
</tr>

<tr>
	<td><?php echo($LABEL['URL']); ?></td>
	<td>
		<input type="text" id="proxy_url" class="proxy_text"
		       value="<?php echo(ORIG_URL); ?>" />
		<noscript>
			<input type="text" id="proxy_url_noscript" class="proxy_text"
			       name="<?php echo(COOK_PREF); ?>"
			       value="<?php echo(ORIG_URL); ?>" />
		</noscript>
		<input type="submit" id="proxy_submit_simple" class="proxy_submit"
		       value="<?php echo($LABEL['SUBMIT_SIMPLE']); ?>" />
	</td>
</tr>

<?php if(!$CONFIG['FORCE_DEFAULT_TUNNEL']){ ?>
<tr name="advanced_mode">
	<td><?php echo($LABEL['TUNNEL']); ?></td>
	<td>
		<input type="text" id="proxy_tunnel_ip" class="proxy_text"
		       name="<?php echo(COOK_PREF); ?>_tunnel_ip"
		       value="<?php echo($CONFIG['TUNNEL_IP']); ?>" />
		<input type="text" size="5" maxlength="5"
		       id="proxy_tunnel_port" class="proxy_text"
		       name="<?php echo(COOK_PREF); ?>_tunnel_port"
		       value="<?php echo($CONFIG['TUNNEL_PORT']); ?>" />
	</td>
</tr>
<?php } ?>

<?php if(!$CONFIG['FORCE_DEFAULT_USER_AGENT']){ ?>
<tr name="advanced_mode">
	<td><?php echo($LABEL['USER_AGENT']); ?></td>
	<td>
		<select name="<?php echo(COOK_PREF); ?>_useragent"
		        id="proxy_useragent" class="proxy_select"
		        onchange="useragent_change();">
			<?php foreach($useragent_array as $useragent){ ?>
			<option value="<?php echo($useragent[0]); ?>"
			 <?php if($OPTIONS['USER_AGENT']==$useragent[0])
			 	echo ' selected="selected"'; ?>
			><?php echo($useragent[1]); ?></option>
			<?php } ?>
		</select>
	</td>
</tr>
<tr id="proxy_useragent_custom_tr" name="advanced_mode"
    class="display_<?php echo(($OPTIONS['USER_AGENT']=='1'?'tr':'none')); ?>">
	<td><?php echo($LABEL['USER_AGENT_CUSTOM']); ?></td>
	<td>
		<input type="text" id="proxy_useragent_custom" class="proxy_text"
		       name="<?php echo(COOK_PREF); ?>_useragent_custom"
		       value="<?php echo($OPTIONS['USER_AGENT']); ?>" />
	</td>
</tr>
<?php } ?>

<?php
foreach($checkbox_array as $checkbox){
	if(!$CONFIG['FORCE_DEFAULT_'.$checkbox]){
		$lowername=strtolower($checkbox);
?>

<tr name="advanced_mode">
	<td>&nbsp;</td>
	<td>
		<input type="checkbox" id="proxy_<?php echo($lowername); ?>"
		       class="proxy_checkbox"
		       name="<?php echo(COOK_PREF); ?>_<?php echo($lowername); ?>"
		       <?php if($OPTIONS[$checkbox]) echo 'checked="checked"'; ?>
		/>&nbsp;<?php echo($LABEL[$checkbox]); ?>
	</td>
</tr>
<?php }
} ?>

<tr name="advanced_mode">
	<td colspan="2">
		<input type="submit" id="proxy_submit_main" class="proxy_submit"
		       value="<?php echo($LABEL['SUBMIT_MAIN']); ?>" />
	</td>
</tr>

<tr>
	<td colspan="2" id="proxy_links_td">
		<a id="proxy_link_author" class="proxy_link" href="http://bcable.net/">
			Surrogafier&nbsp;v<?php echo(VERSION); ?>
			<b>&middot;</b>&nbsp;Brad&nbsp;Cable
		</a>
		<a id="proxy_link_mode" class="proxy_link" href="#"
		   onclick="toggle_mode();">
			<?php echo($OPTIONS['SIMPLE_MODE']?'Advanced':'Simple');
			?>&nbsp;Mode
		</a>
	</td>
</tr>

</table>
</form>

<noscript>
<br />
<b>**</b> Surrogafier has detected that your browser does not have Javascript
enabled. <b>**</b>
<br />
<b>**</b> Surrogafier requires Javascript in order to function to its full
potential. It is highly recommended that you have Javascript enabled for
privacy and security reasons. <b>**</b>
</noscript>

</body>

</html>

<?php exit(); }

# }}}

# FRAMED PAGE WITH URL FORM {{{

if(
	PAGETYPE_ID===PAGETYPE_FRAME_TOP &&
	$OPTIONS['URL_FORM'] &&
	ORIG_URL!=null
){ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo($LABEL['TITLE']); ?></title>
<style>

html, body {
	font-family: bitstream vera sans, arial;
	margin: 0px;
	padding: 0px;
	height: 100%;
	overflow: hidden;
}

form#url_form {
	margin: 0px;
	padding: 0px;
	height: 100%;
}

table#url_table {
	margin: 0px;
	padding: 0px;
	height: 100%;
	width: 100%;
}

td#url_table_td_input {
	width: 100%;
	padding: 3px;
	padding-left: 10px;
}

td#url_table_td_iframe {
	margin: 0px;
	padding: 0px;
	height: 100%;
}

a#url_link {
	color: #000000;
	font-weight: bold;
	padding: 8px;
	text-decoration: none;
}

a#url_link:hover {
	color: #000000;
	font-weight: bold;
	padding: 8px;
	text-decoration: underline;
}

input {
	border: 1px solid #000000;
	color: #000000;
}

input#url_input {
	width: 100%;
}

input#url_submit {
	background-color: #FFFFFF;
	margin-right: 3px;
}

iframe#url_iframe {
	border: 0px;
	border-top: 1px solid #000000;
	width: 100%;
	height: 100%;
}

</style>

<script type="text/javascript">
<!--

<?php echo(COOK_PREF); ?>=true;

function submit_code(){
<?php if($OPTIONS['ENCRYPT_URLS']){ ?>
	document.forms[0].<?php echo(COOK_PREF); ?>.value=
		<?php echo(COOK_PREF); ?>_pe.proxenc(
			document.forms[0].<?php echo(COOK_PREF); ?>.value
		);
<?php } ?>
	return true;
}

//-->
</script>

</head>
<body>

<form id="url_form" method="get" onsubmit="return submit_code();">
<input type="hidden" name="" value="" />

<table cellspacing="0" cellpadding="0" id="url_table">
<tr>
	<td>
		<a href="<?php echo(THIS_SCRIPT.'?=-&='.OENC_URL); ?>"
		   id="url_link">Surrogafier
		</a>
	</td>
	<td>&nbsp;</td>
	<td id="url_table_td_input">
		<input type="text" id="url_input" name=""
			   value="<?php echo(ORIG_URL); ?>" />
	</td>
	<td>&nbsp;</td>
	<td>
		<input type="submit" id="url_submit"
		       value="<?php echo($LABEL['SUBMIT_SIMPLE']); ?>" />
	</td>
</tr>

<tr>
	<td colspan="5" id="url_table_td_iframe">
		<iframe frameborder="0" id="url_iframe"
		        name="<?php echo(COOK_PREF); ?>_top"
		        src="<?php echo(THIS_SCRIPT.'?=_&='.OENC_URL); ?>"></iframe>
	</td>
</tr>

</table>

</form>

</body>
</html>
<?php exit(); }

# }}}

# PRE-JAVASCRIPT CONSTANTS & FUNCTIONS {{{
# these constants and functions must be defined before JS is output, but would
# be more readably located later.

#define('AURL_LOCK_REGEXP','(?:(?:javascript|mailto|about):|~|%7e)');
define('FRAME_LOCK_REGEXP','/^(?:(?:javascript|mailto|about):|#)/i');
define('AURL_LOCK_REGEXP',
	'/^(?:(?:javascript|mailto|about):|#|'.
	str_replace(array('/','.'),array('\/','\.'),addslashes(THIS_SCRIPT)).')/i');
define('URLREG','/^'.
	'(?:([a-z]*)?(?:\:?\/\/))'.      # proto
	'(?:([^\@\/]*)\@)?'.             # userpass
	'([^\/:\?\#\&]*)'.               # servername
	'(?:\:([0-9]+))?'.               # portval
	'(\/[^\&\?\#]*?)?'.              # path
	'([^\/\?\#\&]*(?:\&[^\?\#]*)?)'. # file
	'(?:\?([\s\S]*?))?'.             # query
	'(?:\#([\s\S]*))?'.              # label
'$/ix');

function escape_regexp($regexp,$dollar=false){
	$regexp=
		str_replace('\\','\\\\',
		str_replace('\'','\\\'',
		str_replace('"','\\"',
		str_replace(chr(10),'\n',
		str_replace(chr(13),'\r',
		str_replace(chr(9),'\t',
		$regexp
	))))));
	return ($dollar?preg_replace('/[\\\\]+(?=[0-9])/','\\\\$',$regexp):
		 preg_replace('/[\\\\]+(?=[0-9])/','\\\\\\\\',$regexp)); #*
}

# }}}

# STATIC CACHING FUNCTION {{{
function static_cache(){
	# headers
	header('Cache-Control: must-revalidate');
	header('Pragma: cache');

	# last modified
	$lastmod=filemtime(THIS_FILE);
	$ifmod=$_SERVER['HTTP_IF_MODIFIED_SINCE'];

	if(!empty($ifmod)){
		if(strpos($ifmod,';'))
			$ifmod=substr($ifmod,0,strpos($ifmod,';'));
		$ifmod=strtotime($ifmod);

		if($ifmod==$lastmod){
			header('HTTP/1.1: 304 Not Modified');
			exit();
		}
	}
	header('Last-Modified: '.gmdate('D, d M Y H:i:s',$lastmod).' GMT');
}

# }}}

# CSS STATIC CONTENT {{{

# CSS MAIN {{{

if(QUERY_STRING=='css_main'){
	header('Content-Type: text/css');
	static_cache();

	foreach($STYLE as $id=>$style){
		echo "{$id} {{$style}}\n\n";
	}

	echo ".display_none { display: none !important; }\n";
	echo ".display_tr { display: table-row !important; }\n";

	exit();
}

# }}}

# CSS URL FRAME {{{

if(QUERY_STRING=='css_url_frame'){
	header('Content-Type: text/css');
	static_cache();

	foreach($STYLE_URL_FORM as $id=>$style){
		echo "{$id} {{$style}}\n\n";
	}

	exit();
}

# }}}

# }}}

# JAVASCRIPT STATIC CONTENT/FUNCTIONS {{{

if(
	QUERY_STRING=='js_funcs' ||
	QUERY_STRING=='js_funcs_framed' ||
	QUERY_STRING=='js_funcs_nowrap'
){

	if(QUERY_STRING=='js_funcs_nowrap')
		$do_wrap=false;
	else $do_wrap=true;

	static_cache();

?>//<script type="text/javascript">

// JAVASCRIPT FUNCS: FUNCTIONS FOR NON-WRAPPED PAGES {{{

<?php if(!$do_wrap){ ?>

function useragent_change(){
	var ua=document.getElementById('proxy_useragent');
	var uac=document.getElementById('proxy_useragent_custom');
	var uacTR=document.getElementById('proxy_useragent_custom_tr');

	if(parseInt(ua.value)==1) uacTR.className="display_tr";
	else uacTR.className="display_none";
}

function toggle_mode(){
	var url=document.getElementById('proxy_url');
	var simpBut=document.getElementById('proxy_submit_simple');
	var modeLink=document.getElementById('proxy_link_mode');
	var advTR=document.getElementsByName('advanced_mode');

	for(var i=0; i<advTR.length; i++){
		if(advanced_mode) advTR[i].style.display="none";
		else advTR[i].style.display="table-row";
	}

	if(advanced_mode){
		url.style.width="<?php echo($CONFIG['SIMPLE_MODE_URLWIDTH']) ?>";
		simpBut.style.display="inline";
		modeLink.innerHTML="Advanced&nbsp;Mode";
	}

	else{
		url.style.width="100%";
		simpBut.style.display="none";
		modeLink.innerHTML="Simple&nbsp;Mode";
	}

	advanced_mode=!advanced_mode;
}

function main_submit_code(){
	var dgEBI=function(id){ return document.getElementById(id); }
	dgEBI('proxy_url_hidden').disabled=false;
	if(dgEBI('proxy_encrypt_urls').checked)
		dgEBI('proxy_url_hidden').value=
			<?php echo(COOK_PREF); ?>_pe.proxenc(dgEBI('proxy_url').value);
	else dgEBI('proxy_url_hidden').value=dgEBI('proxy_url').value;
	return true;
}

<?php } ?>

// }}}

// JAVASCRIPT FUNCS: CRYPTOGRAPHIC FUNCTIONS {{{

<?php echo(COOK_PREF); ?>_pe={

expon:function(a,b){
	var num;
	if(b==0) return 1;
	num=a; b--;
	while(b>0){ num*=a; b--; }
	return num;
},

dectobin:function(){
	var dec=arguments[0],chars=arguments[1]||8,binrep="";
	for(j=chars-1;j>=0;j--){
		if(dec>=this.expon(2,j)){
			binrep+="1"; dec-=this.expon(2,j);
		}
		else binrep+="0";
	}
	return binrep;
},

bintodec:function(){
	var bin=arguments[0],chars=arguments[1]||8,dec=0;
	for(var j=0;j<chars;j++)
		if(bin.substring(j,j+1)=="1") dec+=this.expon(2,chars-1-j);
	return dec;
},

b64e:function(string){
	var encstr="",binrep="";
	var charbin,charnum;
	for(var i=0;i<string.length;i++){
		charnum=string.charCodeAt(i);
		binrep+=this.dectobin(charnum);
	}
	while(binrep.length%6) binrep+="00";
	for(var i=1;i*6<=binrep.length;i++){
		charbin=binrep.substring((i-1)*6,i*6);
		charnum=this.bintodec(charbin,6);
		if(charnum<=25) charnum+=65;
		else if(charnum<=51) charnum+=71;
		else if(charnum<=61) charnum-=4;
		else if(charnum==62) charnum=43;
		else if(charnum==63) charnum=47;
		encstr+=String.fromCharCode(charnum);
	}
	while(encstr.length%8) encstr+="=";
	return encstr;
},

proxenc:function(url){
	var new_url="";
	var charnum;
	if(url.substring(0,1)=="~" || url.substring(0,3).toLowerCase()=="%7e")
		return url;
	url=encodeURIComponent(url);
	var sess_pref="<?php echo(SESS_PREF); ?>";
	for(i=0;i<url.length;i++){
		charnum=url.charCodeAt(i);
		charnum+=sess_pref.charCodeAt(i%sess_pref.length);
		while(charnum>126) charnum-=94;
		new_url+=String.fromCharCode(charnum);
	}
	return "~"+encodeURIComponent(this.b64e(new_url));
},

b64d:function(str){
	var binrep="",decstr="";
	var charnum,charbin;
	str=str.replace(/[=]*$/,"");
	for(var i=0;i<str.length;i++){
		charnum=str.charCodeAt(i);
		if(charnum>=97) charnum-=71;
		else if(charnum>=65) charnum-=65;
		else if(charnum>=48) charnum+=4;
		else if(charnum==43) charnum=62;
		else if(charnum==47) charnum=63;
		binrep+=this.dectobin(charnum,6);
	}
	for(var i=0;i+8<binrep.length;i+=8){
		charbin=binrep.substr(i,8);
		decstr+=String.fromCharCode(this.bintodec(charbin));
	}
	return decstr;
},

proxdec:function(url){
	var new_url,charnum;
	if(url.substr(0,1)!='~' && url.substr(0,3).toLowerCase()!='%7e') return url;
	while(url.substr(0,1)=='~' || url.substr(0,3).toLowerCase()=='%7e'){
		url=url.substr(1,url.length-1);
		url=this.b64d(url);
		new_url="";
		for(i=0;i<url.length;i++){
			charnum=url.charCodeAt(i);
			charnum-="<?php echo(SESS_PREF); ?>".charCodeAt(
				i%"<?php echo(SESS_PREF); ?>".length);
			while(charnum<32) charnum+=94;
			new_url+=String.fromCharCode(charnum);
		}
		url=new_url;
	}
	return decodeURIComponent(url); // urldecode()
},

}

// }}}

// JAVASCRIPT FUNCS: COOK_PREF OBJECT {{{

<?php if($do_wrap){ ?>

<?php echo(COOK_PREF); ?>={

parse_attrs:new Array(
	'action',
	'backgroundImage',
	'codebase',
	'href',
	'location',
	'pluginspage',
	'src'
),

URLREG:<?php echo(substr(URLREG,0,strlen(URLREG)-1)); ?>,
THIS_SCRIPT:"<?php echo(THIS_SCRIPT); ?>",
COOK_PREF:"<?php echo(COOK_PREF); ?>",
pe:<?php echo(COOK_PREF); ?>_pe,
gen_curr_urlobj:function(){ this.curr_urlobj=new this.aurl(this.CURR_URL); },

getCookieArr:function(){ return document.cookie.split("; "); },

aurl:function(url,topurl){
	this.URLREG=<?php echo(COOK_PREF); ?>.URLREG;
	this.THIS_SCRIPT=<?php echo(COOK_PREF); ?>.THIS_SCRIPT;
	this.ENCRYPT_URLS=<?php echo(COOK_PREF); ?>.ENCRYPT_URLS;

	this.trim=function(str){ return str.replace(/^\s*([\s\S]*?)\s*$/,"$1"); }

	this.get_fieldreq=function(fieldno,value){
		var fieldreqs=new Array();
		fieldreqs[2]="://"+(value!=""?value+"@":"");
		fieldreqs[4]=(value!="" && parseInt(value)!=80?":"+parseInt(value):"");
		fieldreqs[7]=(value!=""?"?"+value:"");
		fieldreqs[8]=(value!=""?"#"+value:"");
		if(fieldreqs[fieldno]!=undefined) return value;
		// return (value!=""?null:value);
		else return fieldreqs[fieldno];
	}

	this.set_proto=function(proto){
		if(proto==undefined) proto="http";
		if(this.locked) return;
		this.proto=proto;
	}
	this.get_proto=function(){ return this.proto; }

	this.get_userpass=function(){ return this.userpass; }
	this.set_userpass=function(userpass){
		if(userpass==undefined) userpass="";
		this.userpass=userpass;
	}
	this.get_servername=function(){ return this.servername; }
	this.set_servername=function(servername){
		if(servername==undefined) servername="";
		this.servername=servername;
	}
	this.get_portval=function(){
		return (
			this.portval==""?
			(this.get_proto()=="https"?"443":"80"):
			this.portval
		);
	}
	this.set_portval=function(port){
		if(port==undefined) port="";
		this.portval=((parseInt(port)!=80)?port:"").toString();
	}
	this.get_path=function(){ // ***
		if(this.path.indexOf("/../")!=-1)
			this.path=this.path.replace(/(?:\/[^\/]+){0,1}\/\.\.\//g,"/");
		if(this.path.indexOf("/./")!=-1)
			while((path=this.path.replace("/./","/")) && path!=this.path)
				this.path=path;
		return this.path;
	}
	this.set_path=function(path){
		if(path==undefined) path="/"; this.path=path;
	}
	this.get_file=function(){ return this.file; }
	this.set_file=function(file){ if(file==undefined) file=""; this.file=file; }
	this.get_query=function(){ return this.query; }
	this.set_query=function(query){
		if(query==undefined) query="";
		this.query=query;
	}
	this.get_label=function(){ return this.label; }
	this.set_label=function(label){
		if(label==undefined) label="";
		this.label=label;
	}

	this.get_url=function(){
		if(this.locked) return this.url;
		return this.get_proto()+"://"+
			(this.get_userpass()==""?"":this.get_userpass()+"@")+
			this.get_servername()+
			(
				this.get_portval()==undefined || this.get_portval()?
				"":
				":"+parseInt(this.get_portval())
			)+
			this.get_path()+this.get_file()+
			(this.get_query()==""?"":"?"+this.get_query())+
			(this.get_label()==""?"":"#"+this.get_label());
	}

	this.surrogafy=function(){
		var url=this.get_url();
		if(
			this.locked ||
			this.get_proto()+
			this.get_fieldreq(2,this.get_userpass())+
			this.get_servername()+
			this.get_path()+
			this.get_file()
			 ==
			this.THIS_SCRIPT
		) return url;
		var label=this.get_label();
		this.set_label();
		if(this.ENCRYPT_URLS && !this.locked)
			url=<?php echo(COOK_PREF); ?>.pe.proxenc(url);
		// urlencode()d
		//url=this.THIS_SCRIPT+"?="+(!this.ENCRYPT_URLS?escape(url):url);
		url=this.THIS_SCRIPT+"?="+url;
		this.set_label(label);
		return url;
	}

	if(url.length><?php echo($CONFIG['MAXIMUM_URL_LENGTH'])?>)
		this.url="";
	else{
		// parse like PHP does for &#num; HTML entities? // TODO?
		//this.url=preg_replace("/&#([0-9]+);/e","chr(\\1)");
		this.url=this.trim(url.
			replace("&amp;","&").
			replace("\r","").
			replace("\n","")
		);
	}

	this.topurl=topurl;
	this.locked=this.url.match(<?php echo(AURL_LOCK_REGEXP); ?>);
	this.locked=(this.locked==null?false:true);

	if(!this.locked){
		var urlwasvalid=true;
		if(!this.url.match(this.URLREG)){
			urlwasvalid=false;
			if(this.topurl==undefined)
				this.url=
					"http://"+
					(
						this.url.charAt(0)==":" || this.url.charAt(0)=="/"?
						this.url.substring(1):
						this.url
					)+
					(this.url.indexOf("/")!=-1?"":"/");
			else{
				var newurl=
					this.topurl.get_proto()+
					"://"+
					this.get_fieldreq(2,this.topurl.get_userpass())+
					this.topurl.get_servername()+
					(
						this.topurl.get_portval()!=80 && (
							this.topurl.get_proto()=="https"?
							this.topurl.get_portval()!=443:true
						)?
						":"+this.topurl.get_portval():
						""
					);
				if(this.url.substring(0,1)!="/") newurl+=this.topurl.get_path();
				this.url=newurl+this.url;
			}
		}

		this.set_proto(
			(
				urlwasvalid || this.topurl==undefined?
				this.url.replace(/^([^:]+).*$/,"\$1"):
				this.topurl.get_proto()
			)
		);
		this.set_userpass(this.url.replace(this.URLREG,"\$2"));
		this.set_servername(this.url.replace(this.URLREG,"\$3"));
		this.set_portval(this.url.replace(this.URLREG,"\$4"));
		this.set_path(this.url.replace(this.URLREG,"\$5"));
		this.set_file(this.url.replace(this.URLREG,"\$6"));
		this.set_query(this.url.replace(this.URLREG,"\$7"));
		this.set_label(this.url.replace(this.URLREG,"\$8"));
	}

	//if(!this.locked && !this.url.match(this.URLREG)) havok(7,this.url); //*
},

surrogafy_url:function(url,topurl,addproxy){
	url=url.toString();
	if(!url.substring) return;
	if(addproxy==undefined) addproxy=true;
	var urlquote="";
	if(
		(url.substring(0,1)=="\"" || url.substring(0,1)=="'") &&
		url.substring(0,1)==url.substring(url.length-1,url.length)
	){
		urlquote=url.substring(0,1);
		url=url.substring(1,url.length-1);
	}
	if(topurl==undefined) topurl=this.curr_urlobj;
	var urlobj=new this.aurl(url,topurl);
	var new_url=(addproxy?urlobj.surrogafy():urlobj.get_url());
	if(urlquote!="") new_url=urlquote+new_url+urlquote;
	return new_url;
},

surrogafy_url_toobj:function(url,topurl,addproxy){
	url=url.toString();
	if(!url.substring) return;
	if(addproxy==undefined) addproxy=true;
	if(
		(url.substring(0,1)=="\"" || url.substring(0,1)=="'") &&
		url.substring(0,1)==url.substring(url.length-1,url.length)
	) url=url.substring(1,url.length-1);
	if(topurl==undefined) topurl=this.curr_urlobj;
	return new this.aurl(url,topurl);
},

de_surrogafy_url:function(url){
	if(url==undefined) return "";
	url=url.toString();
	if(
		url.match(<?php echo(FRAME_LOCK_REGEXP); ?>) ||
		!url.match(<?php echo(AURL_LOCK_REGEXP); ?>)
	) return url;
	// urldecode()
	return this.pe.proxdec(decodeURIComponent(
		url.substring(url.indexOf('?')+1).replace(
			<?php echo(PAGETYPE_REGEXP); ?>,"\$2")));
},

add_querystuff:function(url,querystuff){
	var pos=url.indexOf('?');
	return url.substr(0,pos+1)+querystuff+url.substr(pos+1,url.length-pos);
},

preg_match_all:function(regexpstr,string){
	var matcharr=new Array();
	var regexp=new RegExp(regexpstr);
	var result;
	while(true){
		result=regexp.exec(string);
		if(result!=null) matcharr.push(result);
		else break;
	}
	return matcharr;
},

framify_url:function(url,frame_type){
	if(frame_type===<?php echo(PAGETYPE_NULL); ?>)
		return url;
	var urlquote="";
	if(
		(url.substring(0,1)=="\"" || url.substring(0,1)=="'") &&
		url.substring(0,1)==url.substring(url.length-1,url.length)
	){
		urlquote=url.substring(0,1);
		url=url.substring(1,url.length-1);
	}
	if(!url.match(<?php echo(FRAME_LOCK_REGEXP); ?>)){
		var query;
		if(frame_type===<?php echo(PAGETYPE_FRAME_TOP); ?>)
			query='&=';
		else if(frame_type===<?php echo(PAGETYPE_FRAMED_CHILD); ?>) query='.&=';
		else if(
			frame_type===<?php echo(PAGETYPE_FRAMED_PAGE); ?> ||
			this.PAGE_FRAMED
		) query='_&=';
		else query='';
		url=url.replace(
			/^([^\?]*)[\?]?<?php echo(PAGETYPE_MINIREGEXP); ?>([^#]*?[#]?.*?)$/,
			'\$1?='+query+'\$3');
	}
	if(urlquote!="") url=urlquote+url+urlquote;
	return url;
},

parse_html:function(regexp,partoparse,html,addproxy,framify){
	var match,begin,end,nurl;
	if(html.match(regexp)){
		var matcharr=this.preg_match_all(regexp,html);
		var newhtml="";
		for(var key in matcharr){
			/*match=matcharr[i];
			nurl=this.surrogafy_url(match[partoparse],undefined,addproxy);
			nhtml=match[0].replace(match[partoparse],nurl);
			html=html.replace(match[0],nhtml);*/
			match=matcharr[key];
			if(match[partoparse]!=undefined){
				begin=html.indexOf(match[partoparse]);
				end=begin+match[partoparse].length;
				nurl=this.surrogafy_url(match[partoparse],undefined,addproxy);
				if(framify) nurl=this.framify_url(nurl,framify);
				newhtml+=html.substring(0,begin)+nurl;
				html=html.substring(end);
			}
		}
		html=newhtml+html;
	}
	return html;
},

parse_all_html:function(){
	if(arguments[0]==null) return;
	var html=arguments[0].toString();
	var key;
	for(var key in regexp_arrays){
		if((arguments.length>1 && key!=arguments[1]) || key=='text/javascript')
			continue;
		arr=regexp_arrays[key];
		for(var regexp_arraykey in arr){
			regexp_array=arr[regexp_arraykey];
			if(regexp_array[0]==undefined) continue;
			if(regexp_array[0]==1)
				html=html.replace(regexp_array[1],regexp_array[2]);
			else if(regexp_array[0]==2){
				addproxy=(regexp_array.length>3?regexp_array[3]:true);
				framify=(regexp_array.length>4?regexp_array[4]:false);
				html=this.parse_html(
					regexp_array[1],regexp_array[2],html,addproxy,framify);
			}
		}
	}
	return html;
},

form_button:null,
form_encrypt:function(form){
	if(form.method=='post') return true;
	//action=form.<php echo(COOK_PREF); ?>.value;
	var action=form.getElementsByName(this.COOK_PREF)[0].value;
	for(var i=1;i<form.elements.length;i++){
		if(
			form.elements[i].disabled || form.elements[i].name=='' ||
			form.elements[i].value=='' || form.elements[i].type=='reset'
		) continue;
		if(form.elements[i].type=='submit'){
			if(form.elements[i].name!=this.form_button) continue;
			this.form_button=null;
		}
		var pref;
		if(!action.match(/\?/)) pref="?";
		else pref="&";
		action+=pref+form.elements[i].name+"="+form.elements[i].value;
	}
	location.href=this.surrogafy_url(action);
	return false;
},

setAttr:function(obj,attr,val){
	if(typeof(attr)!=typeof("")){
		attr=attr.toString();
		attr=attr.substr(1,attr.length-2);
	}

	if(attr=="innerHTML"){
		obj[attr]=this.parse_all_html(val);
		return obj[attr];
	}

	if(obj==document && attr=="cookie"){
		var COOK_REG=/^([^=]*)=([^;]*)(?:;[\s\S]*?)?$/i;
		var realhost=
			this.LOCATION_HOSTNAME.replace("/^www/i","").replace(".","_");
		var cookkey=val.replace(COOK_REG,"\$1");
		var cookval=val.replace(COOK_REG,"\$2");
		if(this.ENCRYPT_COOKIES){
			cookkey=proxenc(cookkey);
			cookval=proxenc(cookval);
		}
		var newcookie=
			realhost+"<?php echo(COOKIE_SEPARATOR); ?>"+
			cookkey+"="+cookval+"; ";
		document.cookie=newcookie;
		return newcookie;
	}

	if(obj==location && attr=="hostname") return this.LOCATION_HOSTNAME;

	if(obj==location && attr=="search"){
		if(val.substr(0,1)=="?") val=val.substr(1);
		this.curr_urlobj.set_query(val);
		val=this.curr_urlobj.get_url();
		attr="href";
	}

	var is_parse_attr=false;
	for(var parse_attr in this.parse_attrs){
		if(attr==parse_attr){
			is_parse_attr=true;
			break;
		}
	}

	var proxval=val;
	if(is_parse_attr){
		proxval=this.surrogafy_url(val);

		// tags framified must match REGEXPS with regexp_array[5]
		if(obj.tagName=="A" || obj.tagName=="AREA")
			proxval=this.framify_url(
				proxval,<?php echo(COOK_PREF); ?>.NEW_PAGETYPE_FRAME_TOP);
		else if(obj.tagName=="FRAME" || obj.tagName=="IFRAME")
			proxval=this.framify_url(
				proxval,<?php echo(PAGETYPE_FRAMED_CHILD); ?>);
	}

	if(this.URL_FORM){
		if((obj==location && attr=="href") || attr=="location"){
			urlobj=this.surrogafy_url_toobj(val);
			if(!urlobj.locked) proxval=this.add_querystuff(proxval,"=&");
			return this.thetop.location.href=proxval;
		}
		else return obj[attr]=proxval;
	}
	else return obj[attr]=proxval;
},

getAttr:function(obj,attr){
	if(typeof(attr)!=typeof("")){
		attr=attr.toString();
		attr=attr.substr(1,attr.length-2);
	}

	if(obj==document && attr=="cookie"){
		var ocookies=this.getCookieArr();
		var cookies="",ocook;
		var COOK_REG=
			/^([\s\S]*)<?php echo(COOKIE_SEPARATOR); ?>([^=]*)=([\s\S]*)(?:; )?$/i;
		for(var key in ocookies){
			ocook=ocookies[key];
			if(typeof(ocook)!=typeof("")) continue;
			if(ocook.match(COOK_REG)==null) continue;
			var realhost=
				this.LOCATION_HOSTNAME.replace("/^www/i","").replace(".","_");
			var cookhost=ocook.replace(COOK_REG,"\$1");
			if(cookhost==realhost){
				if(this.ENCRYPT_COOKIES){
					var cookkey=this.pe.proxdec(ocook.replace(COOK_REG,"\$2"));
					var cookval=this.pe.proxdec(ocook.replace(COOK_REG,"\$3"));
					cookies+=cookkey+"="+cookval+"; ";
				}
				else cookies+=ocook.replace(COOK_REG,"\$2=\$3; ");
			}
		}
		return cookies;
	}

	if(obj==navigator){
		if(this.USERAGENT=="-1" && (attr!="plugins" && attr!="mimeType"))
			return undefined;
		if(this.USERAGENT=="") return obj[attr];
		var msie=this.USERAGENT.match(/msie/i);
		var UA_REG=
			/^([^\/\(]*)\/?([^ \(]*)[ ]*(\(?([^;\)]*);?([^;\)]*);?([^;\)]*);?([^;\)]*);?([^;\)]*);?[^\)]*\)?)[ ]*([^ \/]*)\/?([^ \/]*).*$/i;
		switch(attr){
			case "appName":
				var tempappname=(
					msie?
					"Microsoft Internet Explorer":
					this.USERAGENT.replace(UA_REG,"\$1")
				);
				if(tempappname=="Opera" || tempappname=="Mozilla")
					tempappname="Netscape";
				return tempappname;
			case "appCodeName": return this.USERAGENT.replace(UA_REG,"\$1");
			case "appVersion":
				return (
					msie?
					this.USERAGENT.replace(UA_REG,"\$2 \$3"):
					this.USERAGENT.replace(UA_REG,"\$2 (\$4; \$7)")
				);
			case "language":
				return (msie?undefined:this.USERAGENT.replace(UA_REG,"\$7"));
			case "mimeType": return navigator.mimeType;
			case "oscpu":
				return (msie?undefined:this.USERAGENT.replace(UA_REG,"\$6"));
			case "platform":
				var tempplatform=this.USERAGENT.replace(UA_REG,"\$4");
				return (
					tempplatform=="compatible" || tempplatform=="Windows"?
					"Win32":
					this.USERAGENT.replace(UA_REG,"\$6")
				);
			case "plugins":
				return (
					!<?php echo(COOK_PREF); ?>.REMOVE_OBJECTS?
					navigator.plugins:
					undefined
				);
			case "product":
				return (msie?undefined:this.USERAGENT.replace(UA_REG,"\$9"));
			case "productSub":
				return (msie?undefined:this.USERAGENT.replace(UA_REG,"\$10"));
			case "userAgent": return this.USERAGENT;
			default: return undefined;
		}
	}

	var val;
	if(obj==location && attr=="search") val=location.href;
	else val=obj[attr];

	var is_parse_attr=false;
	for(var parse_attr in this.parse_attrs){
		if(attr==parse_attr){
			is_parse_attr=true;
			break;
		}
	}

	if(is_parse_attr)
		val=this.de_surrogafy_url(val);

	if(obj==location && attr=="search") val=val.replace(/^[^?]*/,"");
	return val;
},

eventify:function(a1,a2){
	document.getElementsByTagName("head")[0].addEventListener("load",function(){
		<?php echo(COOK_PREF); ?>.setParentStuff(a1,a2);
	},false);
	window.addEventListener("load",function(){
		<?php echo(COOK_PREF); ?>.setParentStuff(a1,a2);
	},false);
	this.setParentURL(this.CURR_URL);
},

setParentURL:function(url){
	if(this.thetop!=null && this.thetop!=window){
		this.thetop.document.getElementById('url').value=url;
		this.thetop.document.getElementById('proxy_link').href=
			this.add_querystuff(this.surrogafy_url(url),"=-&");
	}
},

// amazing creativity with the name on my part
setParentStuff:function(proto,server){
	var topdoc=this.thetop.document;
	topdoc.title=document.title;

	// find and set shortcut icon
	var tophead=topdoc.getElementsByTagName("head")[0];
	var links=tophead.getElementsByTagName("link");
	var link=null;
	for(var i=0; i<links.length; i++){
		if(links[i].type=="image/x-icon" && links[i].rel=="shortcut icon")
			link=links[i];
	}

	if(tophead.getElementsByTagName("link").length>0)
		tophead.removeChild(topdoc.getElementsByTagName("link")[0]);

	var favicon=topdoc.createElement("link");
	favicon.type="image/x-icon";
	favicon.rel="shortcut icon";
	favicon.href=(
		link==null?
		this.surrogafy_url(proto+"://"+server+"/favicon.ico"):
		link.href
	);
	tophead.appendChild(favicon);
},

XMLHttpRequest_wrap:function(xmlhttpobj){
	xmlhttpobj.<?php echo(COOK_PREF); ?>_open=xmlhttpobj.open;
	xmlhttpobj.open=<?php echo(COOK_PREF); ?>.XMLHttpRequest_open;
	return xmlhttpobj;
},

XMLHttpRequest_open:function(){
	if(arguments.length<2) return;
	arguments[1]=<?php echo(COOK_PREF); ?>.surrogafy_url(arguments[1]);
	return this.<?php echo(COOK_PREF); ?>_open.apply(this,arguments);
},

// WRAPPED FUNCTIONS AND OBJECTS
thetop:top,
theparent:parent,
setTimeout:window.setTimeout,
setInterval:window.setInterval,
document_write_queue:"",
purge:function(){
	thehtml=this.document_write_queue;
	if(thehtml=="") return;
	thehtml=this.parse_all_html(thehtml);
	this.document_write_queue="";
	document.write_<?php echo(COOK_PREF); ?>(thehtml);
},

purge_noparse:function(){
	thehtml=this.document_write_queue;
	if(thehtml=="") return;
	this.document_write_queue="";
	document.write_<?php echo(COOK_PREF); ?>(thehtml);
}

}

<?php } ?>

// }}}

// JAVASCRIPT FUNCS: WRAPPING/HOOKING {{{

<?php if($do_wrap){ ?>

document.write_<?php echo(COOK_PREF); ?>=document.write;
document.writeln_<?php echo(COOK_PREF); ?>=document.writeln;
document.write=function(html){
	<?php echo(COOK_PREF); ?>.document_write_queue+=html;
}
document.writeln=function(html){
	<?php echo(COOK_PREF); ?>.document_write_queue+=html+"\n";
}

window.open_<?php echo(COOK_PREF); ?>=window.open;
window.open=document.open=function(){
	if(arguments.length<1) return;
	var url=<?php echo(COOK_PREF); ?>.surrogafy_url(arguments[0]);
	if(
		(url.substring(0,1)=="\"" || url.substring(0,1)=="'") &&
		url.substring(0,1)==url.substring(url.length-1,url.length)
	) url=url.substring(1,url.length-1);
	arguments[0]=url;
	return window.open_<?php echo(COOK_PREF); ?>.apply(this.caller,arguments);
}

setTimeout=function(){
	if(arguments.length<2) return;
	arguments[0]=<?php echo(COOK_PREF); ?>.parse_all_html(
		arguments[0],"application/x-javascript");
	return <?php echo(COOK_PREF); ?>.setTimeout.apply(this,arguments);
}

setInterval=function(){
	if(arguments.length<2) return;
	arguments[0]=<?php echo(COOK_PREF); ?>.parse_all_html(
		arguments[0],"application/x-javascript");
	return <?php echo(COOK_PREF); ?>.setInterval.apply(this,arguments);
}

/* hooking for eval(), not necessary anymore, but worked relatively well in the
 * past
/*eval_<?php echo(COOK_PREF); ?>=eval;
eval=function(){
	if(arguments.length<1) return;
	arguments[0]=<?php echo(COOK_PREF); ?>.parse_all_html(
		arguments[0],"application/x-javascript");
	return eval_<?php echo(COOK_PREF); ?>.apply(this.caller,arguments);
}*/

// wrap top and parent objects for anti-frame breaking
if(<?php echo(COOK_PREF); ?>.PAGE_FRAMED){
	if(parent==top) parent=self;
	if(top!=self) top=<?php echo(COOK_PREF); ?>.thetop.frames[0];
}

<?php } ?>

// }}}

//</script><?php exit(); }

# }}}

# REGEXPS {{{

# This is where all the parsing is defined.  If a site isn't being
# parsed properly, the problem is more than likely in this section.
# The rest of the code is just there to set up this wonderful bunch
# of incomprehensible regular expressions.


# REGEXPS: CONVERSION TO JAVASCRIPT {{{

function bool_to_js($bool){ return ($bool?'true':'false'); }
function fix_regexp($regexp){
	global $js_varsect;
	$regexp=preg_replace('/\(\?P\<[a-z0-9_]+\>/i','(',$regexp);
	$regexp=preg_replace('/\(\?P\>[a-z0-9_]+\)/i',$js_varsect,$regexp);
	return $regexp;
}
function convertarray_to_javascript(){
	global $regexp_arrays;
	$js='regexp_arrays=new Array('.count($regexp_arrays).");\n";
	reset($regexp_arrays);
	while(list($key,$arr)=each($regexp_arrays)){
		$js.="regexp_arrays[\"$key\"]=new Array(".count($arr).");\n";
		for($i=0;$i<count($arr);$i++){
			$js.="regexp_arrays[\"$key\"][$i]=new Array(";
			if($arr[$i][0]==1)
				$js.=
					'1,'.escape_regexp(fix_regexp($arr[$i][2])).'g,"'.
					escape_regexp(fix_regexp($arr[$i][3]),true).'"';
			elseif($arr[$i][0]==2)
				$js.=
					'2,'.escape_regexp(fix_regexp($arr[$i][2])).
					"g,{$arr[$i][3]}".
					(count($arr[$i])<5?null:','.bool_to_js($arr[$i][4])).
					(count($arr[$i])<6?null:",{$arr[$i][5]}");
			$js.=");\n";
		}
	}
	return stripslashes($js);
}

# }}}

# REGEXPS: VARIABLES {{{

global $regexp_arrays, $js_varsect;

/* Variable Naming Tags
js:     Javascript
html:   HTML
hook:   are used to determine what is going to be hooked by the script
g:      global helper variable
h:      local/short term helper variable
l:      'looker' (uses lookaheads or lookbehinds for anchoring)
n:      'not'
*/

# REGEXPS: VARIABLES: Helper Variables {{{

/*
$g_justspace:      just space characters (no newlines)   0+
$g_plusjustspace:  just space characters (no newlines)   1+
$g_anyspace:       any space characters at all           0+
$g_plusspace:      any space characters at all           1+
$g_operand:        any operand                           1
$g_n_operand:      anything but an operand               1
$g_quoteseg:       any quote segment                     2+
$g_regseg:         any regular expression segment        2+
*/

$g_justspace="[\t ]*";
$g_plusjustspace="[\t ]+";
$g_anyspace="[\t\r\n ]*";
$g_plusspace="[\t\r\n ]+";
$g_operand='(?:\|\||\&\&|[\+\-\/\*\|\&\%\?\:])';
$g_n_operand='[^\+\-\/\*\|\&\%\?\:]';
$g_quoteseg='(?:"(?:[^"]|[\\\\]")*?"|\'(?:[^\']|[\\\\]\')*?\')';
$g_regseg='\/(?:[^\/]|[\\\\]\/)*?\/[a-z]*';

# }}}

# REGEXPS: VARIABLES: Parsing Config {{{

/*
$html_frametargets:  html list of frame targets to look out for
$hook_html_attrs:    hook html attributes
$hook_js_attrs:      js hook attributes for getting and setting
$hook_js_getattrs:   js hook attributes for getting only
$hook_js_methods:    js hook methods
$js_string_methods:  js methods for the String() object
$js_string_attrs:    js attributes for the String() object
*/

# HTML
$html_frametargets='_(?:top|parent|self)';
$hook_html_attrs='(data|href|src|background|pluginspage|codebase|action)';

# Javascript
/*$hook_js_attrs=
	'(?:href|src|location|action|backgroundImage|pluginspage|codebase|'.
	'location\.href|innerHTML|cookie|search|hostname)';
$hook_js_getattrs=
	"(?:{$hook_js_attrs}|userAgent|platform|appCodeName|appName|appVersion|".
	'language|oscpu|product|productSub|plugins)';*/
$hook_js_methods='(location\.(?:replace|assign))';
// unused? wtf? TODO - figure out why this isn't used and if it should be
//$js_lochost='(location\.host(?:name){0,1})';

$js_string_methods=
	'(?:anchor|big|blink|bold|charAt|charCodeAt|concat|fixed|fontcolor|'.
	'fontsize|fromCharCode|indexOf|italics|lastIndexOf|link|match|replace|'.
	'search|slice|small|split|strike|sub|substr|substring|sup|toLowerCase|'.
	'toUpperCase|toSource|valueOf)';
$js_string_attrs='(?:constructor|length|prototype)';

# }}}

# REGEXPS: VARIABLES: Javascript Expressions Matching {{{

/*
$js_varsect:     flat variable section
$js_jsvarsect:   flat variable section for use in js's parsing engine
$n_js_varsect:   not a javascript variable section
$h_js_exprsect:  helper for js_exprsect
$js_exprsect:    single expression section
$h_js_expr:      helper for js_expr
$js_expr:        any javascript expression
$js_expr2, ...:  $js_expr requires use of a named submatch, so there needs
                 to be multiple versions of $js_expr for use multiple times in
                 one regular expression
*/

$js_varsect=
	"(?:new{$g_plusspace})?[a-zA-Z_\$]".
	"(?:[a-zA-Z0-9\$\._]*[a-zA-Z0-9_])?";
$js_jsvarsect=
	"(?:new{$g_plusspace})?[a-zA-Z_\$]".
	"(?:[a-zA-Z0-9\$\._]*[a-zA-Z0-9_\[\]])?";
$n_js_varsect='[^a-zA-Z\._\[\]]';

$h_js_exprsect="(?:{$g_quoteseg}|{$g_regseg}|{$js_varsect}|[0-9\.]+)";
$js_exprsect="(?:{$h_js_exprsect}|\({$h_js_exprsect}\))";
$h_js_expr=
	"|\[{$g_anyspace}(?:(?P>js_expr)".
		"(?:{$g_anyspace},{$g_anyspace}(?P>js_expr))*{$g_anyspace})?\]".
	"|\({$g_anyspace}(?:(?P>js_expr)".
		"(?:{$g_anyspace},{$g_anyspace}(?P>js_expr))*{$g_anyspace})?\)".
	"|\{{$g_anyspace}(?:(?P>js_expr)".
		"(?:{$g_anyspace},{$g_anyspace}(?P>js_expr))*{$g_anyspace})?\}";
$js_expr=
	"(?P<js_expr>(?:{$js_exprsect}{$h_js_expr})".
	"(?:{$g_anyspace}(?:".
		"\.{$g_anyspace}(?P>js_expr)".
		"|{$g_operand}{$g_anyspace}(?P>js_expr)".
		"|\?{$g_anyspace}(?P>js_expr){$g_anyspace}".
			":{$g_anyspace}(?P>js_expr)".
		$h_js_expr.
	"){$g_anyspace})*)";
$js_expr2=str_replace('js_expr','js_expr2',$js_expr);
$js_expr3=str_replace('js_expr','js_expr3',$js_expr);
$js_expr4=str_replace('js_expr','js_expr4',$js_expr);

# }}}

# REGEXPS: VARIABLES: Miscellaneous {{{

/*
$l_js_end:          looks for if end of javascript statement
$n_l_js_end:        looks for if not end of javascript statement (#)
$js_begin:          matches beginning of javascript statement
$js_beginright:     matches beginning of javascript statement on the RHS
$js_xmlhttpreq:     XMLHttpRequest matching (plus ActiveX version)
$h_html_noquot:     matches an HTML attribute value that is not using quotes
$html_reg:          matches an HTML attribute value
$js_newobj:         matches a 'new' clause inside of Javascript
$html_formnotpost:  matches a form, given it's not of method POST
*/

$l_js_end="(?={$g_justspace}(?:[;\}]|{$g_n_operand}[\n\r]))";
#$n_l_js_end="(?!{$g_justspace}(?:[;\}]|{$g_n_operand}[\n\r]))";
$js_begin=
	"((?:[;\{\}\n\r\(\)]|[\!=]=)(?!{$g_anyspace}(?:#|\/\*|\/\/)){$g_anyspace})";
# TODO - need to get rid of js_beginright or something
# (?<!:[\/])[\/](?![\/]) - this matches a slash ('/') without being a part of
#                          "://"
$js_beginright=
	"((?:[;\{\(=\+\-\*]|[\}\)]{$g_anyspace};{$g_anyspace}|".
	"(?<!:[\/])[\/](?![\/])){$g_justspace})";
#$js_beginright="((?:[;\{\}\(\)=\+\-\*]|(?<!:[\/])[\/](?![\/])){$g_justspace})";

$js_xmlhttpreq=
	"(?:XMLHttpRequest{$g_anyspace}(?:\({$g_anyspace}\)|)|".
	"ActiveXObject{$g_anyspace}\({$g_anyspace}[^\)]+\.XMLHTTP['\"]".
	"{$g_anyspace}\))".
	'(?=;)';

$h_html_noquot='(?:[^"\'\\\\][^> ]*)';
$html_reg="({$g_quoteseg}|{$h_html_noquot})";
$js_newobj="(?:{$g_anyspace}new{$g_plusspace}|{$g_anyspace})";
$html_formnotpost="(?:(?!method{$g_anyspace}={$g_anyspace}(?:'|\")?post)[^>])";

# }}}

# }}}

# REGEXPS: JAVASCRIPT PARSING {{{

$js_regexp_arrays=array(

	#array(1,2,"/({$g_operand}){$g_plusjustspace}[\r\n]+/im",'\1'),

	# object.attribute parsing (get and set)

	# prepare for set for +=
	array(1,2,
		"/{$js_begin}{$js_expr}\.({$js_varsect}){$g_anyspace}\+=/i",
		'\1\2.\3='.COOK_PREF.'.getAttr(\2,/\3/)+'),
	# set for =
	array(1,2,
		"/{$js_begin}{$js_expr}\.(({$js_varsect}){$g_anyspace}=".
			"(?:{$g_anyspace}{$js_expr2}{$g_anyspace}=)*{$g_anyspace})".
			"{$js_expr3}{$l_js_end}/i",
		'\1'.COOK_PREF.'.setAttr(\2,/\4/,\6)'),
	# get
	array(1,2,
		"/{$js_beginright}{$js_expr}\.({$js_varsect})".
			"([^\.=a-z0-9_\[\(\t\r\n]|\.{$js_string_methods}\(|".
			"\.{$js_string_attrs}{$n_js_varsect})/i",
		'\1'.COOK_PREF.'.getAttr(\2,/\3/)\4'),


	# object['attribute'] parsing (get and set)

	# set for +=
	array(1,2,
		"/{$js_begin}{$js_expr}\[{$js_expr2}\]{$g_anyspace}\+=/i",
		'\1\2[\3]='.COOK_PREF.'.getAttr(\2,\3)+'),
	# set for =
	array(1,2,
		"/{$js_begin}{$js_expr}(\[{$js_expr2}\]{$g_anyspace}=".
			"(?:{$g_anyspace}{$js_expr3}{$g_anyspace}=)*{$g_anyspace})".
			"{$js_expr4}{$l_js_end}/i",
		'\1'.COOK_PREF.'.setAttr(\2,\4,\6)'),
	# get
	array(1,2,
		"/{$js_beginright}{$js_expr}\[{$js_expr2}\]".
			"([^\.=a-z0-9_\[\(\t\r\n]|\.{$js_string_methods}\(|".
			"\.{$js_string_attrs}{$n_js_varsect})/i",
		'\1'.COOK_PREF.'.getAttr(\2,\3)\4'),


	# method parsing
	array(1,2,
		"/([^a-z0-9]{$hook_js_methods}{$g_anyspace}\()([^)]*)\)/i",
		'\1'.COOK_PREF.'.surrogafy_url(\3))'),

	# eval parsing
	array(1,2,
		"/([^a-z0-9])eval{$g_anyspace}\(({$g_anyspace}{$js_expr})\)/i",
		'\1eval('.COOK_PREF.'.parse_all_html(\2,"application/x-javascript"))'),

	# action attribute parsing
	array(1,2,
		"/{$js_begin}\.action{$g_anyspace}=/i",
		'\1.'.COOK_PREF.'.value='),

	# object.setAttribute parsing
	array(1,2,
		"/{$js_begin}{$js_expr}\.setAttribute{$g_anyspace}\({$g_anyspace}".
			"{$js_expr2}{$g_anyspace},{$g_anyspace}{$js_expr3}".
			"{$g_anyspace}\)/i",
		'\1'.COOK_PREF.'.setAttr(\2,\3,\4)'),

	# XMLHttpRequest parsing
	array(1,2,
		"/{$js_begin}([^\ {>\t\r\n=;]+{$g_anyspace}=)".
		"({$js_newobj}{$js_xmlhttpreq})/i",
		'\1\2'.COOK_PREF.'.XMLHttpRequest_wrap(\3)'),

	# XMLHttpRequest in return statement parsing
	array(1,2,
		"/{$js_begin}(return{$g_plusspace})({$js_newobj}{$js_xmlhttpreq})/i",
		'\1\2'.COOK_PREF.'.XMLHttpRequest_wrap(\3)'),

	# form.submit() call parsing
	($OPTIONS['ENCRYPT_URLS']?array(1,2,
		"/{$js_begin}((?:[^\) \{\}]*(?:\)\.{0,1}))+)(\.submit{$g_anyspace}\(\)".
			"){$l_js_end}/i",
		'\1void((\2.method=="post"?null:\2\3));')
	:null),
);

# }}}

# REGEXPS: HTML/CSS PARSING {{{

$regexp_arrays=array(
	'text/html' => array(
		# target attr
		(PAGETYPE_ID===PAGETYPE_FRAMED_PAGE?array(1,1,
			"/(<[a-z][^>]*{$g_anyspace}) target{$g_anyspace}={$g_anyspace}".
				"(?:{$html_frametargets}|('){$html_frametargets}'|(\")".
				"{$html_frametargets}\")".
				"/i",
			'\1')
		:null),
		(PAGETYPE_ID===PAGETYPE_FRAMED_CHILD?array(1,1,
			"/(<[a-z][^>]*{$g_anyspace} target{$g_anyspace}={$g_anyspace})".
				"(?:_top|(')_top'|(\")_top\")/i",
			'\1\2\3'.COOK_PREF.'_top\2\3')
		:null),

		# deal with <form>s
		array(1,1,
			"/(<form{$html_formnotpost}*?)".
				"(?:{$g_plusspace}action{$g_anyspace}={$g_anyspace}{$html_reg}".
				")({$html_formnotpost}*)>/i",
			'\1\3><input type="hidden" name="" class="'.COOK_PREF.'" value=\2'.
			' />'),
		array(2,1,
			"/<input type=\"hidden\" name=\"\" class=\"".COOK_PREF."\"".
				" value{$g_anyspace}={$g_anyspace}{$html_reg} \/>/i",
			1,false),
		array(1,1,
			'/(<form[^>]*?)>/i',
			'\1 target="_self"'.
				($OPTIONS['ENCRYPT_URLS']?
				 ' onsubmit="return '.COOK_PREF.'.form_encrypt(this);">':'>')),
		array(1,1,
			"/(<form{$html_formnotpost}+)>(?!<!--".COOK_PREF.'-->)/i',
			'\1 target="_parent"><!--'.COOK_PREF.
				'--><input type="hidden" name="" value="_">'),

		# deal with the form button for encrypted URLs
		($OPTIONS['ENCRYPT_URLS']?array(1,1,
			"/(<input[^>]*? type{$g_anyspace}={$g_anyspace}".
				"(?:\"submit\"|'submit'|submit)[^>]*?[^\/])((?:[ ]?[\/])?>)/i",
			'\1 onclick="'.COOK_PREF.'_form_button=this.name;"\2')
		:null),

		# parse all the other tags
		array(2,1,
			"/<[a-z][^>]*{$g_plusspace}{$hook_html_attrs}{$g_anyspace}=".
				"{$g_anyspace}{$html_reg}/i",
			2),
		array(2,1,
			"/<param[^>]*{$g_plusspace}name{$g_anyspace}={$g_anyspace}[\"']?".
				"movie[^>]*{$g_plusspace}value{$g_anyspace}={$g_anyspace}".
				"{$html_reg}/i",
			1),
		array(2,2,
			"/<script[^>]*?{$g_plusspace}src{$g_anyspace}={$g_anyspace}([\"'])".
				"{$g_anyspace}(.*?[^\\\\])\\1[^>]*>{$g_anyspace}<\/script>/i",
			2),
		($OPTIONS['URL_FORM'] && PAGE_FRAMED?array(2,1,
			"/<a(?:rea)?{$g_plusspace}[^>]*href{$g_anyspace}={$g_anyspace}".
				"{$html_reg}/i",
			1,false,NEW_PAGETYPE_FRAME_TOP)
		:null),
		($OPTIONS['URL_FORM'] && PAGE_FRAMED?array(2,1,
			"/<[i]?frame{$g_plusspace}[^>]*src{$g_anyspace}={$g_anyspace}".
			"{$html_reg}/i",
			1,false,PAGETYPE_FRAMED_CHILD)
		:null)
	),

	'text/css' => array(
		array(2,1,
			"/[^a-z]url\({$g_anyspace}(&(?:quot|#(?:3[49]));|\"|')(.*?[^\\\\])".
				"(\\1){$g_anyspace}\)/i",
			2),
		array(2,1,
			"/[^a-z]url\({$g_anyspace}((?!&(?:quot|#(?:3[49]));)[^\"'\\\\].*?".
				"[^\\\\]){$g_anyspace}\)/i",
			1),
		array(2,1,
			"/@import{$g_plusspace}(&(?:quot|#(?:3[49]));|\"|')(.*?[^\\\\])".
				"(\\1);/i",
			2)
	),

	'application/x-javascript' => $js_regexp_arrays,
	'text/javascript' => $js_regexp_arrays
);

# }}}

# REGEXPS: STATIC JAVASCRIPT REGEXPS PAGE {{{

if(QUERY_STRING=='js_regexps' || QUERY_STRING=='js_regexps_framed'){
	static_cache();
?>//<script type="text/javascript">
<?php echo(
	convertarray_to_javascript().
	(
		$OPTIONS['REMOVE_OBJECTS']?
		'regexp_arrays["text/html"].push(Array(1,/<[\\\\/]?'.
			'(embed|param|object)[^>]*>/ig,""));':
		null
	)
); ?>
//</script><?php exit(); }

# }}}

# REGEXPS: SERVER-SIDE ONLY PARSING {{{

array_push($regexp_arrays['text/html'],
	array(2,1,
		"/<meta[^>]*{$g_plusspace}http-equiv{$g_anyspace}={$g_anyspace}".
		"([\"']|)refresh\\1[^>]* content{$g_anyspace}={$g_anyspace}([\"']|)".
		"[ 0-9\.;\t\\r\n]*url=(.*?)\\2[^>]*>/i",
		3,true,NEW_PAGETYPE_FRAMED_PAGE),
	array(1,1,
		"/(<meta[^>]*{$g_plusspace}http-equiv{$g_anyspace}={$g_anyspace}".
		"([\"']|)set-cookie\\2[^>]* content{$g_anyspace}={$g_anyspace})([\"'])".
		"(.*?[^\\\\]){$g_anyspace}\\3/i",
		'\1\3'.PAGECOOK_PREFIX.'\4\3')
);

# }}}

# REGEXPS: CLEANUP {{{

# needed later, but $g_anyspace and $html_reg are unset below
define('BASE_REGEXP',
	"<base[^>]* href{$g_anyspace}={$g_anyspace}{$html_reg}[^>]*>");
define('END_OF_SCRIPT_TAG',
	"(?:{$g_anyspace}(?:\/\/)?{$g_anyspace}-->{$g_anyspace})?<\/script>");
define('REGEXP_SCRIPT_ONEVENT',
	"( on[a-z]{3,20}=(?:\"[^\"]+\"|'[^']+'|[^\"' >][^ >]+[^\"' >])|".
	" href=(?:\"{$g_anyspace}javascript:[^\"]+\"|".
	"'{$g_anyspace}javascript:[^']+'|".
	"{$g_anyspace}javascript:[^\"' >][^ >]+[^\"' >]))");

unset(
	$g_justspace, $g_plusjustspace, $g_anyspace, $g_plusspace, $g_operand,
	$g_n_operand, $g_quoteseg, $g_regseg,

	$hook_html_attrs, $html_frametargets, $hook_js_attrs, $hook_js_getattrs,
	$hook_js_methods, $js_string_methods, $js_string_attrs,

	$js_varsect, $js_jsvarsect, $n_js_varsect, $h_js_exprsect, $js_exprsect,
	$js_expr, $js_expr2, $js_expr3, $js_expr4,

	$l_js_end, $n_l_js_end, $js_begin, $js_beginright, $js_xmlhttpreq,
	$h_html_noquot, $html_reg, $js_newobj, $html_formnotpost,

	$js_regexp_arrays
);

# }}}

# }}}

# PROXY FUNCTIONS {{{

# PROXY FUNCTIONS: AURL CLASS {{{

# class for URL
class aurl{
	var $url,$topurl,$locked,$force_unlocked;
	var $proto,$userpass,$servername,$portval,$path,$file,$query,$label;

	function aurl($url,$topurl=null,$force_unlocked=false){
		global $CONFIG;
		if(strlen($url)>$CONFIG['MAXIMUM_URL_LENGTH']) $this->url=null;
		else $this->url=
			preg_replace('/&#([0-9]+);/e','chr(\1)',
			trim(str_replace('&amp;','&',
			str_replace(chr(13),null,
			str_replace(chr(10),null,
			$url)))));
		$this->topurl=$topurl;

		$this->force_unlocked=$force_unlocked;
		$this->determine_locked();
		if($this->locked) return;

		$urlwasvalid=true;
		if(!preg_match(URLREG,$this->url)){
			$urlwasvalid=false;
			if($this->topurl==null) $this->url=
				'http://'.
				(
					$this->url{0}==':' || $this->url{0}=='/'?
					substr($this->url,1):
					$this->url
				).
				(strpos($this->url,'/')!==false?null:'/');
			else{
				$newurl=
					$this->topurl->get_proto().
					$this->get_fieldreq(2,$this->topurl->get_userpass()).
					$this->topurl->get_servername().
					(($this->topurl->get_portval()!=80 && (
						$this->topurl->get_proto()=='https'?
						$this->topurl->get_portval()!=443:
						true
					))?':'.$this->topurl->get_portval():null);
				if($this->url{0}!='/') $newurl.=$this->topurl->get_path();
				$this->url=$newurl.$this->url;
			}
		}

		$this->set_proto((
			$urlwasvalid || $this->topurl==null?
			preg_replace('/^([^:\/]*).*$/','\1',$this->url):
			$this->topurl->get_proto()
		));
		$this->set_userpass(preg_replace(URLREG,'\2',$this->url));
		$this->set_servername(preg_replace(URLREG,'\3',$this->url));
		$this->set_portval(preg_replace(URLREG,'\4',$this->url));
		$this->set_path(preg_replace(URLREG,'\5',$this->url));
		$this->set_file(preg_replace(URLREG,'\6',$this->url));
		$this->set_query(preg_replace(URLREG,'\7',$this->url));
		$this->set_label(preg_replace(URLREG,'\8',$this->url));

		if(!$this->locked && !preg_match(URLREG,$this->url))
			havok(7,$this->url); #*
	}

	function determine_locked(){
		if($this->force_unlocked)
			$this->locked=false;
		else $this->locked=preg_match(AURL_LOCK_REGEXP,$this->url)>0;
	} #*

	function get_fieldreq($fieldno,$value){
		$fieldreqs=array(
			2 => '://'.($value!=null?"$value@":null),
			4 => ($value!=null && intval($value)!=80?':'.intval($value):null),
			7 => ($value!=null?"?$value":null),
			8 => ($value!=null?"#$value":null));
		if(!array_key_exists($fieldno,$fieldreqs))
			return (empty($value)?null:$value);
		else return $fieldreqs[$fieldno];
	}

	function set_proto($proto=''){
		if($this->locked) return;
		$this->proto=(!empty($proto)?$proto:'http');
	}
	function get_proto(){ return $this->proto; }
	function get_userpass(){ return $this->userpass; }
	function set_userpass($userpass=null){ $this->userpass=$userpass; }
	function get_servername(){ return $this->servername; }
	function set_servername($servername=null){ $this->servername=$servername; }
	function get_portval(){
		return (
			empty($this->portval)?
			($this->get_proto()=='https'?'443':'80'):
			$this->portval
		);
	}
	function set_portval($port=null){
		$this->portval=strval((intval($port)!=80)?$port:null);
	}
	function get_path(){
		if(strpos($this->path,'/../')!==false)
			$this->path=
				preg_replace('/(?:\/[^\/]+){0,1}\/\.\.\//','/',$this->path);
		if(strpos($this->path,'/./')!==false)
			while(
				($path=str_replace('/./','/',$this->path)) &&
				$path!=$this->path
			) $this->path=$path;
		return $this->path;
	}
	function set_path($path=null){ $this->path=(empty($path)?'/':$path); }
	function get_file(){ return $this->file; }
	function set_file($file=null){ $this->file=$file; }
	function get_query(){ return $this->query; }
	function set_query($query=null){ $this->query=$query; }
	function get_label(){ return $this->label; }
	function set_label($label=null){ $this->label=$label; }

	function get_url($withlabel=true){
		if($this->locked) return $this->url;
		return
			$this->get_proto().'://'.
			($this->get_userpass()==null?null:$this->get_userpass().'@').
			$this->get_servername().
			(
				(
					$this->get_proto()=='https' &&
					intval($this->get_portval())==443
				) || intval($this->get_portval())==80?
				null:
				':'.intval($this->get_portval())
			).
			$this->get_path().$this->get_file().
			($this->get_query()==null?null:'?'.$this->get_query()).
			(
				$withlabel && $this->get_label()==null?
				null:
				'#'.$this->get_label()
			);
	}

	function surrogafy(){
		global $OPTIONS;
		$label=$this->get_label();
		$this->set_label();
		$url=$this->get_url();
		$this->set_label($label);

		#$this->determine_locked();
		if($this->locked) return $url;

		if($OPTIONS['ENCRYPT_URLS'] && !$this->locked) $url=proxenc($url);
		$url=THIS_SCRIPT."?={$url}".(!empty($label)?"#$label":null);
		return $url;
	}
} 

# }}}

# PROXY FUNCTIONS: URL PARSING {{{
function surrogafy_url($url,$topurl=false,$addproxy=true){
	global $curr_urlobj;
	//if(preg_match('/^(["\']).*\1$/is',$url)>0){
	if(
		($url{0}=='"' && substr($url,-1)=='"') ||
		($url{0}=='\'' && substr($url,-1)=='\'')
	){
		$urlquote=$url{0};
		$url=substr($url,1,strlen($url)-2);
	}
	if($topurl===false) $topurl=$curr_urlobj;
	$urlobj=new aurl($url,$topurl);
	$new_url=($addproxy?$urlobj->surrogafy():$urlobj->get_url());
	if(!empty($urlquote)) $new_url="{$urlquote}{$new_url}{$urlquote}";
	return $new_url;
}

function framify_url($url,$frame_type=false){
	global $OPTIONS;
/*	if(
		($frame_type!==PAGETYPE_FRAME_TOP || !$OPTIONS['URL_FORM']) &&
		($frame_type!==PAGETYPE_FRAMED_PAGE && !PAGE_FRAMED)
	) return $url;*/
	if($frame_type===PAGETYPE_NULL) return $url;
	//if(preg_match('/^(["\']).*\1$/is',$url)>0){
	if(
		($url{0}=='"' && substr($url,-1)=='"') ||
		($url{0}=='\'' && substr($url,-1)=='\'')
	){
		$urlquote=$url{0};
		$url=substr($url,1,strlen($url)-2);
	}
	if(preg_match(FRAME_LOCK_REGEXP,$url)<=0){
		if($frame_type===PAGETYPE_FRAME_TOP) # && $OPTIONS['URL_FORM'])
			$query='&=';
		elseif($frame_type===PAGETYPE_FRAMED_CHILD) $query='.&=';
		elseif($frame_type===PAGETYPE_FRAMED_PAGE || PAGE_FRAMED) $query='_&=';
		else $query=null;
		$url=preg_replace(
			'/^([^\?]*)[\?]?'.PAGETYPE_MINIREGEXP.'([^#]*?[#]?.*?)$/',
			"\\1?={$query}\\3",$url,1);
	}
	if(!empty($urlquote)) $url="{$urlquote}{$url}{$urlquote}";
	return $url;
}

function proxenc($url){
	if($url{0}=='~' || strtolower(substr($url,0,3))=='%7e') return $url;
	$url=urlencode($url);
	$new_url=null;
	for($i=0;$i<strlen($url);$i++){
		$char=ord($url{$i});
		$char+=ord(substr(SESS_PREF,$i%strlen(SESS_PREF),1));
		while($char>126) $char-=94;
		$new_url.=chr($char);
	}
	#return '~'.base64_encode($new_url);
	return '~'.urlencode(base64_encode($new_url));
}

# }}}

# PROXY FUNCTIONS: ERRORS & EXITING {{{

function finish_noexit(){
	global $dns_cache_array;
	# save DNS Cache before exiting
	$_SESSION['DNS_CACHE_ARRAY']=$dns_cache_array;
}

function finish(){
	finish_noexit();
	exit();
}

function havok($errorno,$arg1=null,$arg2=null,$arg3=null){
	global $curr_url;
	$url=$curr_url;
	switch($errorno){
		case 1:
			$et='Bad IP Address';
			$ed=
				"The IP address given ({$arg2}) is an impossible IP address, ".
				"or the domain given ({$arg1}) was resolved to an impossible ".
				'IP address.';
			break;
		case 2:
			$et='Address is Blocked';
			$ed=
				'The administrator of this proxy service has decided to '.
				"block this address, domain, or subnet.\n<br /><br />\n".
				"Domain: {$arg1}\n<br />\nAddress: {$arg2}";
			break;
		case 3:
			$et='Could Not Resolve Domain';
			$ed=
				"The domain of the URL given ({$arg1}) could not be resolved ".
				'due to DNS issues or an errorneous domain name.';
			break;
		case 4:
			$et='Bad Filters';
			$ed=
				'The administrator of this proxy has incorrectly configured '.
				'his domain filters, or a domain given could not be resolved.';
			break;
		case 5:
			$et='Domain is Blocked';
			$ed=
				'The administrator of this proxy has decided to block this '.
				'domain.';
			break;
		case 6:
			$et='Could Not Connect to Server';
			$ed=
				'An error has occurred while attempting to connect to '.
				"\"{$arg1}\" on port \"{$arg2}\".";
			break;
		case 7:
			$et='Invalid URL';
			$ed='The URL below was detected to be an invalid URL.';
			$url=$arg1;
			break;
		case 8:
			$et='Trying to A Secure Page Through Insecure Connection';
			$ed=
				'The site you are trying to access is secured by SSL, however '.
				'you are accessing this proxy through an insecure connection. '.
				'Please realize that any information you pass to this site is '.
				'going to be transmitted on an insecure connection, with the '.
				'potential of being intercepted.'.
				'<br /><br />'.
				"Domain to unlock: {$arg1}".
				'<br /><br />'.
				'If you wish to allow secure connections to this domain for '.
				'this session, press continue below.  Otherwise, hit back.'.
				'<br /><br />'.
				'<input type="button" value="Back" style="float: left"'.
				' onclick="history.go(-1);" />'.
				'<input type="button" value="Continue" style="float: right"'.
				' onclick="'.
					'var ifrm=document.createElement(\'iframe\');'.
					'ifrm.onload=function(){ location.reload(true); };'.
					'ifrm.src=\''.THIS_SCRIPT.'?'.COOK_PREF.'_ssl_domain='.
					"{$arg1}';".
					'ifrm.style.height=\'0px\';'.
					'ifrm.style.width=\'0px\';'.
					'ifrm.style.border=\'0px\';'.
					'var body=document.getElementsByTagName(\'body\')[0];'.
					'body.appendChild(ifrm);'.
				'" />'.
				'<br />';
			break;
	}
	$ed.="\n<br /><br />\nURL:&nbsp;{$url}";
?>
<div style="font-family: bitstream vera sans, trebuchet ms">
<div style="border: 3px solid #FFFFFF; padding: 2px">
	<div style="
		float: left; border: 1px solid #602020; padding: 1px;
		background-color: #FFFFFF">
		<div style="
			float: left; background-color: #801010; color: #FFFFFF;
			font-weight: bold; font-size: 54px; padding: 2px;
			padding-left: 12px; padding-right: 12px"
		>!</div>
	</div>
	<div style="float: left; width: 500px; padding-left: 20px">
		<div style="
			border-bottom: 1px solid #000000; font-size: 12pt;
			text-align: center; font-weight: bold; padding: 2px"
		>Error: <?php echo($et); ?></div>
		<div style="padding: 6px"><?php echo($ed); ?></div>
	</div>
</div></div>
<?php finish(); }

# }}}

# PROXY FUNCTIONS: TCP/IP {{{

function ipbitter($ipaddr){
	$ipsplit=explode('.',$ipaddr);
	for($i=0;$i<4;$i++){
		$ipsplit[$i]=decbin($ipsplit[$i]);
		$ipsplit[$i]=str_repeat('0',8-strlen($ipsplit[$i])).$ipsplit[$i];
	}
	return implode(null,$ipsplit);
}

function ipcompare($iprange,$ip){
	$iprarr=explode('/',$iprange);
	$ipaddr=$iprarr[0];
	$mask=$iprarr[1];
	$maskbits=str_repeat('1',$mask).str_repeat('0',$mask);
	$ipbits=ipbitter($ipaddr);
	$ipbits2=ipbitter($ip);
	return (($ipbits & $maskbits)==($ipbits2 & $maskbits));
}

function ip_check($ip,$mask=false){
	$ipseg='(?:[01]?[0-9]{1,2}|2(?:5[0-5]|[0-4][0-9]))';
	return preg_match("/^(?:$ipseg\.){0,3}$ipseg".($mask?'\/[0-9]{1,2}':null).
		'$/i',$ip); #*
}

function gethostbyname_cacheit($address){
	global $dns_cache_array;
	$ipaddr=gethostbyname($address);
	$dns_cache_array[$address]=array('time'=>time(), 'ipaddr'=>$ipaddr);
	return $ipaddr;
}

function gethostbyname_cached($address){
	global $dns_cache_array;
	if(isset($dns_cache_array[$address]))
		return $dns_cache_array[$address]['ipaddr'];
	return gethostbyname_cacheit($address);
}

function get_check($address){
	global $CONFIG;
	if(strrchr($address,'/')) $address=substr(strrchr($address,'/'),1);
	$ipc=ip_check($address);
	$addressip=(ip_check($address)?$address:gethostbyname_cached($address));
	if(!ip_check($addressip)) havok(1,$address,$addressip);
	foreach($CONFIG['BLOCKED_ADDRESSES'] as $badd){
		if(!$ipc)
			if(
				strlen($badd)<=strlen($address) &&
				substr($address,strlen($address)-strlen($badd),
					strlen($badd))==$badd
			) havok(5);
		if($badd==$addressip) havok(2,$address,$addressip);
		elseif(ip_check($badd,true)){
			if(ipcompare($badd,$addressip)) havok(2,$address,$addressip);
		}
		else{
			$baddip=gethostbyname_cached($badd);
			if(empty($baddip)) havok(4);
			if($baddip==$addressip) havok(2,$address,$addressip);
		}
	}
	return $addressip;
}

# }}}

# PROXY FUNCTIONS: HTTP {{{

function httpclean($str){
	return str_replace(' ','+',
		preg_replace('/([^":\-_\.0-9a-z ])/ie',
			'\'%\'.(strlen(dechex(ord(\'\1\')))==1?\'0\':null).'.
			'strtoupper(dechex(ord(\'\1\')))',
		$str));
}

function getpage($url){
	global $CONFIG,$OPTIONS,$headers,$out,$proxy_variables,$referer;

	# Generate HTTP packet content {{{

	$content=null;
	$is_formdata=substr($_SERVER['CONTENT_TYPE'],0,19)=='multipart/form-data';

	# Generate for multipart & handle file uploads {{{

	if($is_formdata){
		$strnum=null;
		for($i=0; $i<29; $i++) $strnum.=rand(0,9);
		$boundary="---------------------------{$strnum}";

		# parse POST variables
		while(list($key,$val)=each($_POST)){
			if(!is_array($val)){
				$content.=
					"--{$boundary}\r\n".
					"Content-Disposition: form-data; name=\"{$key}\"\r\n".
					"\r\n{$val}\r\n";
			}
			else{
				while(list($key2,$val2)=each($val)){
					$content.=
						"--{$boundary}\r\n".
						"Content-Disposition: form-data; name=\"{$key}[]\"\r\n".
						"\r\n{$val2}\r\n";
				}
			}
		}

		# parse uploaded files
		while(list($key,$val)=each($_FILES)){
			if(!is_array($val['name'])){
				$fcont=file_get_contents($val['tmp_name']);
				@unlink($val['tmp_name']);
				$content.=
					"--{$boundary}\r\n".
					"Content-Disposition: form-data; name=\"{$key}\"; ".
						"filename=\"{$val['name']}\"\r\n".
					"Content-Type: {$val['type']}\r\n".
					"\r\n{$fcont}\r\n";
			}
			else{
				for($i=0; $i<count($val['name']); $i++){
					$fcont=file_get_contents($val['tmp_name'][$i]);
					@unlink($val['tmp_name'][$i]);
					$content.=
						"--{$boundary}\r\n".
						"Content-Disposition: form-data; name=\"{$key}[]\"; ".
							"filename=\"{$val['name'][$i]}\"\r\n".
						"Content-Type: {$val['type'][$i]}\r\n".
						"\r\n{$fcont}\r\n";
				}
			}
		}
		$content.="--{$boundary}--\r\n";
	}

	# }}}

	# Generate for standard POST {{{

	else{
		$postkeys=array_keys($_POST);
		foreach($postkeys as $postkey){
			if(!in_array($postkey,$proxy_variables)){
				if(!is_array($_POST[$postkey]))
					$content.=
						($content!=null?'&':null).
						httpclean($postkey).'='.httpclean($_POST[$postkey]);
				else{
					foreach($_POST[$postkey] as $postval)
						$content.=
							($content!=null?'&':null).
							httpclean($postkey).'%5B%5D='.httpclean($postval);
				}
			}
		}
	}

	# }}}

	# }}}

	# URL setup {{{

	$urlobj=new aurl($url);

	# don't access SSL sites unless the proxy is being accessed through SSL too
	if(
		$urlobj->get_proto()=='https' && $CONFIG['PROTO']!='https' &&
		(
			!is_array($_SESSION['ssl_domains']) ||
			(
				is_array($_SESSION['ssl_domains']) &&
				!in_array($urlobj->get_servername(),$_SESSION['ssl_domains'])
			)
		)
	) havok(8,$urlobj->get_servername());

	# get request URL
	$query=$urlobj->get_query();
	$requrl=
		$urlobj->get_path().
		$urlobj->get_file().
		(!empty($query)?"?{$query}":null);

	# }}}

	# HTTP Authorization and Cache stuff {{{
	$http_auth=null;
	if(extension_loaded('apache')){
		$fail=false;
		$cheaders=getallheaders();
		$http_auth=$reqarray['Authorization'];
	}
	else $fail=true;

	$authorization=
		($fail?$_SERVER['HTTP_AUTHORIZATION']:$cheaders['Authorization']);
	$cache_control=
		($fail?$_SERVER['HTTP_CACHE_CONTROL']:$cheaders['Cache-Control']);
	$if_modified=
		($fail?$_SERVER['HTTP_IF_MODIFIED_SINCE']:
		 $cheaders['If-Modified-Since']);
	$if_none_match=
		($fail?$_SERVER['HTTP_IF_NONE_MATCH']:$cheaders['If-None-Match']);

	if($fail){
		if(!empty($authorization)) $http_auth=$authorization;
		elseif(
			!empty($_SERVER['PHP_AUTH_USER']) &&
			!empty($_SERVER['PHP_AUTH_PW'])
		) $http_auth=
			'Basic '.
			base64_encode(
				"{$_SERVER['PHP_AUTH_USER']}:{$_SERVER['PHP_AUTH_PW']}");
		elseif(!empty($_SERVER['PHP_AUTH_DIGEST']))
			$http_auth="Digest {$_SERVER['PHP_AUTH_DIGEST']}";
	}
	# }}}

	# HTTP packet construction {{{

	# figure out what we are connecting to
	if($OPTIONS['TUNNEL_IP']!=null && $OPTIONS['TUNNEL_PORT']!=null){
		$servername=$OPTIONS['TUNNEL_IP'];
		$ipaddress=get_check($servername);
		$portval=$OPTIONS['TUNNEL_PORT'];
		$requrl=$urlobj->get_url(false);
	}
	else{
		$servername=$urlobj->get_servername();
		$ipaddress=
			(
				$urlobj->get_proto()=='ssl' || $urlobj->get_proto()=='https'?
				'ssl://':
				null
			).
			get_check($servername);
		$portval=$urlobj->get_portval();
	}

	# begin packet construction
	$out=
		($content==null?'GET':'POST').' '.
			str_replace(' ','%20',$requrl)." HTTP/1.1\r\n".
		"Host: ".$urlobj->get_servername().
			(
				($portval!=80 && (
					$urlobj->get_proto()=='https'?$portval!=443:true
				))?
				":$portval":
				null
			)."\r\n";

	# user agent and auth headers
	global $useragent;
	$useragent=null;
	if($OPTIONS['USER_AGENT']!='-1'){
		$useragent=$OPTIONS['USER_AGENT'];
		if(empty($useragent)) $useragent=$_SERVER['HTTP_USER_AGENT'];
		if(!empty($useragent)) $out.="User-Agent: $useragent\r\n";
	}
	if(!empty($http_auth)) $out.="Authorization: $http_auth\r\n";

	# referer headers
	if(!$OPTIONS['REMOVE_REFERER'] && !empty($referer))
		$out.='Referer: '.str_replace(' ','+',$referer)."\r\n";

	# POST headers
	if($content!=null)
		$out.=
			'Content-Length: '.strlen($content)."\r\n".
			'Content-Type: '.
				(
					$is_formdata?
					"multipart/form-data; boundary={$boundary}":
					'application/x-www-form-urlencoded'
				)."\r\n";

	# cookie headers
	$cook_prefdomain=
		preg_replace('/^www\./i',null,$urlobj->get_servername()); #*
	$cook_prefix=str_replace('.','_',$cook_prefdomain).COOKIE_SEPARATOR;
	if(!$OPTIONS['REMOVE_COOKIES'] && count($_COOKIE)>0){
		$addtoout=null;
		reset($_COOKIE);
		while(list($key,$val)=each($_COOKIE)){
			if(
				$key{0}!='~' && strtolower(substr($key,0,3))!='%7e' &&
				str_replace(COOKIE_SEPARATOR,null,$key)==$key
			) continue;
			if($OPTIONS['ENCRYPT_COOKIES']){
				$key=proxdec($key);
				$val=proxdec($val);
			}
			$cook_domain=
				substr($key,0,strpos($key,COOKIE_SEPARATOR)).COOKIE_SEPARATOR;
			if(
				substr($cook_prefix,strlen($cook_prefix)-strlen($cook_domain),
					strlen($cook_domain))!=$cook_domain
			) continue;
			$key=
				substr($key,strlen($cook_domain),
					strlen($key)-strlen($cook_domain));
			if(!in_array($key,$proxy_variables)) $addtoout.=" $key=$val;";
		}
		if(!empty($addtoout)){
			$addtoout.="\r\n";
			$out.="Cookie:{$addtoout}";
		}
	}

	# final packet headers and content
	$out.=
		"Accept: */*;q=0.1\r\n".
		($CONFIG['GZIP_PROXY_SERVER']?"Accept-Encoding: gzip\r\n":null).
		//"Accept-Charset: ISO-8859-1,utf-8;q=0.1,*;q=0.1\r\n".
		/*/
		"Keep-Alive: 300\r\n".
		"Connection: keep-alive\r\n".                          /*/
		"Connection: close\r\n".                               //*/
		($cache_control!=null?"Cache-Control: $cache_control\r\n":null).
		($if_modified!=null?"If-Modified-Since: $if_modified\r\n":null).
		($if_none_match!=null?"If-None-Match: $if_none_match\r\n":null).
		"\r\n{$content}";

	# }}}

	# Ignore SSL errors {{{

	# This part ignores any "SSL: fatal protocol error" errors, and makes sure
	# other errors are still triggered correctly
	function errorHandle($errno,$errmsg){
		if(
			$errno<=E_PARSE && (
				$errno!=E_WARNING ||
				substr($errmsg,-25)!='SSL: fatal protocol error'
			)
		){
			restore_error_handler();
			trigger_error($errmsg,$errno<<8);
			set_error_handler('errorHandle');
		}
	}
	set_error_handler('errorHandle');

	# }}}

	# Send HTTP Packet {{{

	$fp=@fsockopen($ipaddress,$portval,$errno,$errval,5)
	    or havok(6,$servername,$portval);
	stream_set_timeout($fp,5);
	# for persistent connections, this may be necessary
	/*
	$ub=stream_get_meta_data($fp);
	$ub=$ub['unread_bytes'];
	if($ub>0) fread($fp,$ub);
	*/
	fwrite($fp,$out);

	# }}}

	# Retrieve and Parse response headers {{{

	$response='100';
	while($response=='100'){
		$responseline=fgets($fp,8192);
		$response=substr($responseline,9,3);

		$headers=array();
		while($curline!="\r\n" && $curline=fgets($fp,8192)){
			$harr=explode(':',$curline,2);
			$headers[strtolower($harr[0])][]=trim($harr[1]);
		}
	}

	//if($headers['pragma'][0]==null) header('Pragma: public');
	//if($headers['cache-control'][0]==null) header('Cache-Control: public');
	//if($headers['last-modified'][0]==null && $headers['expires']==null)
	//	header('Expires: '.date('D, d M Y H:i:s e',time()+86400));

	# read and store cookies
	if(!$OPTIONS['REMOVE_COOKIES']){
		for($i=0;$i<count($headers['set-cookie']);$i++){
			$thiscook=explode('=',$headers['set-cookie'][$i],2);
			if(!strpos($thiscook[1],';')) $thiscook[1].=';';
			$cook_val=substr($thiscook[1],0,strpos($thiscook[1],';'));
			$cook_domain=
				preg_replace('/^.*domain=[	 ]*\.?([^;]+).*?$/i','\1',
					$thiscook[1]); #*
			if($cook_domain==$thiscook[1]) $cook_domain=$cook_prefdomain;
			elseif(
				substr($cook_prefdomain,
					strlen($cook_prefdomain)-strlen($cook_domain),
					strlen($cook_domain))!=$cook_domain
			) continue;
			$cook_name=
				str_replace('.','_',$cook_domain).COOKIE_SEPARATOR.$thiscook[0];
			if($OPTIONS['ENCRYPT_COOKIES']){
				$cook_name=proxenc($cook_name);
				$cook_val=proxenc($cook_val);
			}
			dosetcookie($cook_name,$cook_val);
		}
	}

	# page redirected, send it back to the user
	if($response{0}=='3' && $response{1}=='0' && $response{2}!='4'){
		$urlobj=new aurl($url);
		$redirurl=framify_url(
			surrogafy_url($headers['location'][0],$urlobj),
			NEW_PAGETYPE_FRAMED_PAGE
		);

		fclose($fp);
		restore_error_handler();

		finish_noexit();
		header("Location: {$redirurl}");
		exit();
	}

	# parse the rest of the headers
	$oheaders=$headers;
	$oheaders['location']=$oheaders['content-length']=
		$oheaders['content-encoding']=$oheaders['set-cookie']=
		$oheaders['transfer-encoding']=$oheaders['connection']=
		$oheaders['keep-alive']=$oheaders['pragma']=$oheaders['cache-control']=
		$oheaders['expires']=null;

	while(list($key,$val)=each($oheaders))
		if(!empty($val[0])) header("{$key}: {$val[0]}");
	unset($oheaders);
	header("Status: {$response}");

	# }}}

	# Retrieve content {{{

	if(
		substr($headers['content-type'][0],0,4)=='text' ||
		substr($headers['content-type'][0],0,24)=='application/x-javascript'
	){
		$justoutput=false;
		$justoutputnow=false;
	}
	else{
		$justoutputnow=($headers['content-encoding'][0]=='gzip'?false:true);
		$justoutput=true;
	}

	# Transfer-Encoding: chunked
	if($headers['transfer-encoding'][0]=='chunked'){
		$body=null;
		$chunksize=null;
		while($chunksize!==0){
			$chunksize=intval(fgets($fp,8192),16);
			$bufsize=$chunksize;
			while($bufsize>=1){
				$chunk=fread($fp,$bufsize);
				if($justoutputnow) echo $chunk;
				else $body.=$chunk;
				$bufsize-=strlen($chunk);
			}
			fread($fp,2);
		}
	}

	# Content-Length stuff - commented for even more chocolatey goodness
	# Some servers really botch this up it seems...
	/*elseif($headers['content-length'][0]!=null){
		$conlen=$headers['content-length'][0];
		$body=null;
		for($i=0;$i<$conlen;$i+=$read){
			$read=($conlen-$i<8192?$conlen-$i:8192);
			$byte=fread($fp,$read);
			if($justoutputnow) echo $byte;
			else $body.=$byte;
		}
	}*/

	# Generic stream getter
	else{
		if(function_exists('stream_get_contents')){
			if($justoutputnow) echo stream_get_contents($fp);
			else $body=stream_get_contents($fp);
		}
		else{
			$body=null;
			while(true){
				$chunk=fread($fp,8192);
				if(empty($chunk)) break;
				if($justoutputnow) echo $chunk;
				else $body.=$chunk;
			}
		}
	}

	fclose($fp);
	restore_error_handler();

	# }}}

	# GZIP, output, and return {{{

	if($CONFIG['GZIP_PROXY_SERVER'] && $headers['content-encoding'][0]=='gzip')
		$body=gzinflate(substr($body,10));
	if($justoutput){
		if(!$justoutputnow) echo $body;
		finish();
	}

	return array($body,$url,$cook_prefix);

	# }}}

}

# }}}

# }}}

# PROXY EXECUTION {{{

# PROXY EXECUTION: COOKIE VARIABLES {{{

global $proxy_variables;
$proxy_variables=array(
	'user', COOK_PREF, COOK_PREF.'_set_values',
	COOK_PREF.'_tunnel_ip',COOK_PREF.'_tunnel_port',
	COOK_PREF.'_useragent',COOK_PREF.'_useragent_custom',
	COOK_PREF.'_url_form',
	COOK_PREF.'_remove_cookies',COOK_PREF.'_remove_referer',
	COOK_PREF.'_remove_scripts',COOK_PREF.'_remove_objects',
	COOK_PREF.'_encrypt_urls',COOK_PREF.'_encrypt_cookies');

# }}}

# PROXY_EXECUTION: REDIRECT IF FORM INPUT {{{

if(IS_FORM_INPUT){
	$theurl=framify_url(surrogafy_url(ORIG_URL),PAGETYPE_FRAME_TOP);
	header("Location: {$theurl}");
	finish();
}

# }}}

# PROXY EXECUTION: REFERER {{{

global $referer;
if($_SERVER['HTTP_REFERER']!=null && !$OPTIONS['REMOVE_REFERER']){
	$refurlobj=new aurl($_SERVER['HTTP_REFERER'], null, true);
	$referer=proxdec(preg_replace(
		'/^=(?:\&=|_\&=|\.\&=)?([^\&]*)[\s\S]*$/i','\1',
		$refurlobj->get_query()
	));
}
else $referer=null;

#$getkeys=array_keys($_GET);
#foreach($getkeys as $getvar){
#	if(!in_array($getvar,$proxy_variables)){
#		$curr_url.=
#			(strpos($curr_url,'?')===false?'?':'&').
#			"$getvar=".urlencode($_GET[$getvar]);
#	}
#}

# }}}

# PROXY EXECUTION: DNS CACHE {{{

if(!isset($_SESSION['DNS_CACHE_ARRAY'])) $dns_cache_array=array();
else $dns_cache_array=$_SESSION['DNS_CACHE_ARRAY'];

# purge old records from DNS cache
while(list($key,$entry)=each($dns_cache_array)){
	if($entry['time']<time()-($CONFIG['DNS_CACHE_EXPIRE']*60))
		unset($dns_cache_array[$key]);
}

# }}}

# PROXY EXECUTION: PAGE RETRIEVAL {{{

global $headers;
$pagestuff=getpage($curr_url);
$body=$pagestuff[0];

$tbody=trim($body);
if(
	($tbody{0}=='"' && substr($tbody,-1)=='"') ||
	($tbody{0}=='\'' && substr($tbody,-1)=='\'')
){
	echo $body;
	finish();
}
unset($tbody);

$curr_url=$pagestuff[1];
define('PAGECOOK_PREFIX',$pagestuff[2]);
unset($pagestuff);
define('CONTENT_TYPE',
	preg_replace('/^([a-z0-9\-\/]+).*$/i','\1',$headers['content-type'][0])); #*

# }}}

# PROXY EXECUTION: PAGE PARSING {{{

if(strpos($body,'<base')){
	$base=preg_replace('/^.*'.BASE_REGEXP.'.*$/is','\1',$body);
	if(!empty($base) && $base!=$body && !empty($base{100})){
		$body=preg_replace('/'.BASE_REGEXP.'/i',null,$body);

		//preg_match('/^(["\']).*\1$/i',$base)>0
		if(
			($base{0}=='"' && substr($base,-1)=='"') ||
			($base{0}=='\'' && substr($base,-1)=='\'')
		) $base=substr($base,1,strlen($base)-2); #*
		$curr_url=$base;
	}
	unset($base);
}

global $curr_urlobj;
$curr_urlobj=new aurl($curr_url);

# PROXY EXECUTION: PAGE PARSING: PARSING FUNCTIONS {{{

function parse_html($regexp,$partoparse,$html,$addproxy,$framify){
	global $curr_urlobj;
	$newhtml=null;
	while(preg_match($regexp,$html,$matcharr,PREG_OFFSET_CAPTURE)){
		$nurl=surrogafy_url($matcharr[$partoparse][0],$curr_urlobj,$addproxy);
		if($framify) $nurl=framify_url($nurl,$framify);
		$begin=$matcharr[$partoparse][1];
		$end=$matcharr[$partoparse][1]+strlen($matcharr[$partoparse][0]);
		$newhtml.=substr_replace($html,$nurl,$begin);
		$html=substr($html,$end,strlen($html)-$end);
	}
	$newhtml.=$html;
	return $newhtml;
}

function regular_express($regexp_array,$thevar){
	# in benchmarks, this 'optimization' appeared to not do anything at all, or
	# possibly even slow things down
	#$regexp_array[2].='S';
	if($regexp_array[0]==1)
		$newvar=preg_replace($regexp_array[2],$regexp_array[3],$thevar);
	elseif($regexp_array[0]==2){
		$addproxy=(isset($regexp_array[4])?$regexp_array[4]:true);
		$framify=(isset($regexp_array[5])?$regexp_array[5]:false);
		$newvar=parse_html(
			$regexp_array[2],$regexp_array[3],$thevar,$addproxy,$framify);
	}
	return $newvar;
}

function parse_all_html($html){
	global $OPTIONS, $regexp_arrays;

	if(CONTENT_TYPE!='text/html'){
		for(reset($regexp_arrays);list($key,$arr)=each($regexp_arrays);){
			if($key==CONTENT_TYPE){
				foreach($arr as $regarr){
					if($regarr==null) continue;
					$html=regular_express($regarr,$html);
				}
			}
		}
		return $html;
	}

	#if($OPTIONS['REMOVE_SCRIPTS']) $splitarr=array($html);
	$splitarr=preg_split(
		'/(<!--(?!\[if).*?-->|<style.*?<\/style>|<script.*?<\/script>)/is',
		$html,-1,PREG_SPLIT_DELIM_CAPTURE);
	unset($html);

	$firstrun=true;
	$firstjsrun=true;
	for(reset($regexp_arrays);list($key,$arr)=each($regexp_arrays);){
		if($key=='text/javascript') continue;

		// OPTION1: use ONLY if no Javascript REGEXPS affect HTML sections and
		// all HTML modifying Javascript REGEXPS are performed after HTML
		// regexps.  This gives a pretty significant speed boost.  If used,
		// make sure "OPTION2" lines are commented, and other "OPTION1" lines
		// AREN'T.
		if($firstjsrun && $key=='application/x-javascript'){
			if($OPTIONS['REMOVE_SCRIPTS']) break;
			$splitarr2=array();
			for($i=0;$i<count($splitarr);$i+=2){
				$splitarr2[$i]=preg_split(
					'/'.REGEXP_SCRIPT_ONEVENT.'/is',$splitarr[$i],-1,
					PREG_SPLIT_DELIM_CAPTURE);
			}
		}
		// END OPTION1

		# firstrun remove scripts: on<event>s and noscript tags; also remove
		# objects
		if(
			$firstrun &&
			($OPTIONS['REMOVE_SCRIPTS'] || $OPTIONS['REMOVE_OBJECTS'])
		){
			for($i=0;$i<count($splitarr);$i+=2){
				if($OPTIONS['REMOVE_SCRIPTS'])
					$splitarr[$i]=preg_replace(
						'/(?:'.REGEXP_SCRIPT_ONEVENT.'|<.?noscript>)/is',null,
						$splitarr[$i]);
				if($OPTIONS['REMOVE_OBJECTS'])
					$splitarr[$i]=preg_replace(
						'/<(embed|object).*?<\/\1>/is',null,$splitarr[$i]);
			}
		}

		foreach($arr as $regexp_array){
			if($regexp_array==null) continue;
			for($i=0;$i<count($splitarr);$i++){

				# parse scripts for on<event>s
				// OPTION1
				if($i%2==0 && isset($splitarr2) && $regexp_array[1]==2){

				// OPTION2
				//if($regexp_array[1]==2 && $i%2==0){
					//$splitarr2[$i]=preg_split(
					//	'/( on[a-z]{3,20}=(?:"(?:[^"]+)"|\'(?:[^\']+)\'|'.
					//	'[^"\' >][^ >]+[^"\' >]))/is',$splitarr[$i],-1,
					//	PREG_SPLIT_DELIM_CAPTURE);
					// END OPTION2

					// UNRELATED TO OPTIONS
					//if(count($splitarr2[$i])<2)
					//	$splitarr[$i]=regular_express(
					//		$regexp_array,$splitarr[$i]);
					if(count($splitarr2[$i])>1){
						for($j=1;$j<count($splitarr2[$i]);$j+=2){
							$begin=preg_replace(
								'/^([^=]+=.).*$/i','\1',$splitarr2[$i][$j]);
							$quote=substr($begin,-1);
							if($quote!='"' && $quote!='\''){
								$quote=null;
								$begin=substr($begin,0,-1);
							}
							$code=preg_replace(
								'/^[^=]+='.
								($quote==null?'(.*)$/i':'.(.*).$/i'),'\1',
								$splitarr2[$i][$j]);
							if(substr($code,0,11)=='javascript:'){
								$begin.='javascript:';
								$code=substr($code,11);
							}
							if($firstjsrun) $code=";{$code};";
							$splitarr2[$i][$j]=
								$begin.regular_express($regexp_array,$code).
								$quote;
						}
						// OPTION2
						//$splitarr[$i]=implode(null,$splitarr2[$i]);
					}
				}

				# remove scripts
				elseif(
					$firstrun &&
					$OPTIONS['REMOVE_SCRIPTS'] &&
					strtolower(substr($splitarr[$i],0,7))=='<script'
				) $splitarr[$i]=null;

				# parse valid HTML in HTML section
				elseif($i%2==0 && $regexp_array[1]==1)
					$splitarr[$i]=regular_express($regexp_array,$splitarr[$i]);

				# parse valid other things
				elseif(
					(
						# HTML key but not in HTML section
						$regexp_array[1]==1 ||

						( # javascript section
							$regexp_array[1]==2 &&
							strtolower(substr($splitarr[$i],0,7))=='<script'
						) ||

						( # CSS section
							$key=='text/css' &&
							strtolower(substr($splitarr[$i],0,6))=='<style'
						)

					) && # not in comment
					substr($splitarr[$i],0,4)!="<!--"
				){
					# DE-STROY!
					$pos=strpos($splitarr[$i],'>');
					$l_html=substr($splitarr[$i],0,$pos+1);
					$l_body=substr($splitarr[$i],$pos+1);

					# HTML parses just HTML
					if($key=='text/html')
						$l_html=regular_express($regexp_array,$l_html);

					# javascript, CSS, and such parses just their own
					else
						$l_body=regular_express($regexp_array,$l_body);

					# put humpty-dumpty together again
					$splitarr[$i]=$l_html.$l_body;
				}

				# script purge cleanup
				if(
					$firstrun &&
					!$OPTIONS['REMOVE_SCRIPTS'] &&
					strtolower(substr($splitarr[$i],-9))=='</script>' &&
					!preg_match('/^[^>]*src/i',$splitarr[$i])
				) $splitarr[$i]=
						preg_replace('/'.END_OF_SCRIPT_TAG.'$/i',
							';'.COOK_PREF.'.purge();//--></script>',
							$splitarr[$i]);

			}

			$firstrun=false;
			if($firstjsrun && $key=='application/x-javascript')
				$firstjsrun=false;
		}
	}

	// OPTION1
	if(!$OPTIONS['REMOVE_SCRIPTS']){
		for($i=0;$i<count($splitarr);$i+=2){
			$splitarr[$i]=implode(null,$splitarr2[$i]);
		}
	}
	// END OPTION1

	return implode(null,$splitarr);
}

# }}}

//$starttime=microtime(true); # BENCHMARK
$body=parse_all_html($body);
//$parsetime=microtime(true)-$starttime; # BENCHMARK

# PROXY EXECUTION: PAGE PARSING: PROXY HEADERS/JAVASCRIPT {{{

if(CONTENT_TYPE=='text/html'){
	$big_headers=
		'<meta name="robots" content="noindex, nofollow" />'.
		($OPTIONS['URL_FORM'] && PAGETYPE_ID===PAGETYPE_FRAMED_PAGE?
			'<base target="_top">':null).
		'<link rel="shortcut icon" href="'.
			surrogafy_url(
				$curr_urlobj->get_proto().'://'.
				$curr_urlobj->get_servername().'/favicon.ico').'" />'.
		(!$CONFIG['REMOVE_SCRIPTS']?
			'<script type="text/javascript" src="'.THIS_SCRIPT.'?js_funcs'.
				(PAGE_FRAMED?'_framed':null).'"></script>'.
			'<script type="text/javascript" src="'.THIS_SCRIPT.
				'?js_regexps'.(PAGE_FRAMED?'_framed':null).'"></script>'.
			'<script type="text/javascript">'.
			//'<!--'.

			COOK_PREF.'_do_proxy=true;'.

			COOK_PREF.'.CURR_URL="'.
				str_replace(
					'"','\\"',$curr_urlobj->get_url()).'"+location.hash;'.
						COOK_PREF.'.gen_curr_urlobj();'.

			COOK_PREF.'.DOCUMENT_REFERER="'.(
				$OPTIONS['URL_FORM']?
				str_replace('"','\\"',$referer):
				null).'";'.

			COOK_PREF.'.ENCRYPT_COOKIES='.
				bool_to_js($OPTIONS['ENCRYPT_COOKIES']).';'.

			COOK_PREF.'.ENCRYPT_URLS='.bool_to_js($OPTIONS['ENCRYPT_URLS']).
				';'.

			COOK_PREF.'.LOCATION_HOSTNAME="'.
				str_replace('"','\\"',$curr_urlobj->get_servername()).'";'.

			COOK_PREF.'.LOCATION_PORT="'.
				str_replace('"','\\"',$curr_urlobj->get_portval()).'";'.

			COOK_PREF.'.LOCATION_SEARCH="'.(
					$curr_urlobj->get_query()!=null?
					'?'.str_replace('"','\\"',$curr_urlobj->get_query()):
					null
				).'";'.

			COOK_PREF.'.NEW_PAGETYPE_FRAME_TOP='.NEW_PAGETYPE_FRAME_TOP.';'.

			COOK_PREF.'.PAGE_FRAMED='.bool_to_js(PAGE_FRAMED).';'.

			COOK_PREF.'.REMOVE_OBJECTS='.
				bool_to_js($OPTIONS['REMOVE_OBJECTS']).';'.

			COOK_PREF.'.URL_FORM='.bool_to_js($OPTIONS['URL_FORM']).';'.

			COOK_PREF.".USERAGENT=\"{$useragent}\";".
				(
					$OPTIONS['URL_FORM'] && PAGETYPE_ID==PAGETYPE_FRAMED_PAGE?
					'if('.COOK_PREF.'.theparent=='.COOK_PREF.'.thetop) '.
						COOK_PREF.'.eventify("'.$curr_urlobj->get_proto().
						'","'.$curr_urlobj->get_servername().'");':
					null
				).

			//'//-->'.
			'</script>':
		null);

	$body=preg_replace(
		'/(?:(<(?:head|body)[^>]*>)|(<(?:\/head|meta|link|script)))/i',
		"\\1$big_headers\\2",$body,1);
	unset($big_headers);
}
elseif(
	CONTENT_TYPE=='application/x-javascript' ||
	CONTENT_TYPE=='text/javascript'
) $body.=';'.COOK_PREF.'.purge();';

# }}}

# }}}

## Retrieved, Parsed, All Ready to Output ##
echo $body;

# BENCHMARK
//echo
//	'total time: '.(microtime(true)-$totstarttime).
//	"<br />parse time: {$parsetime} seconds".
//	(isset($oparsetime)?"<br />other time 1: {$oparsetime} seconds":null).
//	(isset($oparsetime2)?"<br />other time 2: {$oparsetime2} seconds":null);

# }}}

finish_noexit();

############
## THE END ##
##############
#
# VIM is the ideal way to edit this file.  Automatic folding occurs making the
# blocks of code easier to read and navigate
# vim:foldmethod=marker
#
################## ?>
