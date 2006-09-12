<?php

#
# Surrogafier v0.9.1b
#
# Author: Brad Cable
# Email: brad@bcable.net
# License: Modified BSD
# License Details:
# http://bcable.net/license.php
#


## CONFIG ##

# Time limit for a single request and parse. [30]
define('TIME_LIMIT',30);

# Default to simple mode when the page is loaded. [false]
define('DEFAULT_SIMPLE',false);
# Force the page to always be in simple mode (no advanced mode option). [false]
define('FORCE_SIMPLE',false);
# Width for the URL box when in simple mode (CSS "width" attribute). [300px]
define('SIMPLE_MODE_URLWIDTH','300px');

# Default value for tunnel server. []
define('DEFAULT_TUNNEL_PIP','');
# Default value for tunnel port. []
define('DEFAULT_TUNNEL_PPORT','');
# Should the tunnel fields be displayed? "false" value here will force the defaults above [true]
define('FORCE_DEFAULT_TUNNEL',true);

# Default value for "Persistent URL" checkbox [true]
define('DEFAULT_URL_FORM',true);
# Default value for "Remove Cookies" checkbox [false]
define('DEFAULT_REMOVE_COOKIES',false);
# Default value for "Remove Referer Field" checkbox [false]
define('DEFAULT_REMOVE_REFERER',false);
# Default value for "Remove Scripts" checkbox [false]
define('DEFAULT_REMOVE_SCRIPTS',false);
# Default value for "Remove Objects" checkbox [false]
define('DEFAULT_REMOVE_OBJECTS',false);
# Default value for "Encrypt URLs" checkbox [false]
define('DEFAULT_ENCRYPT_URLS',false);
# Default value for "Encrypt Cookies" checkbox [false]
define('DEFAULT_ENCRYPT_COOKS',false);

# Protocol that proxy is running on. [http]
define('PROTO','http');

/*/ Address Blocking Notes \*\

Formats for address blocking are as follows:

  1.2.3.4     - plain IP address
  1.2.3.4/24  - subnet blocking
  php.net     - domain blocking

\*\ End Address Blocking Notes /*/

$blocked_addresses=array('10.0.0.0/24','172.0.0.0/24','192.0.0.0/24','127.0.0.0/24');

## END CONFIG ##



// DON'T EDIT ANYTHING AFTER THIS POINT \\


#
# (unless you absolutely know what you are doing...)
#


if(!ini_get('safe_mode')) set_time_limit(TIME_LIMIT);
if(extension_loaded('zlib') && !ini_get('zlib.output_compression')) ob_start('ob_gzhandler'); # use gzip encoding to compress all data, if possible

# reverse magic quotes if enabled
if(get_magic_quotes_gpc()){
	function stripslashes_recurse($var){
		if(is_array($var)) $var=array_map('stripslashes_recurse',$var);
		else $var=stripslashes($var);
		return $var;
	}
	$_GET=stripslashes_recurse($_GET);
	$_POST=stripslashes_recurse($_POST);
	$_COOKIE=stripslashes_recurse($_COOKIE);
}

define('VERSION','0.9.1b');
define('THIS_SCRIPT',PROTO."://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}");
define('SIMPLE_MODE',DEFAULT_SIMPLE || FORCE_SIMPLE);

# Randomized cookie prefixes #
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

define('FIRST_LOAD',empty($_COOKIE['PHPSESSID']));
session_start();
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

if(FIRST_LOAD){
	if(DEFAULT_URL_FORM) dosetcookie(COOK_PREF.'_url_form',true);
	if(DEFAULT_REMOVE_COOKIES) dosetcookie(COOK_PREF.'_remove_cookies',true);
	if(DEFAULT_REMOVE_REFERER) dosetcookie(COOK_PREF.'_remove_referer',true);
	if(DEFAULT_REMOVE_SCRIPTS) dosetcookie(COOK_PREF.'_remove_scripts',true);
	if(DEFAULT_REMOVE_OBJECTS) dosetcookie(COOK_PREF.'_remove_objects',true);
	if(DEFAULT_ENCRYPT_URLS) dosetcookie(COOK_PREF.'_encrypt_urls',true);
	if(DEFAULT_ENCRYPT_COOKS) dosetcookie(COOK_PREF.'_encrypt_cooks',true);
}

# end #

$postandget=array_merge($_GET,$_POST);
define('ENCRYPT_URLS',(empty($postandget[COOK_PREF.'_set_values'])?!empty($_COOKIE[COOK_PREF.'_encrypt_urls']):!empty($postandget[COOK_PREF.'_encrypt_urls'])));
define('URL_FORM',(empty($postandget[COOK_PREF.'_set_values'])?!empty($_COOKIE[COOK_PREF.'_url_form']):!empty($postandget[COOK_PREF.'_url_form'])));
define('PAGE_FRAMED',(!empty($_GET[COOK_PREF.'_framed']) || $_SERVER['QUERY_STRING']=='js_regexps_framed'));
#define('URLVAR',(ENCRYPT_URLS?'e':null).'url');

function my_base64_decode($string){ return base64_decode(str_replace(' ','+',urldecode($string))); }

function proxdec($url){
	if(substr($url,0,1)!='~' && strtolower(substr($url,0,3))!='%7e') return $url;
	#while(strpos($url,'%')!==false) $url=urldecode($url);
	#$url=urldecode($url);
	while(substr($url,0,1)=='~' || strtolower(substr($url,0,3))=='%7e'){
		$url=substr($url,1,strlen($url)-1);
		$url=my_base64_decode($url);
		$new_url=null;
		for($i=0;$i<strlen($url);$i++){
			$char=ord(substr($url,$i,1));
			$char-=ord(substr(SESS_PREF,$i%strlen(SESS_PREF),1));
			while($char<32) $char+=94;
			$new_url.=chr($char);
		}
		$url=$new_url;
	}
	return urldecode($url);
}

$js_proxenc='

function expon(a,b){
	if(b==0) return 1;
	num=a; b--;
	while(b>0){ num*=a; b--; }
	return num;
}

function b64e(string){
	if(window.btoa) return btoa(string);
	binrep="";
	for(i=0;i<string.length;i++){
		charnum=string.charCodeAt(i);
		for(j=7;j>=0;j--){
			if(charnum>=expon(2,j)){
				binrep+="1"; charnum-=expon(2,j);
			}
			else binrep+="0";
		}
	}
	while(binrep.length%6) binrep+="00";
	encstr="";
	for(i=1;i*6<=binrep.length;i++){
		charbin=binrep.substring((i-1)*6,i*6);
		charnum=0;
		for(j=0;j<6;j++) if(charbin.substring(j,j+1)=="1") charnum+=expon(2,5-j);
		if(charnum<=25) charnum+=65;
		else if(charnum<=51) charnum+=71;
		else if(charnum<=61) charnum-=4;
		else if(charnum==62) charnum=43;
		else if(charnum==63) charnum=47;
		encstr+=String.fromCharCode(charnum);
	}
	while(encstr.length%8) encstr+="=";
	return encstr;
}

function proxenc(url){
	if(url.substring(0,1)=="~" || url.substring(0,3).toLowerCase()=="%7e") return url;
	url=encodeURIComponent(url);
	new_url="";
	sess_pref="'.SESS_PREF.'";
	for(i=0;i<url.length;i++){
		char=url.charCodeAt(i);
		char+=sess_pref.charCodeAt(i%sess_pref.length);
		while(char>126) char-=94;
		new_url+=String.fromCharCode(char);
	}
	return "~"+encodeURIComponent(b64e(new_url));
	//return "~"+b64e(new_url);
}';


