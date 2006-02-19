<?

#
# Surrogafier v0.7.5b
#
# Author: Brad Cable
# License: GPL Version 2
#

define("VERSION","0.7.5b");
define("COOKIE_SEPARATOR","__surrogafier_sep__");

define("THIS_SCRIPT","http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}");


# Randomized cookie prefixes #
function gen_cookpref(){
	$chars="";
	for($i=0;$i<12;$i++){
		$char=rand(0,25);
		$char=chr($char+97);
		$chars.=$char;
	}
	return $chars;
}

if(empty($_COOKIE['user'])){
	$cookpref=gen_cookpref();
	setcookie("user",$cookpref);
}
else $cookpref=$_COOKIE['user'];
define("COOK_PREF",$cookpref);
# end #

$js_proxenc="function proxenc_url(url){
	if(url.substring(0,1)==\"~\" || url.substring(0,3).toLowerCase()==\"%7e\") return url;
	new_url=\"\";
	for(i=0;i<url.length;i++){
		char=String.charCodeAt(url.substring(i,i+1));
		char+=String.charCodeAt(\"".COOK_PREF."\".substring(i%\"".COOK_PREF."\".length,(i%\"".COOK_PREF."\".length)+1));
		while(char>126) char-=94;
		new_url+=String.fromCharCode(char);
	}
	return encodeURIComponent(\"~\"+btoa(new_url));
}";

$postandget=array_merge($_GET,$_POST);
if(!isset($postandget[COOK_PREF.'_url'])){

## First Page Displayed When Accessing the Proxy ##

$useragent_array=array(
	array(""," [ Don't Send ] "),
	array("Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8) Gecko/20051111 Firefox/1.5","Windows XP / Firefox 1.5"),
	array("Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)","Windows XP / Internet Explorer 6"),
	array("Opera/8.51 (Windows NT 5.1; U; en)","Windows XP / Opera 8.51"),
	array("Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8) Gecko/20051111 Firefox/1.5","Linux / Firefox 1.5"),
	array("Opera/8.51 (X11; Linux i686; U; en)","Linux / Opera 8.51"),
	array("Mozilla/5.0 (compatible; Konqueror/3.4; Linux) KHTML/3.4.2 (like Gecko)","Linux / Konqueror 3.4.2"),
	array("Links (2.1pre18; Linux 2.6.14.5 i686; 180x58)","Linux / Links (2.1pre18)"),
	array("Dillo/0.8.5","Any / Dillo 0.8.5"),
	array("Wget/1.10.2","Any / Wget 1.10.2"),
	array("Lynx/2.8rel5","Any / Lynx 2.8rel.5"),
	array("1"," [ Custom ] ")
);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
<head>
<title>Surrogafier</title>
<style>
	body{font-family: bitstream vera sans, arial}
	input{border: 1px solid #000000}
	select{border: 1px solid #000000}
	a{color: #000000}
	a:hover{text-decoration: none}
</style>
<script language="javascript">
<!--
<?=$js_proxenc?>
//-->
</script>
</head>
<body onload="document.getElementById('url').focus();">
<div style="font-size: 18pt; font-weight: bold; text-align: center; margin-bottom: 5px">Surrogafier</div>
<center>
<form method="post" onsubmit="if(this.<?=COOK_PREF?>_encode_urls.checked) this.<?=COOK_PREF?>_url.value=proxenc_url(document.getElementById('url').value);">
<input type="hidden" name="<?=COOK_PREF?>_url" />
<input type="hidden" name="<?=COOK_PREF?>_set_values" value="1" />
<table>
<tr>
	<td>URL:</td>
	<td><input type="text" id="url" style="width: 230px" /></td>
</tr>
<tr>
	<td>Proxy Server:</td>
	<td><table cellspacing="0" cellpadding="0">
	<tr>
		<td><input type="text" name="<?=COOK_PREF?>_pip" style="width: 180px" value="<?=($_COOKIE[COOK_PREF.'_pip'])?>" /></td>
		<td style="width: 5px">&nbsp;</td>
		<td><input type="text" name="<?=COOK_PREF?>_pport" maxlength="5" size="5" style="width: 45px" value="<?=($_COOKIE[COOK_PREF.'_pport'])?>" /></td>
	</tr>
	</table></td>
</tr>
<tr>
	<td>User-Agent:</td>
	<td><select name="<?=COOK_PREF?>_useragent" style="width: 230px" onchange="if(this.value=='1'){ document.getElementById('useragent_texttr').style.display='table-row'; document.getElementById('<?=COOK_PREF?>_useragenttext').focus(); } else document.getElementById('useragent_texttr').style.display='none';">
<? foreach($useragent_array as $useragent){ ?>
		<option value="<?=($useragent[0])?>"<? if($_COOKIE[COOK_PREF.'_useragent']==$useragent[0]) echo " selected=\"selected\""; ?>><?=($useragent[1])?></option>
<? } ?>
	</select>
	</td>
</tr>
<tr id="useragent_texttr" style="display: <?=(($_COOKIE[COOK_PREF.'_useragent']=="1")?"table-row":"none")?>">
	<td>&nbsp;</td>
	<td><input type="text" id="<?=COOK_PREF?>_useragenttext" name="<?=COOK_PREF?>_useragenttext" value="<?=($_COOKIE[COOK_PREF.'_useragenttext'])?>" style="width: 230px" /></td>
</tr>
<tr><td></td><td><input type="checkbox" name="<?=COOK_PREF?>_remove_cookies" style="border: 0px" <? if(!empty($_COOKIE[COOK_PREF.'_remove_cookies'])) echo "checked=\"checked\" "; ?>/>&nbsp;Remove Cookies</td></tr>
<tr><td></td><td><input type="checkbox" name="<?=COOK_PREF?>_remove_referer" style="border: 0px" <? if(!empty($_COOKIE[COOK_PREF.'_remove_referer'])) echo "checked=\"checked\" "; ?>/>&nbsp;Remove Referer Field</td></tr>
<tr><td></td><td><input type="checkbox" name="<?=COOK_PREF?>_remove_scripts" style="border: 0px" <? if(!empty($_COOKIE[COOK_PREF.'_remove_scripts'])) echo "checked=\"checked\" "; ?>/>&nbsp;Remove Scripts (JS, VBS, etc)</td></tr>
<tr><td></td><td><input type="checkbox" name="<?=COOK_PREF?>_remove_objects" style="border: 0px" <? if(!empty($_COOKIE[COOK_PREF.'_remove_objects'])) echo "checked=\"checked\" "; ?>/>&nbsp;Remove Objects (Flash, Java, etc)</td></tr>
<tr><td></td><td><input type="checkbox" name="<?=COOK_PREF?>_encode_urls" style="border: 0px" <? if(!empty($_COOKIE[COOK_PREF.'_encode_urls'])) echo "checked=\"checked\" "; ?>/>&nbsp;Encode URLs</td></tr>
<tr><td colspan="2"><input type="submit" value="Surrogafy" style="width: 100%; background-color: #F0F0F0" /></td></tr>
</table>
<br />
<div style="font-size: 10pt">Surrogafier v<?=VERSION?>
<br />
&copy; CopyLeft 2006 <a href="http://bcable.net/">Brad Cable</a></div>
</form>
</center>
</body>
</html>

<? }
else{

## PROXY FUNCTIONS ##

function check_proto($url){ return ((preg_replace("/^[a-z]*\:\/\//i","",$url)!=$url)?true:false); }

function protostrip($url){
	if(substr($url,0,2)=="//") $url=substr($url,2,strlen($url)-2);
	elseif(check_proto($url)) $url=preg_replace("/^[a-z]*\:\/\/(.*)$/i","\\1",$url);
	return $url;
}

function get_proto($url,$topurl=""){
	if(check_proto($url)) return preg_replace("/^([a-z]*)\:\/\/.*$/i","\\1",$url);
	else{
		if(empty($topurl) || !check_proto($topurl)) return "http";
		else return get_proto($topurl);
	}
}

function protofilestrip($url,$light=false){
	$url=protostrip($url);
	$url=preg_replace("/^([^\?\#]*).*$/i","\\1",$url);
	if(str_replace("/","",$url)!=$url) $url=preg_replace("/^([^\/]*)\/.*$/i","\\1",$url);
	if($light && substr($url,0,4)=="www.") $url=substr($url,4,strlen($url)-4);
	return $url;
}

function servername($url,$light=false,$stripport=false){
	$server=protofilestrip($url,$light);
	if($stripport) return strtolower(preg_replace("/^([^:]+).*$/","\\1",$server));
	else return $server;
}

function portval($url,$default=80){
	$portval=protofilestrip($url,false);
	if(preg_match("/:/",$portval)>0) $portval=intval(preg_replace("/^[^:]+:([0-9]*)$/","\\1",$portval));
	else $portval=0;
	if($portval==0) return $default;
	else return $portval;
}

function proxdec_url($url){
	if(substr($url,0,1)!="~" && strtolower(substr($url,0,3))!="%7e") return $url;
	while(substr($url,0,1)=="~" || strtolower(substr($url,0,3))=="%7e"){
		if(strtolower(substr($url,0,3))=="%7e") $url=urldecode($url);
		$url=substr($url,1,strlen($url)-1);
		$url=base64_decode($url);
		$new_url="";
		for($i=0;$i<strlen($url);$i++){
			$char=ord(substr($url,$i,1));
			$char-=ord(substr(COOK_PREF,$i%strlen(COOK_PREF),1));
			while($char<32) $char+=94;
			$new_url.=chr($char);
		}
		$url=$new_url;
	}
	return $url;
}

function proxenc_url($url){
	if(substr($url,0,1)=="~" || strtolower(substr($url,0,3))=="%7e") return $url;
	$new_url="";
	for($i=0;$i<strlen($url);$i++){
		$char=ord(substr($url,$i,1));
		$char+=ord(substr(COOK_PREF,$i%strlen(COOK_PREF),1));
		while($char>126) $char-=94;
		$new_url.=chr($char);
	}
	return urlencode("~".base64_encode($new_url));
}

function surrogafy_url($url,$add_proxy=true,$topurl=CURR_URL,$parse_url=true){
	if($parse_url==true){
		if(substr($url,0,strlen(THIS_SCRIPT))==THIS_SCRIPT || substr($url,0,11)=="javascript:") return $url;
		if(substr($url,0,1)=="#") return $url;
		$url=str_replace("&amp;","&",$url);
		$new_url=$url;
		if(substr($new_url,0,2)=="//") $new_url=get_proto($new_url,$topurl).":".$new_url;
		if(!check_proto($new_url)) $new_url=get_proto($new_url,$topurl)."://".servername($topurl).filepath($url,$topurl);
		if(preg_match("/\#/",$new_url)){
			$label=preg_replace("/^.*\#/","#",$new_url);
			$new_url=preg_replace("/\#.*$/","",$new_url);
		}
		$new_url=preg_replace(array("/ /","/&amp;/"),array("%20","&"),$new_url);
	}
	else $new_url=$url;
	$new_url=trim($new_url);
	if(ENCODE_URLS) $new_url=proxenc_url($new_url);
	elseif($add_proxy) $new_url=urlencode($new_url);
	if($add_proxy) $new_url=THIS_SCRIPT."?".COOK_PREF."_url=$new_url$label";
	return $new_url;
}

function filepath($url,$topurl=CURR_URL){
	if(protostrip($url)!=$url || substr($url,0,1)=="/"){
		$url=protostrip($url);
		if(count(explode("/",preg_replace("/^([^\?\#]*).*$/i","\\1",$url)))>=2) $url=preg_replace("/^[^\/]*\/([^\?\#]*)/i","\\1",$url);
		else $url="";
		return "/".$url;
	}
	else{
		if(!check_proto($topurl) && substr($topurl,0,1)!="/") return "/";
		$curr_url_path=filepath($topurl);
		if(str_replace("/","",$curr_url_path)!=$curr_url_path){
			$curr_url_path=preg_replace("/^(.*\/)[^\/]*$/i","\\1",$curr_url_path);
			return $curr_url_path.$url;
		}
		else return "/".$url;
	}
}

function header_value_arr($headername){
	global $headers;
	$linearr=explode("\n",$headers);
	$hvalsarr=preg_grep("/$headername\: (.*)/i",$linearr);
	return array_values(preg_replace("/$headername\: (.*)/i","\\1",$hvalsarr));
}

function header_value($headername){
	$arr=header_value_arr($headername);
	return $arr[0];
}

function getpage($url){

	global $headers,$out,$post_vars,$proxy_variables;

	$url=str_replace(" ","+",$url);
	$requrl=filepath($url);
	if(!empty($_COOKIE[COOK_PREF."_pip"]) && !empty($_COOKIE[COOK_PREF."_pport"])){
		$servername=$_COOKIE[COOK_PREF."_pip"];
		$portval=intval($_COOKIE[COOK_PREF."_pport"]);
		$requrl=$url;
	}
	elseif(get_proto($url)=="ssl" || get_proto($url)=="https"){
		$servername="ssl://".servername($url,false,true);
		$portval=portval($url,443);
	}
	else{
		$servername=servername($url,false,true);
		$portval=portval($url);
	}

	$fp=pfsockopen($servername,$portval,$errno,$errval,5);
	if(!$fp) die("<br />An error has occurred while attempting to connect to \"$servername\" on port \"$portval\"<br />URL: $url");

	$out=(empty($post_vars)?"GET":"POST")." $requrl HTTP/1.1\r\nHost: ".servername($url)."\r\n";

	if(!empty($_COOKIE[COOK_PREF."_useragent"])){
		if($_COOKIE[COOK_PREF."_useragent"]=="1") $useragent_cook=$_COOKIE[COOK_PREF."_useragenttext"];
		else $useragent_cook=$_COOKIE[COOK_PREF."_useragent"];
		if(!empty($useraget_cook)) $out.="User-Agent: $useragent_cook\r\n";
	}

	$http_auth="";
	if(extension_loaded("apache")){
		$reqarray=getallheaders();
		$http_auth=$reqarray['Authorization'];
	}
	else{
		if(!empty($_SERVER['HTTP_AUTHORIZATION'])) $http_auth=$_SERVER['HTTP_AUTHORIZATION'];
		elseif(!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW']))
			$http_auth="Basic ".base64_encode($_SERVER['PHP_AUTH_USER'].":".$_SERVER['PHP_AUTH_PW']);
		elseif(!empty($_SERVER['PHP_AUTH_DIGEST'])) $http_auth="Digest ".$_SERVER['PHP_AUTH_DIGEST'];
	}
	if(!empty($http_auth)) $out.="Authorization: $http_auth\r\n";

	$referer=urldecode(preg_replace("/^([^\?]*)(\?".COOK_PREF."_url=)?/i","",$_SERVER["HTTP_REFERER"]));
	if(empty($_COOKIE[COOK_PREF."_remove_referer"]) && !empty($referer)) $out.="Referer: $referer\r\n";
	if(!empty($post_vars)) $out.="Content-Length: ".strlen($post_vars)."\r\nContent-Type: application/x-www-form-urlencoded\r\n";

	$cook_prefdomain=servername($url,true);
	$cook_prefix=str_replace(".","_",$cook_prefdomain).COOKIE_SEPARATOR;
	if(count($_COOKIE)>0 && empty($_COOKIE[COOK_PREF.'_remove_cookies'])){
		$addtoout="Cookie:";
		reset($_COOKIE);
		while(list($key,$val)=each($_COOKIE)){
			if(str_replace(COOKIE_SEPARATOR,"",$key)==$key) continue;
			$cook_domain=preg_replace("/^(.*".COOKIE_SEPARATOR.").*$/","\\1",$key);
			if(substr($cook_prefix,strlen($cook_prefix)-strlen($cook_domain),strlen($cook_domain))!=$cook_domain) continue;
			$key=substr($key,strlen($cook_domain),strlen($key)-strlen($cook_domain));
			if(!in_array($key,$proxy_variables)) $addtoout.=" $key=$val;";
		}
		if($addtoout!="Cookie:"){
			$addtoout.="\r\n";
			$out.=$addtoout;
		}
	}

	$out.="Accept-Language: en-us,en;q=0.5\r\n".
	      "Accept-Encoding: gzip,deflate\r\n".
	      "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n".
	      "Connection: close\r\n".
	      "\r\n".$post_vars;
	fwrite($fp,$out);

	$response="100";
	while($response=="100"){
		$responseline=fread($fp,12);
		$response=substr($responseline,-3,3);

		$headers=fread($fp,1);
		while(true){
			$chunk="";
			$byte=fread($fp,1);
			if($byte=="\r"){
				$headers.=fread($fp,1);
				break;
			}
			while($byte!="\r"){
				$chunk.=$byte;
				$byte=fread($fp,1);
			}
			$headers.=$chunk.fread($fp,1);
		}
	}

	if(header_value("Server")!="") header("Server: ".header_value("Server"));
	if(header_value("WWW-Authenticate")!="") header("WWW-Authenticate: ".header_value("WWW-Authenticate"));

	if(empty($_COOKIE[COOK_PREF.'_remove_cookies'])){
		$setcookiearr=header_value_arr("Set-Cookie");
		for($i=0;$i<count($setcookiearr);$i++){
			$thiscook=explode("=",$setcookiearr[$i],2);
			$cook_val=preg_replace("/^([^;]*);.*$/i","\\1",$thiscook[1]);
			$cook_domain=preg_replace("/^.*domain=[\t ]*\.?([^;]+)\.?;.*$/i","\\1",$thiscook[1]);
			if($cook_domain==$thiscook[1]) $cook_domain=$cook_prefdomain;
			elseif(substr($cook_prefdomain,strlen($cook_prefdomain)-strlen($cook_domain),strlen($cook_domain))!=$cook_domain) continue;
			$cook_name=str_replace(".","_",$cook_domain).COOKIE_SEPARATOR.$thiscook[0];
			$_COOKIE[$cook_name]=$cook_val;
			setcookie($cook_name,$cook_val);
		}
	}

	if(substr($response,0,2)=="30"){
		$redirurl=surrogafy_url(header_value("Location"),true,$url);
		header("Location: $redirurl");
		exit();
	}

	if(header_value("Content-Type")!="") header("Content-Type: ".header_value("Content-Type"));
	if(header_value("Content-Disposition")!="") header("Content-Disposition: ".header_value("Content-Disposition"));
	elseif(substr(header_value("Content-Type"),0,4)!="text" && substr(header_value("Content-Type"),0,24)!="application/x-javascript"){
		$file=preg_replace("/^.*\/([^\/#\?]*).*?$/i","\\1",$url);
		if(!empty($file)) header("Content-Disposition: attachment; filename=$file");
	}

	if(substr(header_value("Content-Type"),0,4)=="text" || substr(header_value("Content-Type"),0,24)=="application/x-javascript"){
		$justoutput=false;
		$justoutputnow=false;
	}
	else{
		if(header_value("Content-Encoding")=="gzip") $justoutputnow=false;
		else $justoutputnow=true;
		$justoutput=true;
		if(header_value("Content-Length")!="") header("Content-Length: ".header_value("Content-Length"));
	}

	if(header_value("Transfer-Encoding")=="chunked"){
		$body="";
		$chunksize="";
		while($chunksize!==0){
			$byte="";
			$chunk="";
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

	elseif(header_value("Content-Length")!=""){
		$conlen=header_value("Content-Length");
		$body="";
		for($i=0;$i<$conlen;$i++){
			$byte=fread($fp,1);
			if($justoutputnow) echo $byte;
			else $body.=$byte;
		}
	}

	else{
		$body="";
		while(true){
			$chunk=fread($fp,200);
			if($justoutputnow) echo($chunk);
			else $body.=$chunk;
			if($chunk=="") break;
		}
	}

	#fclose($fp);
	if(header_value("Content-Encoding")=="gzip") $body=gzinflate(substr($body,10));
	if($justoutput){
		if(!$justoutputnow) echo $body;
		exit();
	}
	return array($body,$url,$cook_prefix);

}
## END PROXY FUNCTIONS#


## BEGIN PROXY CODE #

# Deal with cookies for proxy #
global $proxy_variables,$proxy_varblacklist,$post_vars,$cookies;
$proxy_variables=array(COOK_PREF."_url",COOK_PREF."_pip",COOK_PREF."_pport",COOK_PREF."_useragent",COOK_PREF."_useragenttext",COOK_PREF."_remove_cookies",COOK_PREF."_remove_referer",COOK_PREF."_remove_scripts",COOK_PREF."_remove_objects",COOK_PREF."_encode_urls");
$proxy_varblacklist=array(COOK_PREF."_url");

if($postandget[COOK_PREF.'_set_values']){
	if($postandget[COOK_PREF."_useragent"]!="1"){
		unset($postandget[COOK_PREF."_useragenttext"]);
		setcookie(COOK_PREF."_useragenttext",false);
	}
	while(list($key,$val)=each($proxy_variables)){
		if(!in_array($val,$proxy_varblacklist)){
			if(!isset($postandget[$val]) || empty($postandget[$val])) setcookie($val,false);
			else{
				$_COOKIE[$val]=$postandget[$val];
				setcookie($val,$postandget[$val]);
			}
		}
	}
	define("ENCODE_URLS",!empty($postandget[COOK_PREF.'_encode_urls']));
	$theurl=surrogafy_url($postandget[COOK_PREF.'_url'],true,"",false);
	header("Location: $theurl");
	exit();
}
# end #

# Deal with GET/POST/COOKIES and the URL #
$curr_url=stripslashes($postandget[COOK_PREF.'_url']);
define("ENCODE_URLS",!empty($_COOKIE[COOK_PREF.'_encode_urls']));
if(!empty($_POST[COOK_PREF.'_url'])) $curr_url=urldecode($curr_url);
if(ENCODE_URLS) $curr_url=proxdec_url($curr_url);

$getkeys=array_keys($_GET);
foreach($getkeys as $getvar){
	if(!in_array($getvar,$proxy_variables)){
		if(str_replace("?","",$curr_url)==$curr_url) $curr_url.="?$getvar=".urlencode($_GET[$getvar]);
		else $curr_url.="&$getvar=".urlencode($_GET[$getvar]);
	}
}

$post_vars="";
$postkeys=array_keys($_POST);
foreach($postkeys as $postkey){
	if(!in_array($postkey,$proxy_variables)){
		if(!empty($post_vars)) $post_vars.="&";
		$post_vars.="$postkey=".urlencode($_POST[$postkey]);
	}
}

$curr_url=get_proto($curr_url)."://".protostrip($curr_url);
# end #

# Get the page #
$pagestuff=getpage($curr_url);
$body=$pagestuff[0];
define("CURR_URL",$pagestuff[1]);
define("PAGECOOK_PREFIX",$pagestuff[2]);
unset($pagestuff);
define("CONTENT_TYPE",preg_replace("/^([a-z0-9\-\/]+).*$/i","\\1",header_value("Content-Type")));
# end #


## Got the Page, Now Parse The Body ##

## REGEXPS ##
#
# This is where all the parsing is defined.  If a site isn't being
# parsed properly, the problem is more than likely in this section.
# The rest of the code is just there to set up this wonderful bunch
# of incomprehensible regular expressions.
#

global $regexp_arrays,$notinscript;
$anyspace="[\t\r\n ]*";
$plusspace="[\t\r\n ]+";
$htmlattrs="(href|src|action|background|pluginspage|codebase)";
$jsattrs="(href|src|location|action|background|backgroundImage|pluginspage|codebase)";
$jsmethods="(replace)";
$notinscript="(?s)(?!(((?!<).(?!script[^>]*>))+?)<\/script>)";
$isinscript="(?s)(?=(((?!<).(?!script[^>]*>))+?)<\/script>)";
#$isinscriptb="(?s)(?<=<[a-z]+[^\>]*on[a-z]{3,20}$anyspace=$anyspace([\"']))";

$js_regexp_arrays=array(
	//array(1,"/(<[a-z][^>]*?on[a-z]{3,20}$anyspace=$anyspace([\"']).*?[^\\\\])\\2/i","\\1;\\2"),
	array(1,"/(<[a-z]+[^\>]*on[a-z]{3,20}$anyspace=$anyspace([\"']))(.*?[^\\\\])\\1/i","\\1\\3;\\2"),
	array(1,"/([^a-z]$jsmethods\()([^)]*)\)$isinscript/i","\\1surrogafy_url(\\3))"),
	array(2,"/\.$jsattrs$anyspace=$anyspace(\"|')(.*)(\\2)([;\}])$isinscript/i",3),
	array(1,"/(\.$jsattrs$anyspace=$anyspace)([^\"'\=\t ].*?)([;\}])$isinscript/i","\\1surrogafy_url(\\3)\\4")
);
$regexp_arrays=array(
	"text/html" => array(
		array(1,"/(<form[^>]*action$anyspace=$anyspace([\"'])$anyspace(.*?[^\\\\])$anyspace\\2[^>]*>)$notinscript/i","\\1\n<input type=\"hidden\" name=\"".COOK_PREF."_url\" value=\"\\3\" />\n"),
		array(1,"/(<form[^>]*action$anyspace=$anyspace([^\"'\\\\][^> ]*)[^>]*>)$notinscript/i","\\1\n<input type=\"hidden\" name=\"".COOK_PREF."_url\" value=\"\\2\" />\n"),
		array(2,"/name=\"".COOK_PREF."_url\" value=\"(.*?)\" \/>/i",1,false),
		array(2,"/<[a-z][^>]*?$plusspace$htmlattrs$anyspace=$anyspace([\"'])$anyspace(.*?[^\\\\])$anyspace\\2[^>]*>$notinscript/i",3),
		array(2,"/<[a-z][^>]*?$plusspace$htmlattrs$anyspace=$anyspace([^\"'\\\\][^> ]*)[^>]*>$notinscript/i",2),
		array(2,"/<script[^>]*?{$plusspace}src$anyspace=$anyspace([\"'])$anyspace(.*?[^\\\\])\\1[^>]*>$anyspace<\/script>/i",2),
		array(2,"/<meta[^>]*http-equiv$anyspace=$anyspace([\"'])refresh\\1[^>]*content$anyspace=$anyspace([\"'])[ 0-9\.;\t\\r\n]*url=(.*?)\\2[^>]*>/i",3),
		array(2,"/<meta[^>]*http-equiv$anyspace={$anyspace}refresh[^>]*content$anyspace=$anyspace([\"'])[ 0-9\.;\t\\r\n]*url=(.*?)\\1[^>]*>/i",2),
		array(1,"/(<meta[^>]*http-equiv$anyspace=$anyspace([\"'])set-cookie\\2[^>]*content$anyspace=$anyspace)([\"'])(.*?[^\\\\])$anyspace\\3/i","\\1\\3".PAGECOOK_PREFIX."\\4\\3"),
		array(1,"/(<meta[^>]*http-equiv$anyspace={$anyspace}set-cookie[^>]*content$anyspace=$anyspace)([\"'])(.*?[^\\\\])$anyspace\\2/i","\\1\\2".PAGECOOK_PREFIX."\\3\\2")
	),
	"text/css" => array(
		array(2,"/[^a-z]url\($anyspace(\"|')(.*)(\\1)$anyspace\)/i",2),
		array(2,"/[^a-z]url\($anyspace([^\"'\\\\].*)$anyspace\)/i",1),
		array(2,"/@import (\"|')(.*)(\\1);/i",2)
	),
	"application/x-javascript" => $js_regexp_arrays,
	"text/javascript" => $js_regexp_arrays
);

## END REGEXPS ##

# Parsing Functions #

function parse_html($regexp,$partoparse,$html,$addproxy){
	if(preg_match($regexp,$html)>0){
		preg_match_all($regexp,$html,$matcharr,PREG_SET_ORDER);
		foreach($matcharr as $match){
			$nurl=surrogafy_url($match[$partoparse],$addproxy);
			$nhtml=str_replace($match[$partoparse],$nurl,$match[0]);
			$html=str_replace($match[0],$nhtml,$html);
		}
	}
	return $html;
}

function parse_all_html($html){
	global $regexp_arrays,$notinscript,$isinscript;
	reset($regexp_arrays);
	while(list($key,$arr)=each($regexp_arrays)){
		if(CONTENT_TYPE==$key || (CONTENT_TYPE=="text/html" && $key!="text/javascript")){
			foreach($arr as $regexp_array){
				if(CONTENT_TYPE=="text/html") $regexp=$regexp_array[1];
				else $regexp=str_replace($notinscript,"",str_replace($isinscript,"",$regexp_array[1]));
				if($regexp_array[0]==1) $html=preg_replace($regexp,$regexp_array[2],$html);
				elseif($regexp_array[0]==2){
					if(count($regexp_array)<4) $addproxy=true;
					else $addproxy=false;
					$html=parse_html($regexp,$regexp_array[2],$html,$addproxy);
				}
			}
		}
	}
	return $html;
}

$body=parse_all_html($body);
# end #

# Code Conversion to Javascript #
function escape_regexp($regexp,$dollar=false){
	global $notinscript,$isinscript;
	$regexp=addslashes(str_replace($isinscript,"",str_replace($notinscript,"",str_replace("\n","\\n",str_replace("\r","\\r",$regexp)))));
	if($dollar) return preg_replace("/[\\\\]+(?=[0-9])/","\\\\$",$regexp);
	else return preg_replace("/[\\\\]+(?=[0-9])/","\\\\\\\\",$regexp);
}

function convertarray_to_javascript(){
	global $regexp_arrays;
	$js="regexp_arrays=new Array(".count($regexp_arrays).");\n";
	reset($regexp_arrays);
	while(list($key,$arr)=each($regexp_arrays)){
		$js.="regexp_arrays[\"$key\"]=new Array(".count($arr).");\n";
		for($i=0;$i<count($arr);$i++){
			$js.="regexp_arrays[\"$key\"][$i]=new Array(";
			if($arr[$i][0]==1) $js.="1,".escape_regexp($arr[$i][1])."g,\"".escape_regexp($arr[$i][2],true)."\"";
			elseif($arr[$i][0]==2){
				if(count($arr[$i]<4)) $addproxy=true;
				else $addproxy=false;
				$js.="2,".escape_regexp($arr[$i][1])."g,{$arr[$i][2]},$addproxy";
			}
			$js.=");\n";
		}
	}
	return $js;
}
# end #

if(CONTENT_TYPE=="text/html" || CONTENT_TYPE=="application/x-javascript"){
# Insert the code's Javascript #
	$body=preg_replace("/<head>/i","<head>

<link rel=\"shortcut icon\" href=\"".surrogafy_url(servername(CURR_URL)."/favicon.ico")."\" />

<script language=\"javascript\">
<!--

".convertarray_to_javascript().((!empty($_COOKIE[COOK_PREF.'_remove_objects']))?"regexp_arrays[\"text/html\"].push(Array(1,/<[\\\\/]?(embed|param|object)[^>]*>/ig,\"\"));":"")."

function check_proto(url){ return ((url.replace(/^[a-z]*\:\/\//i,\"\")!=url)?true:false); }

function protostrip(url){
	if(url.substring(0,2)==\"//\") url=url.substring(2,url.length-2);
	else if(check_proto(url)) url=url.replace(/^[a-z]*\:\/\/(.*)$/i,\"\\$1\");
	return url;
}

function get_proto(url,topurl){
	if(check_proto(url)) return url.replace(/^([a-z]*)\:\/\/.*$/i,\"\\$1\");
	else{
		if(topurl==\"\" || !check_proto(topurl)) return \"http\";
		else return get_proto(topurl,\"\");
	}
}

function protofilestrip(url){
	url=protostrip(url);
	url=url.replace(/^([^\?\#]*).*$/i,\"\\$1\");
	if(url.replace(\"/\",\"\")!=url) url=url.replace(/^([^\/]*)\/.*$/i,\"\\$1\");
	return url;
}

function servername(url){
	server=protofilestrip(url);
	return server.replace(/^([^:]+).*$/,\"\\$1\",server);
}

function filepath(url){
	if(protostrip(url)!=url || url.substring(0,1)==\"/\"){
		url=protostrip(url);
		if(url.replace(/^([^\?\#]*).*$/i,\"\\$1\").split(\"/\").length>=2) url=url.replace(/^[^\/]*\/([^\?\#]*)/i,\"\\$1\");
		else url=\"\";
		url=\"/\"+url;
		return url;
	}
	else{
		curr_url_path=filepath(\"".CURR_URL."\");
		if(curr_url_path.replace(\"/\",\"\")!=curr_url_path){
			curr_url_path=curr_url_path.replace(/^(.*\/)[^\/]*$/i,\"\\$1\");
			return curr_url_path+url;
		}
		else return \"/\"+url;
	}
}

$js_proxenc

function surrogafy_url(url){
	if(url.substring(0,\"".THIS_SCRIPT."\".length)==\"".THIS_SCRIPT."\" || url.substring(0,11)==\"javascript:\") return url;
	if(url.substring(0,1)==\"#\") return url;
	new_url=url;
	if(new_url.substring(0,2)==\"//\") new_url=get_proto(new_url,\"".CURR_URL."\")+\":\"+new_url;
	if(!check_proto(new_url)) new_url=get_proto(new_url,\"".CURR_URL."\")+\"://\"+servername(\"".CURR_URL."\")+filepath(url);
	if(".ENCODE_URLS.") new_url=proxenc_url(new_url);
	else new_url=encodeURIComponent(new_url);
	new_url=\"".THIS_SCRIPT."?".COOK_PREF."_url=\"+new_url;
	return new_url;
}

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
		for(matchkey in matcharr){
			match=matcharr[matchkey];
			nurl=surrogafy_url(match[partoparse],addproxy);
			nhtml=match[0].replace(match[partoparse],nurl);
			html=html.replace(match[0],nhtml);
		}
	}
	return html;
}

function parse_all_html(html){
	for(key in regexp_arrays){
		arr=regexp_arrays[key];
		for(regexp_arraykey in arr){
			regexp_array=arr[regexp_arraykey];
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

document.write_actual=document.write;
document.write=function(html){
	html=parse_all_html(html);
	document.write_actual(html);
}

document.writeln_actual=document.writeln;
document.writeln=function(html){
	html=parse_all_html(html);
	document.writeln_actual(html);
}

window.open_actual=window.open;
window.open=function(url,arg2,arg3){
	url=surrogafy_url(url);
	window.open_actual(url,arg2,arg3);
}

//-->
</script>",$body);
# end #

# Remove Scripts #
	if(!empty($_COOKIE[COOK_PREF.'_remove_scripts'])){
		$body=preg_replace("/<(.?)noscript>/si","",$body);
		$body=preg_replace("/<script.+?<\/script>/si","",$body);
	}
}
# end #

# Remove objects #
if(!empty($_COOKIE[COOK_PREF.'_remove_objects'])){
	$body=preg_replace("/<embed.*?<\/embed>/si","",$body);
	$body=preg_replace("/<object.*?<\/object>/si","",$body);
}
# end #

## Retrieved, Parsed, All Ready to Output ##
echo $body;
#echo $out."\n\n".$body; # debug thingy

## THE END ##

} ?>