if(!empty($postandget['force_main']) || (substr($_SERVER['QUERY_STRING'],0,3)!='js_' && empty($postandget[COOK_PREF]))){

## First Page Displayed When Accessing the Proxy ##

$useragentinfo=null;
if(preg_match('/(?:linux|x11)/i',$_SERVER['HTTP_USER_AGENT'])) $useragentinfo.='Linux';
elseif(preg_match('/win(?:dows|32)/i',$_SERVER['HTTP_USER_AGENT'])) $useragentinfo.='Windows';
elseif(preg_match('/mac/i',$_SERVER['HTTP_USER_AGENT'])) $useragentinfo.='Macintosh';
else $useragentinfo.='Unknown';

$useragentinfo.=' / ';

if(preg_match('/msie/i',$_SERVER['HTTP_USER_AGENT'])) $useragentinfo.='Internet Explorer';
elseif(preg_match('/firefox/i',$_SERVER['HTTP_USER_AGENT'])) $useragentinfo.='Firefox';
elseif(preg_match('/netscape/i',$_SERVER['HTTP_USER_AGENT'])) $useragentinfo.='Netscape';
elseif(preg_match('/konqueror/i',$_SERVER['HTTP_USER_AGENT'])) $useragentinfo.='Konqueror';
elseif(preg_match('/seamonkey/i',$_SERVER['HTTP_USER_AGENT'])) $useragentinfo.='SeaMonkey';
elseif(preg_match('/opera/i',$_SERVER['HTTP_USER_AGENT'])) $useragentinfo.='Opera';
else $useragentinfo.='Unknown';

$useragent_array=array(
	array(null,"Actual ($useragentinfo)"),
	array('-1',' [ Don\'t Send ] '),
	array('Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8) Gecko/20051111 Firefox/1.5','Windows XP / Firefox 1.5'),
	array('Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)','Windows XP / Internet Explorer 6'),
	array('Opera/8.51 (Windows NT 5.1; U; en)','Windows XP / Opera 8.51'),
	array('Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8) Gecko/20051111 Firefox/1.5','Linux / Firefox 1.5'),
	array('Opera/8.51 (X11; Linux i686; U; en)','Linux / Opera 8.51'),
	array('Mozilla/5.0 (compatible; Konqueror/3.4; Linux) KHTML/3.4.2 (like Gecko)','Linux / Konqueror 3.4.2'),
	array('Links (2.1pre18; Linux 2.6.14.5 i686; 180x58)','Linux / Links (2.1pre18)'),
	array('Dillo/0.8.5','Any / Dillo 0.8.5'),
	array('Wget/1.10.2','Any / Wget 1.10.2'),
	array('Lynx/2.8rel5','Any / Lynx 2.8rel.5'),
	array('1',' [ Custom ] <noscript><b>**</b></noscript>')
);

$ipregexp='/^((?:[0-2]{0,2}[0-9]{1,2}\.){3}[0-2]{0,2}[0-9]{1,2})\:([0-9]{1,5})$/';

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
<head>
<title>Surrogafier</title>
<meta name="robots" content="index, nofollow" />
<style>
	body{font-family: bitstream vera sans, trebuchet ms}
	input{border: 1px solid #000000}
	select{border: 1px solid #000000}
	a{color: #000000}
	a:hover{text-decoration: none}
	.advanced_stuff{display: <?php echo(SIMPLE_MODE?'none':'table-row'); ?>}
	.simple_stuff{display: <?php echo(SIMPLE_MODE?'table-row':'none'); ?>}
	.url{width: <?php echo(SIMPLE_MODE?SIMPLE_MODE_URLWIDTH:'99%'); ?>}
	.signature{float: left}
<?php if(FORCE_SIMPLE){ ?>
	.noscript_stuff{display: none}
	.signature{text-align: center; float: none}
<?php } ?>
</style>
<?php if(!FORCE_SIMPLE){ ?><noscript><style>
	.advanced_stuff{display: table-row}
	.simple_stuff{display: none}
	.noscript_stuff{display: none}
	.noscripturl{width: 99%}
	.url{display: none}
	.signature{text-align: center; float: none}
</style></noscript><?php } ?>
<script language="javascript">
<!--

<?php echo($js_proxenc); ?>

function useragent_check(focus){
	if(document.getElementsByName('<?php echo(COOK_PREF); ?>_useragent')[0].value=='1'){
		document.getElementById('useragent_texttr').style.display="";
		if(focus) document.getElementById('<?php echo(COOK_PREF); ?>_useragenttext').focus();
	}
	else document.getElementById('useragent_texttr').style.display='none';
}

<?php if(!FORCE_SIMPLE){ ?>
advanced_mode=true;
function toggle_mode(){
	document.getElementById("mode_toggler").innerHTML=(advanced_mode?"Advanced Mode":"Simple Mode");
	advanced_stuff=document.getElementsByTagName("tr");
	for(i=1;i<=12;i++) advanced_stuff[i].style.display=(advanced_mode?"none":"");
	document.getElementById("simple_submit").style.display=(advanced_mode?"inline":"none");
	document.getElementById("url").style.width=(advanced_mode?"<?php echo(SIMPLE_MODE_URLWIDTH); ?>":"99%");
	advanced_mode=!advanced_mode;
	if(advanced_mode) useragent_check(false);
	setTimeout("document.getElementById('url').focus();",100);
}
<?php } ?>

function submit_code(){
	document.forms[0].<?php echo(COOK_PREF); ?>.disabled=false;
	document.forms[0].<?php echo(COOK_PREF); ?>.value=document.getElementById('url').value;
	if(document.forms[0].<?php echo(COOK_PREF); ?>_encrypt_urls.checked){
		document.forms[0].<?php echo(COOK_PREF); ?>.value=proxenc(document.forms[0].<?php echo(COOK_PREF); ?>.value);
	}
	return true;
}

//-->
</script>
</head>
<body<?php echo(SIMPLE_MODE?' onload="toggle_mode();"':null); ?>>
<center>
<span style="font-size: 18pt; font-weight: bold; margin-bottom: 5px">Surrogafier</span>
<form method="post" onsubmit="return submit_code();" style="margin: 0px; padding: 0px">
<input type="hidden" name="<?php echo(COOK_PREF); ?>_set_values" value="1" />
<input type="hidden" name="<?php echo(COOK_PREF); ?>" disabled="disabled" />
<input type="hidden" name="force_main" value="" />
<table>
<tr>
<td style="text-align: left">URL:&nbsp;&nbsp;</td>
<td>
	<input type="text" class="url" id="url" value="<?php echo(proxdec($postandget[COOK_PREF])); ?>" />
	<noscript><input type="text" class="noscripturl" name="<?php echo(COOK_PREF); ?>" id="url" value="<?php echo(proxdec($postandget[COOK_PREF])); ?>" /></noscript>
	<input type="submit" class="simple_stuff" id="simple_submit" value="Surrogafy" style="background-color: #F0F0F0" />
</td>
</tr>
<?php if(FORCE_DEFAULT_TUNNEL){ ?>
<tr class="advanced_stuff">
<td style="text-align: left">Tunnel Proxy:</td>
<td><table cellspacing="0" cellpadding="0">
<tr>
	<td style="width: 100%"><input type="text" name="<?php echo(COOK_PREF); ?>_pip" onkeyup="if(this.value.match(<?php echo($ipregexp); ?>)){ document.forms[0].<?php echo(COOK_PREF); ?>_pport.value=this.value.replace(<?php echo($ipregexp); ?>,'\$2'); this.value=this.value.replace(<?php echo($ipregexp); ?>,'\$1'); document.forms[0].<?php echo(COOK_PREF); ?>_pport.focus(); };" style="width: 100%; text-align: left" value="<?php echo(empty($_COOKIE[COOK_PREF.'_pip'])?DEFAULT_TUNNEL_PIP:$_COOKIE[COOK_PREF.'_pip']); ?>" /></td>
	<td style="width: 5px">&nbsp;&nbsp;</td>
	<td style="width: 50px"><input type="text" name="<?php echo(COOK_PREF); ?>_pport" maxlength="5" size="5" style="width: 50px" value="<?php echo(empty($_COOKIE[COOK_PREF.'_pport'])?DEFAULT_TUNNEL_PPORT:$_COOKIE[COOK_PREF.'_pport']); ?>" /></td>
</tr>
</table></td>
</tr>
<?php } ?>
<tr class="advanced_stuff">
<td style="text-align: left">User-Agent:</td>
<td><select name="<?php echo(COOK_PREF); ?>_useragent" style="width: 100%" onchange="useragent_check(true);">
<?php foreach($useragent_array as $useragent){ ?>
	<option value="<?php echo($useragent[0]); ?>"<?php if($_COOKIE[COOK_PREF.'_useragent']==$useragent[0]) echo ' selected="selected"'; ?>><?php echo($useragent[1]); ?></option>
<?php } ?>
</select></td>
</tr>
<tr class="advanced_stuff" id="useragent_texttr"<?php echo($_COOKIE[COOK_PREF.'_useragent']=='1'?null:' style="display: none"'); ?>>
	<td>&nbsp;</td>
	<td><input type="text" id="<?php echo(COOK_PREF); ?>_useragenttext" name="<?php echo(COOK_PREF); ?>_useragenttext" value="<?php echo($_COOKIE[COOK_PREF.'_useragenttext']); ?>" style="width: 99%" /></td>
</tr>
<tr class="advanced_stuff"><td>&nbsp;</td><td style="text-align: left"><input type="checkbox" name="<?php echo(COOK_PREF); ?>_url_form" style="border: 0px" <?php if(!empty($_COOKIE[COOK_PREF.'_url_form'])) echo 'checked="checked" '; ?>/>&nbsp;Persistent URL Form</td></tr>
<tr class="advanced_stuff"><td>&nbsp;</td><td style="text-align: left"><input type="checkbox" name="<?php echo(COOK_PREF); ?>_remove_cookies" style="border: 0px" <?php if(!empty($_COOKIE[COOK_PREF.'_remove_cookies'])) echo 'checked="checked" '; ?>/>&nbsp;Remove Cookies</td></tr>
<tr class="advanced_stuff"><td>&nbsp;</td><td style="text-align: left"><input type="checkbox" name="<?php echo(COOK_PREF); ?>_remove_referer" style="border: 0px" <?php if(!empty($_COOKIE[COOK_PREF.'_remove_referer'])) echo 'checked="checked" '; ?>/>&nbsp;Remove Referer Field</td></tr>
<tr class="advanced_stuff"><td>&nbsp;</td><td style="text-align: left"><input type="checkbox" name="<?php echo(COOK_PREF); ?>_remove_scripts" style="border: 0px" <?php if(!empty($_COOKIE[COOK_PREF.'_remove_scripts'])) echo 'checked="checked" '; ?>/>&nbsp;Remove Scripts (JS, VBS, etc)</td></tr>
<tr class="advanced_stuff"><td>&nbsp;</td><td style="text-align: left"><input type="checkbox" name="<?php echo(COOK_PREF); ?>_remove_objects" style="border: 0px" <?php if(!empty($_COOKIE[COOK_PREF.'_remove_objects'])) echo 'checked="checked" '; ?>/>&nbsp;Remove Objects (Flash, Java, etc)</td></tr>
<tr class="advanced_stuff"><td>&nbsp;</td><td style="text-align: left"><input type="checkbox" name="<?php echo(COOK_PREF); ?>_encrypt_urls" style="border: 0px" <?php if(!empty($_COOKIE[COOK_PREF.'_encrypt_urls'])) echo 'checked="checked" '; ?>/>&nbsp;Encrypt URLs<noscript><b>**</b></noscript></td></tr>
<tr class="advanced_stuff"><td>&nbsp;</td><td style="text-align: left"><input type="checkbox" name="<?php echo(COOK_PREF); ?>_encrypt_cooks" style="border: 0px" <?php if(!empty($_COOKIE[COOK_PREF.'_encrypt_cooks'])) echo 'checked="checked" '; ?>/>&nbsp;Encrypt Cookies<noscript><b>**</b></noscript></td></tr>
<tr class="advanced_stuff"><td colspan="2"><input type="submit" value="Surrogafy" style="width: 100%; background-color: #F0F0F0" /></td></tr>
<tr><td style="font-size: 8pt" colspan="2">
<div class="signature"><a href="http://bcable.net/">Surrogafier v<?php echo(VERSION); ?> <b>&middot;</b> Brad Cable</a></div>
<div class="noscript_stuff" style="float: right"><a href="#" onclick="toggle_mode();" id="mode_toggler"><?php echo(SIMPLE_MODE?'Advanced':'Simple'); ?> Mode</a></div>
</td></tr>
</table>
<noscript>
<br />
<b>**</b> Surrogafier has detected that your browser does not have Javascript enabled. <b>**</b>
<br />
<b>**</b> Surrogafier requires Javascript in order to function to its full potential. <b>**</b>
</noscript>
</form>
</center>
</body>
</html>

<?php exit(); }

# page with URL form
if(!empty($_GET[COOK_PREF.'_frame']) && !empty($postandget[COOK_PREF])){ ?>
<html>
<head>
<style>
	body{font-family: bitstream vera sans, trebuchet ms; margin: 0px; padding: 0px; font-size: 12px; overflow: hidden}
	input{border: 1px solid #000000}
	td{font-size: 12px}
	a{text-decoration: none; color: #000000}
	a:hover{text-decoration: underline}
</style>
<script><?php echo(COOK_PREF); ?>=true;</script>
</head>
<body>
<form method="get">
<input type="hidden" name="<?php echo(COOK_PREF); ?>_frame" value="1" />
<table cellpadding="0" cellspacing="0" style="width: 100%; height: 100%; padding: 0px; margin: 0px">
<tr><td><table cellpadding="0" cellspacing="0" style="width: 100%; padding: 3px">
<tr>
	<td>&nbsp;<b><a id="proxy_link" href="<?php echo(THIS_SCRIPT.'?force_main=1&'.COOK_PREF.'='.$postandget[COOK_PREF]); ?>">Surrogafier</a></b>&nbsp;&nbsp;</td>
	<td style="width: 100%"><input type="text" class="url" name="<?php echo(COOK_PREF); ?>" style="width: 100%; padding-left: 4px" id="url" value="<?php echo(proxdec($postandget[COOK_PREF])); ?>" /></td>
	<td>&nbsp;</td>
	<td><input type="submit" class="simple_stuff" id="simple_submit" value="Surrogafy" style="background-color: #F0F0F0" /></td>
</tr>
</table></td></tr>
<tr><td style="height: 100%; border-top: 1px solid #000000">
<iframe src="<?php echo(THIS_SCRIPT.'?'.COOK_PREF."={$postandget[COOK_PREF]}&".COOK_PREF.'_framed=1'); ?>" frameborder="0" style="border: 0px; width: 100%; height: 100%"></iframe>
</td></tr>
</table>
</form>
</body>
</html>
<?php exit(); }

#define('AURL_LOCK_REGEXP','(?:(?:javascript|mailto|about):|~|%7e)');
define('FRAME_LOCK_REGEXP','(?:(?:javascript|mailto|about):)');
define('AURL_LOCK_REGEXP','(?:(?:javascript|mailto|about):|'.str_replace(array('/','.'),array('\/','\.'),addslashes(THIS_SCRIPT)).')');

## JAVASCRIPT FUNCS ##
if($_SERVER['QUERY_STRING']=='js_funcs'){ ?>//<script>

function trim(str){ return str.replace(/^\s*([\s\S]*?)\s*$/,"$1"); }

const URLREG=/^(?:([a-z]*)?(?:\:?\/\/))(?:([^\@\/]*)\@)?([^\/:\?\#\&]*)(?:\:([0-9]+))?(\/[^\&\?\#]*?)?([^\/\?\#\&]*(?:\&[^\?\#]*)?)(?:\?([\s\S]*?))?(?:\#([\s\S]*))?$/i;

function aurl(url,topurl){

	this.get_fieldreq=function(fieldno,value){
		fieldreqs=new Array();
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
	this.set_userpass=function(userpass){ if(userpass==undefined) userpass=""; this.userpass=userpass; }
	this.get_servername=function(){ return this.servername; }
	this.set_servername=function(servername){ if(servername==undefined) servername=""; this.servername=servername; }
	this.get_portval=function(){ return ((this.portval=="")?(this.get_proto()=="https"?"443":"80"):this.portval); }
	this.set_portval=function(port){ if(port==undefined) port=""; this.portval=((parseInt(port)!=80)?port:"").toString(); }
	this.get_path=function(){ // ***
		if(this.path.indexOf("/../")!=-1) this.path=this.path.replace(/(?:\/[^\/]+){0,1}\/\.\.\//g,"/");
		if(this.path.indexOf("/./")!=-1) while((path=this.path.replace("/./","/")) && path!=this.path) this.path=path;
		return this.path;
	}
	this.set_path=function(path){ if(path==undefined) path="/"; this.path=path; }
	this.get_file=function(){ return this.file; }
	this.set_file=function(file){ if(file==undefined) file=""; this.file=file; }
	this.get_query=function(){ return this.query; }
	this.set_query=function(query){ if(query==undefined) query=""; this.query=query; }
	this.get_label=function(){ return this.label; }
	this.set_label=function(label){ if(label==undefined) label=""; this.label=label; }

	this.get_url=function(){
		if(this.locked) return this.url;
		return this.get_proto()+"://"+
		       (this.get_userpass()==""?"":this.get_userpass()+"@")+
		       this.get_servername()+
		       (parseInt(this.get_portval())==80?"":":"+parseInt(this.get_portval()))+
		       this.get_path()+this.get_file()+
		       (this.get_query()==""?"":"?"+this.get_query())+
		       (this.get_label()==""?"":"#"+this.get_label())
		;
	}

	this.surrogafy=function(){
		url=this.get_url();
		if(this.locked || this.get_proto()+this.get_fieldreq(2,this.get_userpass())+this.get_servername()+this.get_path()+this.get_file()==proxy_this_script) return url;
		label=this.get_label();
		this.set_label();
		if(proxy_encrypt_urls && !this.locked) url=proxenc(url);
		url=proxy_this_script+"?<?php echo(COOK_PREF); ?>="+(!proxy_encrypt_urls?escape(url):url);
		this.set_label(label);
		return url;
	}

	//this.url=preg_replace("/&#([0-9]+);/e","chr(\\1)" // parse like PHP does for &#num; HTML entities?
	this.url=trim(url.replace("&amp;","&").replace("\r","").replace("\n",""));
	this.topurl=topurl;
	this.locked=(url.match(/^<?php echo(AURL_LOCK_REGEXP); ?>/i)?true:false); //*

	if(this.locked) return;

	urlwasvalid=true;
	if(!this.url.match(URLREG)){
		urlwasvalid=false;
		if(this.topurl==undefined) this.url="http://"+((this.url.charAt(0)==":" || this.url.charAt(0)=="/")?this.url.substring(1):this.url)+(this.url.indexOf("/")!=-1?"":"/");
		else{
			newurl=this.topurl.get_proto()+"://"+this.get_fieldreq(2,this.topurl.get_userpass())+this.topurl.get_servername()+((this.topurl.get_portval()!=80 && (this.topurl.get_proto()=="https"?this.topurl.get_portval()!=443:true))?":"+this.topurl.get_portval():"");
			if(this.url.substring(0,1)!="/") newurl+=this.topurl.get_path();
			this.url=newurl+this.url;
		}
	}

	this.set_proto((urlwasvalid || this.topurl==undefined?this.url.replace(/^([^:]+).*$/,"\$1"):this.topurl.get_proto()));
	this.set_userpass(this.url.replace(URLREG,"$2"));
	this.set_servername(this.url.replace(URLREG,"$3"));
	this.set_portval(this.url.replace(URLREG,"$4"));
	this.set_path(this.url.replace(URLREG,"$5"));
	this.set_file(this.url.replace(URLREG,"$6"));
	this.set_query(this.url.replace(URLREG,"$7"));
	this.set_label(this.url.replace(URLREG,"$8"));

	//if(!this.locked && !this.url.match(URLREG)) havok(7,this.url); //*
}

function surrogafy_url(url,topurl,addproxy){
	if(addproxy==undefined) addproxy=true;
	urlquote="";
	if((url.substring(0,1)=="\"" || url.substring(0,1)=="'") && url.substring(0,1)==url.substring(url.length-1,url.length)){
		urlquote=url.substring(0,1);
		url=url.substring(1,url.length-1);
	}
	if(topurl==undefined) topurl=curr_urlobj;
	urlobj=new aurl(url,topurl);
	new_url=(addproxy?urlobj.surrogafy():urlobj.get_url());
	if(urlquote!="") new_url=urlquote+new_url+urlquote;
	return new_url;
}

function surrogafy_url_toobj(url,topurl,addproxy){
	if(addproxy==undefined) addproxy=true;
	if((url.substring(0,1)=="\"" || url.substring(0,1)=="'") && url.substring(0,1)==url.substring(url.length-1,url.length)) url=url.substring(1,url.length-1);
	if(topurl==undefined) topurl=curr_urlobj;
	urlobj=new aurl(url,topurl);
	return urlobj;
}

<?php echo($js_proxenc); ?>

function preg_match_all(regexpstr,string){
	matcharr=new Array();
	regexp=new RegExp(regexpstr);
	while(true){
		result=regexp.exec(string);
		if(result!=null) matcharr.push(result);
		else break;
	}
	return matcharr;
}

function parse_html(regexp,partoparse,html,addproxy){
	if(html.match(regexp)){
		matcharr=preg_match_all(regexp,html);
		newhtml="";
		for(key in matcharr){
			/*match=matcharr[i];
			nurl=surrogafy_url(match[partoparse],undefined,addproxy);
			nhtml=match[0].replace(match[partoparse],nurl);
			html=html.replace(match[0],nhtml);*/
			match=matcharr[key];
			begin=html.indexOf(match[partoparse]);
			end=begin+match[partoparse].length;
			nurl=surrogafy_url(match[partoparse],undefined,addproxy);
			newhtml+=html.substring(0,begin)+nurl;
			html=html.substring(end);
		}
		html=newhtml+html;
	}
	return html;
}

function parse_all_html(){
	if(arguments[0]==null) return;
	html=arguments[0].toString();
	for(key in regexp_arrays){
		if(arguments.length>1 && key!=arguments[1]) continue;
		arr=regexp_arrays[key];
		for(regexp_arraykey in arr){
			regexp_array=arr[regexp_arraykey];
			if(regexp_array[0]==undefined) continue;
			if(regexp_array[0]==1) html=html.replace(regexp_array[1],regexp_array[2]);
			else if(regexp_array[0]==2){
				if(regexp_array.length<4) addproxy=true;
				else addproxy=false;
				html=parse_html(regexp_array[1],regexp_array[2],html,addproxy);
			}
		}
	}
	return html;
}

function proxy_form_encrypt(form){
	if(form.method=='post') return true;
	action=(proxy_encrypt_urls?form.<?php echo(COOK_PREF); ?>.value:form.<?php echo(COOK_PREF); ?>.value);
	for(i=1;i<form.elements.length;i++){
		if(form.elements[i].disabled || form.elements[i].name=='' || form.elements[i].value=='' || form.elements[i].type=='reset') continue;
		if(form.elements[i].type=='submit'){
			if(form.elements[i].name!=proxy_form_button) continue;
			proxy_form_button=null;
		}
		if(!action.match(/\?/)) pref="?";
		else pref="&";
		action+=pref+form.elements[i].name+"="+form.elements[i].value;
	}
	location.href=surrogafy_url(action);
	return false;
}

function setParentURL(url){
	if(top!=window){
		top.document.getElementById('url').value=url;
		top.document.getElementById('proxy_link').href=surrogafy_url(url)+"&force_main=1";
	}
}

function proxy_setAttribute(obj,attr,url){
	proxurl=surrogafy_url(url);
	if(proxy_url_form){
		prefix="";
		if(obj=="location" || attr=="location") prefix="top.";
		if(obj=="location" || attr=="location" || attr=="href"){
			urlobj=surrogafy_url_toobj(url);
			append="";
			if(!urlobj.locked) append="&<?php echo(COOK_PREF); ?>_frame=1";
			eval_actual(prefix+obj+"."+attr+"=\""+proxurl+append+"\"");
		}
		else eval_actual(obj+"."+attr+"=\""+proxurl+"\";");
	}
	else eval_actual(obj+"."+attr+"=\""+proxurl+"\";");
}

// function wrapping

document.write_actual=document.write;
document.write=function(html){
	html=parse_all_html(html);
	return document.write_actual(html);
}

document.writeln_actual=document.writeln;
document.writeln=function(html){
	html=parse_all_html(html);
	return document.writeln_actual(html);
}

window.open_actual=window.open;
window.open=function(url,arg2,arg3){
	url=surrogafy_url(url);
	if((url.substring(0,1)=="\"" || url.substring(0,1)=="'") && url.substring(0,1)==url.substring(url.length-1,url.length)) url=url.substring(1,url.length-1);
	return window.open_actual(url,arg2,arg3);
}

eval_actual=eval;
eval=function(js){
	js=parse_all_html(js,"text/javascript");
	return eval_actual(js);
}

proxy_XMLHttpRequest_open=function(){
	switch(arguments.length){
		case 1: break;
		case 2: this._realopen(arguments[0],surrogafy_url(arguments[1])); break;
		case 3: this._realopen(arguments[0],surrogafy_url(arguments[1]),arguments[2]); break;
		case 4: this._realopen(arguments[0],surrogafy_url(arguments[1]),arguments[2],arguments[3]); break;
		case 5: this._realopen(arguments[0],surrogafy_url(arguments[1]),arguments[2],arguments[3],arguments[4]); break;
	}
}

//</script><?php exit(); }
## END JAVASCRIPT FUNCS ##


## REGEXPS ##
#
# This is where all the parsing is defined.  If a site isn't being
# parsed properly, the problem is more than likely in this section.
# The rest of the code is just there to set up this wonderful bunch
# of incomprehensible regular expressions.
#

# Regexp Conversion to Javascript #
function escape_regexp($regexp,$dollar=false){
	$regexp=str_replace('\\','\\\\',str_replace('\'','\\\'',str_replace('"','\\"',str_replace("\n",'\n',str_replace("\r",'\r',str_replace("\t",'\t',$regexp))))));
	return ($dollar?preg_replace('/[\\\\]+(?=[0-9])/','\\\\$',$regexp):preg_replace('/[\\\\]+(?=[0-9])/','\\\\\\\\',$regexp)); #*
}

function convertarray_to_javascript(){
	global $regexp_arrays;
	$js='regexp_arrays=new Array('.count($regexp_arrays).");\n";
	reset($regexp_arrays);
	while(list($key,$arr)=each($regexp_arrays)){
		$js.="regexp_arrays[\"$key\"]=new Array(".count($arr).");\n";
		for($i=0;$i<count($arr);$i++){
			$js.="regexp_arrays[\"$key\"][$i]=new Array(";
			if($arr[$i][0]==1) $js.='1,'.escape_regexp($arr[$i][2]).'g,"'.escape_regexp($arr[$i][3],true).'"';
			elseif($arr[$i][0]==2) $js.='2,'.escape_regexp($arr[$i][2])."g,{$arr[$i][3]}".(count($arr[$i])<5?null:',false');
			$js.=");\n";
		}
	}
	return stripslashes($js);
}
# end #

global $regexp_arrays;

$jsattrs='(href|src|location|backgroundImage|pluginspage|codebase|img|location\.href)';
$jshtmlattrs='(innerHTML)';
$jsmethods='(location\.replace)';
$jslochost='(location\.host(?:name){0,1})';
$jsrealpage='((?:(?:document|window)\.){0,1}location(?:(?=[^\.])|\.(?!hash|host|hostname|pathname|port|protocol|reload|search)[a-z]+)|document\.documentURI|[a-z]+\.referrer)';

$anyspace="[\t\r\n ]*";
$plusspace="[\t\r\n ]+";
$spacer="[\t ]*";
$htmlattrs='(data|href|src|background|pluginspage|codebase)';
$jsvarsect='[a-zA-Z0-9\._\[\]\+-]*';
$jsvarobj="(?:{$jsvarsect}(?:(?:\([a-zA-Z0-9\._\[\]\+\-\ ]*?\)){$jsvarsect})*)";
#$jsvarobj='(?:[a-zA-Z0-9\._\(\)\[\]\+\-]+)';
$quoteseg='(?:(?:"(?:(?:[^"]|[\\\\]")*?)")|(?:\'(?:(?:[^\']|[\\\\]\')*?)\')';
$jsquotereg="((?:(?:{$anyspace}{$quoteseg}|{$jsvarobj}){$anyspace}\+)*){$anyspace}{$quoteseg}|{$jsvarobj}){$spacer}(?=[;\}\n\r]))";
#$jsend="(?={$anyspace}[;\}\n\r\'\"])";
$jsend="(?={$anyspace}[;\}\n\r])";
$htmlnoquot='(?:[^"\'\\\\][^> ]*)';
$htmlreg="({$quoteseg}|{$htmlnoquot}))";

$js_regexp_arrays=array(
	array(1,2,"/([^a-z0-9]){$jsrealpage}([^a-z0-9=])/i",'\1proxy_current_url\3'),
	array(1,2,"/([^a-z]){$jslochost}([^a-z])/i",'\1proxy_location_hostname\3'),
	array(1,2,"/([^a-z]{$jsmethods}{$anyspace}\()([^)]*)\)/i",'\1surrogafy_url(\3))'),
	#array(1,2,"/(\.{$jsattrs}{$anyspace}=(?:(?:{$anyspace}{$jsvarobj}{$anyspace}=)*){$anyspace})({$jsquotereg}(?:\+{$jsquotereg})*){$jsend}/i",'\1surrogafy_url(\3)'),
	array(1,2,"/({$jsvarobj})\.({$jsattrs}{$anyspace}=(?:(?:{$anyspace}{$jsvarobj}{$anyspace}=)*){$anyspace})({$jsquotereg}(?:\+{$jsquotereg})*){$jsend}/i",'proxy_setAttribute("\1","\3",\4)'),
	array(1,2,"/(\.{$jshtmlattrs}{$anyspace}=(?:(?:{$anyspace}{$jsvarobj}{$anyspace}=)*){$anyspace})({$jsquotereg}(?:\+{$jsquotereg})*){$jsend}/i",'\1parse_all_html(\3)'),
	array(1,2,"/\.action({$anyspace}=(?:(?:{$anyspace}{$jsvarobj}{$anyspace}=)*){$anyspace})({$jsquotereg}(?:\+{$jsquotereg})*){$jsend}/i",'.'.COOK_PREF.'.value\1surrogafy_url(\3,false)'),
	array(1,2,"/(\.setattribute{$anyspace}\({$anyspace}(\"|'){$jsattrs}(\\2){$anyspace},{$anyspace})(.*?){$jsend}/i",'\1surrogafy_url(\5)'),
	array(1,2,"/(([^ {>\t\r\n=;]+){$anyspace}=(?:{$anyspace}new{$anyspace}|{$anyspace})XMLHttpRequest(?:\(\);|;))/i","\\1\n\\2._realopen=\\2.open;\n\\2.open=proxy_XMLHttpRequest_open;"),
	array(1,2,"/(([^ {>\t\r\n=;]+){$anyspace}=(?:{$anyspace}new{$anyspace}|{$anyspace})ActiveXObject{$anyspace}\({$anyspace}([\"'])[a-z0-9]*\.XMLHTTP\\3{$anyspace}\)[;]{0,1})/i","\\1\n\\2._realopen=\\2.open;\n\\2.open=proxy_XMLHttpRequest_open;"),
	(ENCRYPT_URLS?array(1,2,"/((?:[^\) \{\}]*(?:\)\.{0,1}))+)(\.submit{$anyspace}\(\)){$jsend}/i",'void((\1.method=="post"?null:\1\2));'):null),
);

$regexp_arrays=array(
	'text/html' => array(
		array(1,1,"/( on[a-z]{3,20}{$anyspace}={$anyspace})(?:(\"[^\"]+[^;\"])(\")|('[^']+[^;'])('))/i",'\1\2;\3'),
#.(URL_FORM?"<input type=\"hidden\" name=\"".COOK_PREF."_frame\" value=\"1\" />\n":null)
		array(1,1,"/(<form(?:(?!action)[^>])+)(>)/i","\\1".(URL_FORM?' target="_self"':null)."\\2\n<input type=\"hidden\" name=\"".COOK_PREF."\" value=\"{$curr_url}\" />\n"),
		array(1,1,"/(<form[^>]*?) action{$anyspace}={$anyspace}{$htmlreg}([^>]*>)/i","\\1".(URL_FORM?' target="_self"':null)."\\3\n<input type=\"hidden\" name=\"".COOK_PREF."\" value=\\2 />\n"),

		(ENCRYPT_URLS?array(1,1,'/(<form[^>]*?)>/i','\1 onsubmit="return proxy_form_encrypt(this);">'):null),
		(ENCRYPT_URLS?array(1,1,"/(<input[^>]*? type{$anyspace}={$anyspace}(?:\"submit\"|'submit'|submit)[^>]*?[^\/])((?:[ ]?[\/])?>)/i",'\1 onclick="proxy_form_button=this.name;"\2'):null),

		array(2,1,"/ name=\"".COOK_PREF."\" value{$anyspace}={$anyspace}{$htmlreg} \/>/i",1,false),
		array(2,1,"/<[a-z][^>]*{$plusspace}{$htmlattrs}{$anyspace}={$anyspace}{$htmlreg}/i",2),
		array(2,2,"/<script[^>]*?{$plusspace}src{$anyspace}={$anyspace}([\"']){$anyspace}(.*?[^\\\\])\\1[^>]*>{$anyspace}<\/script>/i",2),
		(URL_FORM && PAGE_FRAMED?array(1,1,"/(<a{$plusspace}[^>]*href{$anyspace}={$anyspace})(?:(?!".FRAME_LOCK_REGEXP.")({$htmlnoquot})|(\")(?!".FRAME_LOCK_REGEXP.')([^"]+)"|(\')(?!'.FRAME_LOCK_REGEXP.')([^\']+)\')/i','\1\2\3\4\5\6&'.COOK_PREF.'_frame=1\3\5'):null)
	),

	'text/css' => array(
		array(2,1,"/[^a-z]url\({$anyspace}(\"|')(.*?[^\\\\])(\\1){$anyspace}\)/i",2),
		array(2,1,"/[^a-z]url\({$anyspace}([^\"'\\\\].*?[^\\\\]){$anyspace}\)/i",1),
		array(2,1,"/@import{$plusspace}(\"|')(.*?[^\\\\])(\\1);/i",2)
	),

	'application/x-javascript' => $js_regexp_arrays,
	'text/javascript' => $js_regexp_arrays
);

## JAVASCRIPT REGEXPS ##
if($_SERVER['QUERY_STRING']=='js_regexps' || $_SERVER['QUERY_STRING']=='js_regexps_framed'){ ?>//<script>
<?php echo(convertarray_to_javascript().((!empty($_COOKIE[COOK_PREF.'_remove_objects']))?'regexp_arrays["text/html"].push(Array(1,/<[\\\\/]?(embed|param|object)[^>]*>/ig,""));':null)); ?>
//</script><?php exit(); }
## END JAVASCRIPT REGEXPS ##

# Server side only parsing
array_push($regexp_arrays['text/html'],
	array(2,1,"/<meta[^>]*{$plusspace}http-equiv{$anyspace}={$anyspace}([\"'])refresh\\1[^>]* content{$anyspace}={$anyspace}([\"'])[ 0-9\.;\t\\r\n]*url=(.*?)\\2[^>]*>/i",3),
	array(2,1,"/<meta[^>]*{$plusspace}http-equiv{$anyspace}={$anyspace}refresh [^>]*content{$anyspace}={$anyspace}([\"'])[ 0-9\.;\t\\r\n]*url=(.*?)\\1[^>]*>/i",2),
	array(1,1,"/(<meta[^>]*{$plusspace}http-equiv{$anyspace}={$anyspace}([\"'])set-cookie\\2[^>]* content{$anyspace}={$anyspace})([\"'])(.*?[^\\\\]){$anyspace}\\3/i",'\1\3'.PAGECOOK_PREFIX.'\4\3'),
	array(1,1,"/(<meta[^>]*{$plusspace}http-equiv{$anyspace}={$anyspace}set-cookie[^>]* content{$anyspace}={$anyspace})([\"'])(.*?[^\\\\]){$anyspace}\\2/i",'\1\2'.PAGECOOK_PREFIX.'\3\2')
);

## END REGEXPS ##

## PROXY FUNCTIONS ##

# class for URL
define('URLREG','/^'.
	'(?:([a-z]*)?(?:\:?\/\/))'.		# proto
	'(?:([^\@\/]*)\@)?'.			# userpass
	'([^\/:\?\#\&]*)'.			# servername
	'(?:\:([0-9]+))?'.			# portval
	'(\/[^\&\?\#]*?)?'.			# path
	'([^\/\?\#\&]*(?:\&[^\?\#]*)?)'.	# file
	'(?:\?(.*?))?'.				# query
	'(?:\#(.*))?'.				# label
'$/ix');

class aurl{
	var $url,$topurl,$locked;
	var $proto,$userpass,$servername,$portval,$path,$file,$query,$label;

	function aurl($url,$topurl=null){
		$this->url=preg_replace('/&#([0-9]+);/e','chr(\1)',trim(str_replace('&amp;','&',str_replace("\r",null,str_replace("\n",null,$url)))));
		$this->topurl=$topurl;
		$this->locked=(preg_match("/^".AURL_LOCK_REGEXP."/i",$url)?true:false); #*

		if($this->locked) return;

		$urlwasvalid=true;
		if(!preg_match(URLREG,$this->url)){
			$urlwasvalid=false;
			if($this->topurl==null) $this->url='http://'.(($this->url{0}==':' || $this->url{0}=='/')?substr($this->url,1):$this->url).(strpos($this->url,'/')!==false?null:'/');
			else{
				$newurl=$this->topurl->get_proto().$this->get_fieldreq(2,$this->topurl->get_userpass()).$this->topurl->get_servername().(($this->topurl->get_portval()!=80 && ($this->topurl->get_proto()=='https'?$this->topurl->get_portval()!=443:true))?':'.$this->topurl->get_portval():null);
				if(substr($this->url,0,1)!='/') $newurl.=$this->topurl->get_path();
				$this->url=$newurl.$this->url;
			}
		}

		$this->set_proto(($urlwasvalid || $this->topurl==null?preg_replace('/^([^:]+).*$/','\1',$this->url):$this->topurl->get_proto()));
		$this->set_userpass(preg_replace(URLREG,'\2',$this->url));
		$this->set_servername(preg_replace(URLREG,'\3',$this->url));
		$this->set_portval(preg_replace(URLREG,'\4',$this->url));
		$this->set_path(preg_replace(URLREG,'\5',$this->url));
		$this->set_file(preg_replace(URLREG,'\6',$this->url));
		$this->set_query(preg_replace(URLREG,'\7',$this->url));
		$this->set_label(preg_replace(URLREG,'\8',$this->url));

		if(!$this->locked && !preg_match(URLREG,$this->url)) havok(7,$this->url); #*
	}

	function get_fieldreq($fieldno,$value){
		$fieldreqs=array(2 => '://'.($value!=null?"$value@":null), 4 => ($value!=null && intval($value)!=80?':'.intval($value):null), 7 => ($value!=null?"?$value":null), 8 => ($value!=null?"#$value":null));
		if(!array_key_exists($fieldno,$fieldreqs)) return (empty($value)?null:$value);
		else return $fieldreqs[$fieldno];
	}

	function set_proto($proto='http'){ if($this->locked) return; $this->proto=$proto; }
	function get_proto(){ return $this->proto; }
	function get_userpass(){ return $this->userpass; }
	function set_userpass($userpass=null){ $this->userpass=$userpass; }
	function get_servername(){ return $this->servername; }
	function set_servername($servername=null){ $this->servername=$servername; }
	function get_portval(){ return (empty($this->portval)?($this->get_proto()=='https'?'443':'80'):$this->portval); }
	function set_portval($port=null){ $this->portval=strval((intval($port)!=80)?$port:null); }
	function get_path(){
		if(strpos($this->path,'/../')!==false) $this->path=preg_replace('/(?:\/[^\/]+){0,1}\/\.\.\//','/',$this->path);
		if(strpos($this->path,'/./')!==false) while(($path=str_replace('/./','/',$this->path)) && $path!=$this->path) $this->path=$path;
		return $this->path;
	}
	function set_path($path=null){ $this->path=(empty($path)?'/':$path); }
	function get_file(){ return $this->file; }
	function set_file($file=null){ $this->file=$file; }
	function get_query(){ return $this->query; }
	function set_query($query=null){ $this->query=$query; }
	function get_label(){ return $this->label; }
	function set_label($label=null){ $this->label=$label; }

	function get_url(){
		if($this->locked) return $this->url;
		return $this->get_proto().'://'.
		       ($this->get_userpass()==null?null:$this->get_userpass().'@').
		       $this->get_servername().
		       (intval($this->get_portval())==80?null:':'.intval($this->get_portval())).
		       $this->get_path().$this->get_file().
		       ($this->get_query()==null?null:'?'.$this->get_query()).
		       ($this->get_label()==null?null:'#'.$this->get_label())
		;
	}

	function surrogafy(){
		$url=$this->get_url();
		if($this->locked || $this->get_proto().$this->get_fieldreq(2,$this->get_userpass()).$this->get_servername().$this->get_path().$this->get_file()==THIS_SCRIPT) return $url;
		$label=$this->get_label();
		$this->set_label();
		if(ENCRYPT_URLS && !$this->locked) $url=proxenc($url);
		$url=THIS_SCRIPT.'?'.COOK_PREF.'='.(!ENCRYPT_URLS?urlencode($url):$url);
		$this->set_label($label);
		return $url;
	}
}

function surrogafy_url($url,$topurl=false,$addproxy=true){
	global $curr_urlobj;
	if(preg_match('/^(["\']).*\1$/is',$url)>0){ #*
		$urlquote=substr($url,0,1);
		$url=substr($url,1,strlen($url)-2);
	}
	if($topurl===false) $topurl=$curr_urlobj;
	$urlobj=new aurl($url,$topurl);
	$new_url=($addproxy?$urlobj->surrogafy():$urlobj->get_url());
	if(!empty($urlquote)) $new_url=$urlquote.$new_url.$urlquote;
	return $new_url;
}

function proxenc($url){
	if(substr($url,0,1)=='~' || strtolower(substr($url,0,3))=='%7e') return $url;
	$url=urlencode($url);
	$new_url=null;
	for($i=0;$i<strlen($url);$i++){
		$char=ord(substr($url,$i,1));
		$char+=ord(substr(SESS_PREF,$i%strlen(SESS_PREF),1));
		while($char>126) $char-=94;
		$new_url.=chr($char);
	}
	#return '~'.base64_encode($new_url);
	return '~'.urlencode(base64_encode($new_url));
}

function header_value_arr($headername){
	global $headers;
	$linearr=explode("\n",$headers);
	$hvalsarr=preg_grep("/$headername\: ([^\r\n]*)/i",$linearr); #*
	return array_values(preg_replace("/$headername\: ([^\r\n]*)/i",'\1',$hvalsarr));
}

function header_value($headername){
	$arr=header_value_arr($headername);
	return $arr[0];
}

function havok($errorno,$arg1=null,$arg2=null,$arg3=null){
	global $curr_url;
	$url=$curr_url;
	switch($errorno){
		case 1:
			$et='Bad IP Address';
			$ed="The IP address given ({$arg2}) is an impossible IP address, or the domain given ({$arg1}) was resolved to an impossible IP address.";
			break;
		case 2:
			$et='Address is Blocked';
			$ed="The administrator of this proxy service has decided to block this address, domain, or subnet.\n<br /><br />\nDomain: {$arg1}\n<br />\nAddress: {$arg2}";
			break;
		case 3:
			$et='Could Not Resolve Domain';
			$ed="The domain of the URL given ({$arg1}) could not be resolved due to DNS issues or an errorneous domain name.";
			break;
		case 4:
			$et='Bad Filters';
			$ed='The administrator of this proxy has incorrectly configured his domain filters, or a domain given could not be resolved.';
			break;
		case 5:
			$et='Domain is Blocked';
			$ed='The administrator of this proxy has decided to block this domain.';
			break;
		case 6:
			$et='Could Not Connect to Server';
			$ed="An error has occurred while attempting to connect to \"{$arg1}\" on port \"{$arg2}\".";
			break;
		case 7:
			$et='Invalid URL';
			$ed='The URL below was detected to be an invalid URL.';
			$url=$arg1;
			break;
	}
	$ed.="\n<br /><br />\nURL:&nbsp;{$url}";
?>
<div style="font-family: bitstream vera sans, trebuchet ms"><div style="border: 3px solid #FFFFFF; padding: 2px">
	<div style="float: left; border: 1px solid #602020; padding: 1px; background-color: #FFFFFF">
	<div style="float: left; background-color: #801010; color: #FFFFFF; font-weight: bold; font-size: 54px; padding: 2px; padding-left: 12px; padding-right: 12px">!</div>
	</div>
	<div style="float: left; width: 500px; padding-left: 20px">
		<div style="border-bottom: 1px solid #000000; font-size: 12pt; text-align: center; font-weight: bold; padding: 2px">Error: <?php echo($et); ?></div>
		<div style="padding: 6px"><?php echo($ed); ?></div>
	</div>
</div></div>
<?php exit(); }

function ipbitter($ipaddr){
	$ipsplit=explode('.',$ipaddr);
	for($i=0;$i<count($ipsplit);$i++){
		$ipsplit[$i]=decbin($ipsplit[$i]);
		$ipsplit[$i]=str_repeat('0',8-strlen($ipsplit[$i])).$ipsplit[$i];
	}
	return implode(null,$ipsplit);
}

function ipcompare($iprange,$ip){
	$iprarr=split('/',$iprange);
	$ipaddr=$iprarr[0];
	$mask=$iprarr[1];
	$maskbits=str_repeat('1',32-$mask).str_repeat('0',$mask);
	$ipbits=ipbitter($ipaddr);
	$ipbits2=ipbitter($ip);
	return (($ipbits & $maskbits)==($ipbits2 & $maskbits));
}


function ip_check($ip,$mask=false){
	$ipseg='(?:[01]?[0-9]{1,2}|2(?:5[0-5]|[0-4][0-9]))';
	return preg_match("/^(?:$ipseg\.){3}$ipseg".($mask?'\/[0-9]{1,2}':null).'$/i',$ip); #*
}

function get_check($address){
	global $blocked_addresses;
	if(strrchr($address,'/')) $address=substr(strrchr($address,'/'),1);
	$ipc=ip_check($address);
	$addressip=(ip_check($address)?$address:gethostbyname($address));
	if(!ip_check($addressip)) havok(1,$address,$addressip);
	foreach($blocked_addresses as $badd){
		if(!$ipc) if(strlen($badd)<=strlen($address) && substr($address,strlen($address)-strlen($badd),strlen($badd))==$badd) havok(5);
		if($badd==$addressip) havok(2,$address,$addressip);
		elseif(ip_check($badd,true)){ if(ipcompare($badd,$addressip)) havok(2,$address,$addressip); }
		else{
			$baddip=gethostbyname($badd);
			if(empty($baddip)) havok(4);
			if($baddip==$addressip) havok(2,$address,$addressip);
		}
	}
	return true;
}

function httpclean($str){ return preg_replace('/([^":-_\.0-9a-z])/e','\'%\'.(strlen(dechex(ord(\'\1\')))==1?\'0\':null).strtoupper(dechex(ord(\'\1\')))',$str); } #*

function getpage($url){
	global $headers,$out,$post_vars,$proxy_variables,$referer;

	$urlobj=new aurl($url);
	$query=$urlobj->get_query();
	$requrl=$urlobj->get_path().$urlobj->get_file().(!empty($query)?"?$query":null);

	$http_auth=null;
	if(extension_loaded('apache')){
		$fail=false;
		$cheaders=getallheaders();
		$http_auth=$reqarray['Authorization'];
	}
	else $fail=true;

	$authorization=($fail?$_SERVER['HTTP_AUTHORIZATION']:$cheaders['Authorization']);
	$cache_control=($fail?$_SERVER['HTTP_CACHE_CONTROL']:$cheaders['Cache-Control']);
	$if_modified=($fail?$_SERVER['HTTP_IF_MODIFIED_SINCE']:$cheaders['If-Modified-Since']);
	$if_none_match=($fail?$_SERVER['HTTP_IF_NONE_MATCH']:$cheaders['If-None-Match']);

	if($fail){
		if(!empty($authorization)) $http_auth=$authorization;
		elseif(!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW']))
			$http_auth='Basic '.base64_encode("{$_SERVER['PHP_AUTH_USER']}:{$_SERVER['PHP_AUTH_PW']}");
		elseif(!empty($_SERVER['PHP_AUTH_DIGEST'])) $http_auth="Digest {$_SERVER['PHP_AUTH_DIGEST']}";
	}

	if(PIP!=null && PPORT!=null){
		$servername=PIP;
		$portval=PPORT;
		$requrl=$urlobj->get_url();
	}
	else{
		$servername=($urlobj->get_proto()=='ssl' || $urlobj->get_proto()=='https'?'ssl://':null).$urlobj->get_servername();
		$portval=$urlobj->get_portval();
	}
	get_check($servername);

	$out="{$_SERVER['REQUEST_METHOD']} ".str_replace(' ','%20',$requrl)." HTTP/1.1\r\nHost: ".$urlobj->get_servername().(($portval!=80 && ($urlobj->get_proto()=='https'?$portval!=443:true))?":$portval":null)."\r\n";

	if($_COOKIE[COOK_PREF.'_useragent']!='-1'){
		$useragent=$_COOKIE[COOK_PREF.'_useragent'];
		if(empty($useragent)) $useragent=$_SERVER['HTTP_USER_AGENT'];
		$useragent_cook=($useragent==1?$_COOKIE[COOK_PREF.'_useragenttext']:$useragent);
		if(!empty($useragent_cook)) $out.="User-Agent: $useragent_cook\r\n";
	}
	if(!empty($http_auth)) $out.="Authorization: $http_auth\r\n";

	if(empty($_COOKIE[COOK_PREF.'_remove_referer']) && !empty($referer)) $out.='Referer: '.str_replace(' ','+',$referer)."\r\n";
	if($_SERVER['REQUEST_METHOD']=='POST') $out.='Content-Length: '.strlen($post_vars)."\r\nContent-Type: application/x-www-form-urlencoded\r\n";

	$cook_prefdomain=preg_replace('/^www\./i',null,$urlobj->get_servername()); #*
	$cook_prefix=str_replace('.','_',$cook_prefdomain).COOKIE_SEPARATOR;
	if(count($_COOKIE)>0 && empty($_COOKIE[COOK_PREF.'_remove_cookies'])){
		$addtoout=null;
		reset($_COOKIE);
		while(list($key,$val)=each($_COOKIE)){
			if(ENCRYPT_COOKS){
				$key=proxdec($key);
				$val=proxdec($val); #urldecode($val));
			}
			if(str_replace(COOKIE_SEPARATOR,null,$key)==$key) continue;
			#$cook_domain=preg_replace('/^(.*'.COOKIE_SEPARATOR.').*$/','\1',$key); #**
			$cook_domain=substr($key,0,strpos($key,COOKIE_SEPARATOR)).COOKIE_SEPARATOR;
			if(substr($cook_prefix,strlen($cook_prefix)-strlen($cook_domain),strlen($cook_domain))!=$cook_domain) continue;
			$key=substr($key,strlen($cook_domain),strlen($key)-strlen($cook_domain));
			if(!in_array($key,$proxy_variables)) $addtoout.=" $key=$val;";
		}
		if(!empty($addtoout)){
			$addtoout.="\r\n";
			$out.="Cookie:{$addtoout}";
		}
	}

	$out.="Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5\r\n".
	      "Accept-Language: en-us,en;q=0.5\r\n".
	      "Accept-Encoding: gzip\r\n".
	      "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n".  /*/
	      "Keep-Alive: 300\r\n".
	      "Connection: keep-alive\r\n".                          /*/
	      "Connection: close\r\n".                               //*/
	      ($cache_control!=null?"Cache-Control: $cache_control\r\n":null).
	      ($if_modified!=null?"If-Modified-Since: $if_modified\r\n":null).
	      ($if_none_match!=null?"If-None-Match: $if_none_match\r\n":null).
	      "\r\n{$post_vars}"
	;

	// This part ignores any "SSL: fatal protocol error" errors, and makes sure other errors are still triggered correctly
	function errorHandle($errno,$errmsg){
		if($errno<=E_PARSE && ($errno!=E_WARNING || substr($errmsg,strlen($errmsg)-25,25)!='SSL: fatal protocol error')){
			restore_error_handler();
			trigger_error($errmsg,$errno<<8);
			set_error_handler('errorHandle');
		}
	}
	set_error_handler('errorHandle');

	$fp=@fsockopen($servername,$portval,$errno,$errval,5) or havok(6,$servername,$portval);
	stream_set_timeout($fp,5);
	$ub=stream_get_meta_data($fp);
	$ub=$ub['unread_bytes'];
	if($ub>0) fread($fp,$ub);
	fwrite($fp,$out);

	function get_byte($fp){
		$byte=fread($fp,1);
		return ($byte=="\r"?get_byte($fp):$byte);
	}

	$response='100';
	while($response=='100'){
		$responseline=fread($fp,12);
		$response=substr($responseline,-3,3);

		$headers=$responseline.fread($fp,1);
		while(true){
			$chunk=null;
			$byte=get_byte($fp);
			if($byte=="\n") break;
			while($byte!="\n"){
				$chunk.=$byte;
				$byte=get_byte($fp);
			}
			$headers.=$chunk.$byte;
		}
	}

	#if(header_value('Pragma')==null) header('Pragma: public');
	#if(header_value('Cache-Control')==null) header('Cache-Control: public');
	#if(header_value('Last-Modified')==null && header_value('Expires')==null) header('Expires: '.date('D, d M Y H:i:s e',time()+86400));

	if(empty($_COOKIE[COOK_PREF.'_remove_cookies'])){
		$setcookiearr=header_value_arr('Set-Cookie');
		for($i=0;$i<count($setcookiearr);$i++){
			$thiscook=explode('=',$setcookiearr[$i],2);
			$cook_val=substr($thiscook[1],0,strpos($thiscook[1],';'));
			$cook_domain=preg_replace('/^.*domain=[	 ]*\.?([^;]+).*?$/i','\1',$thiscook[1]); #*
			if($cook_domain==$thiscook[1]) $cook_domain=$cook_prefdomain;
			elseif(substr($cook_prefdomain,strlen($cook_prefdomain)-strlen($cook_domain),strlen($cook_domain))!=$cook_domain) continue;
			$cook_name=str_replace('.','_',$cook_domain).COOKIE_SEPARATOR.$thiscook[0];
			if(ENCRYPT_COOKS){
				$cook_name=proxenc($cook_name);
				$cook_val=proxenc($cook_val);
			}
			dosetcookie($cook_name,$cook_val);
		}
	}

	if(substr($response,0,2)=='30' && $response{2}!='4'){
		$urlobj=new aurl($url);
		$redirurl=surrogafy_url(header_value('Location'),$urlobj);

		fclose($fp);
		restore_error_handler();

		header("Location: $redirurl");
		exit();
	}

	$oheaders=preg_replace("/[\r\n](?:Location|Content-Length|Content-Encoding|Set-Cookie|Transfer-Encoding|Connection|Keep-Alive|Pragma|Cache-Control|Expires)\: .*/i",null,$headers); #*
	$ohsplit=explode("\n",$oheaders);
	foreach($ohsplit as $header) if(!empty($header)) header($header);
	unset($oheaders,$ohsplit);
	#header('Status: '.$response);

	if(substr(header_value('Content-Type'),0,4)=='text' || substr(header_value('Content-Type'),0,24)=='application/x-javascript'){
		$justoutput=false;
		$justoutputnow=false;
	}
	else{
		$justoutputnow=(header_value('Content-Encoding')=='gzip'?false:true);
		$justoutput=true;
	}

	if(header_value('Transfer-Encoding')=='chunked'){
		$body=null;
		$chunksize=null;
		while($chunksize!==0){
			$byte=null;
			$chunk=null;
			while($byte!="\r"){
				$chunk.=$byte;
				$byte=fread($fp,1);
			}
			fread($fp,1);
			$chunksize=intval($chunk,16);
			$bufsize=$chunksize;
			while($bufsize>=1){
				$subchunk=fread($fp,$bufsize);
				if($justoutputnow) echo $subchunk;
				else $body.=$subchunk;
				$bufsize-=strlen($subchunk);
			}
			fread($fp,2);
		}
	}

	// for
	/*elseif(header_value('Content-Length')!=null){
		$conlen=header_value('Content-Length');
		$body=null;
		for($i=0;$i<$conlen;$i+=$read){
			$read=($conlen-$i<1024?$conlen-$i:1024);
			$byte=fread($fp,$read);
			if($justoutputnow) echo $byte;
			else $body.=$byte;
		}
	}*/

	else{
		$body=null;
		while(true){
			$chunk=fread($fp,1024);
			if($justoutputnow) echo $chunk;
			else $body.=$chunk;
			if(empty($chunk)) break;
		}
	}

	fclose($fp);
	restore_error_handler();

	if(header_value('Content-Encoding')=='gzip') $body=gzinflate(substr($body,10));
	#die('REQ: '.$out.'<br /><br />HEADERS: '.$headers.'<br /><br />Body: '.$body);
	if($justoutput){
		if(!$justoutputnow) echo $body;
		exit();
	}
	return array($body,$url,$cook_prefix);

}
## END PROXY FUNCTIONS#

## BEGIN PROXY CODE #

# Deal with cookies for proxy #
global $proxy_variables,$proxy_varblacklist,$post_vars,$cookies,$curr_url,$curr_urlobj,$referer,$blocked_addresses;

$curr_url=proxdec($postandget[COOK_PREF]);
#$curr_url=urldecode($postandget[COOK_PREF]);
#while(strpos($curr_url,'%')!==false) $curr_url=urldecode($curr_url);
#$curr_url=stripslashes($curr_url);

$proxy_variables=array(COOK_PREF,COOK_PREF.'_pip',COOK_PREF.'_pport',COOK_PREF.'_useragent',COOK_PREF.'_useragenttext',COOK_PREF.'_url_form',COOK_PREF.'_remove_cookies',COOK_PREF.'_remove_referer',COOK_PREF.'_remove_scripts',COOK_PREF.'_remove_objects',COOK_PREF.'_encrypt_urls',COOK_PREF.'_encrypt_cooks',COOK_PREF.'_frame',COOK_PREF.'_framed');
$proxy_varblacklist=array(COOK_PREF,COOK_PREF);

if($postandget[COOK_PREF.'_set_values']){
	if($postandget[COOK_PREF.'_useragent']!='1'){
		unset($postandget[COOK_PREF.'_useragenttext']);
		dosetcookie(COOK_PREF.'_useragenttext',false,0);
	}
	while(list($key,$val)=each($proxy_variables)){
		if(!in_array($val,$proxy_varblacklist)){
			dosetcookie($val,false,0);
			if(isset($postandget[$val]) && !empty($postandget[$val])) dosetcookie($val,$postandget[$val]);
		}
	}
	$theurl=surrogafy_url(proxdec($postandget[COOK_PREF]));
	#$theurl=surrogafy_url((ENCRYPT_URLS?proxdec($theurl):$theurl),null);
	header("Location: $theurl".(!empty($postandget[COOK_PREF.'_url_form'])?'&'.COOK_PREF.'_frame=1':null));
	exit();
}
# end #

define('PIP',(FORCE_DEFAULT_TUNNEL?$_COOKIE[COOK_PREF.'_pip']:DEFAULT_TUNNEL_PIP));
define('PPORT',intval(FORCE_DEFAULT_TUNNEL?$_COOKIE[COOK_PREF.'_pport']:DEFAULT_TUNNEL_PPORT));

# Deal with GET/POST/COOKIES and the URL #
define('ENCRYPT_COOKS',!empty($_COOKIE[COOK_PREF.'_encrypt_cooks']));
#if(ENCRYPT_URLS) $curr_url=proxdec($curr_url);
$referer=proxdec(preg_replace('/^([^\?]*)(\?'.COOK_PREF.'=)?/i',null,$_SERVER['HTTP_REFERER'])); #*

$getkeys=array_keys($_GET);
foreach($getkeys as $getvar){ if(!in_array($getvar,$proxy_variables)){ $curr_url.=(strpos($curr_url,'?')===false?'?':'&')."$getvar=".urlencode($_GET[$getvar]); } }

$post_vars=null;
$postkeys=array_keys($_POST);
foreach($postkeys as $postkey){
	if(!in_array($postkey,$proxy_variables)){
		if(!is_array($_POST[$postkey]))
			$post_vars.=($post_vars!=null?'&':null).httpclean($postkey).'='.httpclean($_POST[$postkey]);
		else{
			foreach($_POST[$postkey] as $postval)
				$post_vars.=($post_vars!=null?'&':null).httpclean($postkey).'%5B%5D='.httpclean($postval);
		}
	}
}
# end #

# Get the page #
$pagestuff=getpage($curr_url);
$body=$pagestuff[0];

# For AJAX, some things quote the entire HTML of a page... this makes sure it doesn't parse inside of that
if(preg_match("/^[\t\r\n ]*([\"']).*\\1[\t\r\n ]*$/i",$body)>0){ #*
	echo $body;
	exit();
}

$curr_url=$pagestuff[1];
define('PAGECOOK_PREFIX',$pagestuff[2]);
unset($pagestuff);
define('CONTENT_TYPE',preg_replace('/^([a-z0-9\-\/]+).*$/i','\1',header_value('Content-Type'))); #*
# end #


## Got the Page, Now Parse The Body ##
$base=preg_replace("/^.*<base[^>]* href$anyspace=$anyspace{$htmlreg}[^>]*>.*$/is",'\1',$body);
$body=preg_replace("/<base[^>]* href$anyspace=$anyspace{$htmlreg}[^>]*>/i",null,$body);
if(!empty($base) && $base!=$body && !empty($base{100})){
	if(preg_match('/^(["\']).*\1$/i',$base)>0) $base=substr($base,1,strlen($base)-2); #*
	$curr_url=$base;
}

$curr_urlobj=new aurl($curr_url);


# Parsing Functions #

function parse_html($regexp,$partoparse,$html,$addproxy){
	global $curr_urlobj;
	$newhtml=null;
	while(preg_match($regexp,$html,$matcharr,PREG_OFFSET_CAPTURE)){
		$nurl=surrogafy_url($matcharr[$partoparse][0],$curr_urlobj,$addproxy);
		#$begin=$matcharr[$partoparse][1];
		#$len=strlen($matcharr[$partoparse][0]);
		#$end=$matcharr[$partoparse][1]+$len;
		#$newhtml.=substr($html,0,$begin).str_replace($matcharr[$partoparse][0],$nurl,substr($html,$begin,$len));
		#$html=substr($html,$end,strlen($html)-$end);
		$begin=$matcharr[$partoparse][1];
		$end=$matcharr[$partoparse][1]+strlen($matcharr[$partoparse][0]);
		$newhtml.=substr_replace($html,$nurl,$begin);
		#$newhtml.=substr($html,0,$begin).str_replace($matcharr[$partoparse][0],$nurl,substr($html,$begin,$len));
		$html=substr($html,$end,strlen($html)-$end);

	}
	$newhtml.=$html;
	return $newhtml;
}

function regular_express($regexp_array,$thevar){
	$regexp_array[2].='S';
	if($regexp_array[0]==1) $thevar=preg_replace($regexp_array[2],$regexp_array[3],$thevar);
	elseif($regexp_array[0]==2){
		$addproxy=((count($regexp_array)<5)?true:false);
		$thevar=parse_html($regexp_array[2],$regexp_array[3],$thevar,$addproxy);
	}
	return $thevar;
}

function parse_all_html($html){
	global $regexp_arrays;
	if(CONTENT_TYPE!='text/html'){
		for(reset($regexp_arrays);list($key,$arr)=each($regexp_arrays);) if($key==CONTENT_TYPE) foreach($arr as $regarr) $html=regular_express($regarr,$html);
		return $html;
	}
	if(!empty($_COOKIE[COOK_PREF.'_remove_scripts'])) $splitarr=array($html);
	else $splitarr=preg_split('/(<!--.*?-->|<style.*?<\/style>|<script.*?<\/script>)/is',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
	unset($html);
	for(reset($regexp_arrays);list($key,$arr)=each($regexp_arrays);){
		if($key=='text/javascript') continue;
		foreach($arr as $regexp_array){
			for($i=0;$i<count($splitarr);$i+=1){
				if($regexp_array[1]==2 && $i%2==0){
					$splitarr2=preg_split('/( on[a-z]{3,20}=(?:"(?:[^"]+)"|\'(?:[^\']+)\'|[^"\' >][^ >]+[^"\' >]))/is',$splitarr[$i],-1,PREG_SPLIT_DELIM_CAPTURE);
					if(count($splitarr2)<2) $splitarr[$i]=regular_express($regexp_array,$splitarr[$i]);
					else{
						for($j=1;$j<count($splitarr2);$j+=2) $splitarr2[$j]=regular_express($regexp_array,$splitarr2[$j]);
						$splitarr[$i]=implode(null,$splitarr2);
					}
					unset($splitarr2);
				}
				elseif(($regexp_array[1]==1 && $i%2==0) || (substr($splitarr[$i],0,7)=='<script' && $regexp_array[1]==2) || (substr($splitarr[$i],0,6)=='<style' && $key=='text/css'))
					$splitarr[$i]=regular_express($regexp_array,$splitarr[$i]);
			}
		}
	}
	return implode(null,$splitarr);
}

if(CONTENT_TYPE=='text/html'){
	if(!empty($_COOKIE[COOK_PREF.'_remove_scripts'])){
		$body=preg_replace('/<(.?)noscript>/si',null,$body); #*
		$body=preg_replace('/<script.+?<\/script>/si',null,$body);
		$body=preg_replace('/ on[a-z]{3,20}=(?:"[^"]*"|\'[^\']*\'|[^\'" ][^ ]*)/i',null,$body);
	}

	if(!empty($_COOKIE[COOK_PREF.'_remove_objects'])){
		$body=preg_replace('/<embed.*?<\/embed>/si',null,$body);
		$body=preg_replace('/<object.*?<\/object>/si',null,$body);
	}
}

$body=parse_all_html($body);
# end #


if(CONTENT_TYPE=='text/html'){
# Insert the code's headers/Javascript #
	$urlobj=new aurl($curr_url);
	$big_headers='<meta name="robots" content="noindex, nofollow" />'.
			(URL_FORM && PAGE_FRAMED?'<base target="_top">':null).
			'<link rel="icon" href="'.surrogafy_url($urlobj->get_proto().'://'.($urlobj->get_servername()).'/favicon.ico').'" />'.
			(empty($_COOKIE[COOK_PREF.'_remove_scripts'])?
				'<script type="text/javascript" src="'.THIS_SCRIPT.'?js_funcs"></script>'.
				'<script type="text/javascript" src="'.THIS_SCRIPT.'?js_regexps'.(PAGE_FRAMED?'_framed':null).'"></script>'.
				'<script language="javascript">'.
				//'<!--'.
				'document.proxy_current_url=proxy_current_url="'.str_replace('"','\\"',$urlobj->get_url()).'";'.
				'document.proxy_this_script=proxy_this_script="'.THIS_SCRIPT.'";'.
				'document.proxy_location_hostname=proxy_location_hostname="'.str_replace('"','\\"',$urlobj->get_servername()).'";'.
				'document.proxy_location_port=proxy_location_port="'.str_replace('"','\\"',$urlobj->get_portval()).'";'.
				'document.proxy_encrypt_urls=proxy_encrypt_urls='.(ENCRYPT_URLS?'true':'false').';'.
				'document.proxy_url_form=proxy_url_form='.(URL_FORM?'true':'false').';'.
				'document.proxy_form_button=proxy_form_button=null;'.
				'curr_urlobj=new aurl(proxy_current_url);'.
				(URL_FORM?'if(parent==top){ window.addEventListener("load",function(){ parent.document.title=document.title; },false);'.
					'setParentURL(proxy_current_url); }':null).
				//'//-->'.
				'</script>'
			:null)
	;

	if(preg_match('/<head/i',$body)) $body=preg_replace('/(<head[^>]*>)/i',"\\1$big_headers",$body,1);
	elseif(preg_match('/<\/head/i',$body)) $body=preg_replace('/<\/head/i',"$big_headers</head",$body,1);
	elseif(preg_match('/<body/i',$body)) $body=preg_replace('/(<body[^>]*>)/i',"\\1$big_headers",$body,1);
	elseif(preg_match('/<html/i',$body)) $body=preg_replace('/(<html[^>]*>)/i',"\\1$big_headers",$body,1);
	elseif(preg_match('/<script/i',$body)) $body=preg_replace('/<script/i',"$big_headers<script",$body,1); #*
	else $body=$big_headers.$body;
	/*$i=0;
	while($newbody!=$body){
		if($i>0) $body=$newbody; $i++;
		$newbody=preg_replace('/([^;>])(<\/script>)/i',"\\1alert($i);\\2",$body,1);
	}*/
# end #
}

## Retrieved, Parsed, All Ready to Output ##
echo $body;

## THE END ## ?>
