<?

#
# Surrogafier v0.7.4.1b
#
# Author: Brad Cable
# License: GPL Version 2
#

define("VERSION","0.7.4.1b");
define("COOKIE_SEPARATOR","__surrogafier_sep__");

define("THIS_SCRIPT","http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}");

global $proxy_variables,$proxy_varblacklist,$post_vars;
$proxy_variables=array("proxy_url","proxy_pip","proxy_pport","proxy_useragent","proxy_useragenttext","proxy_remove_cookies","proxy_remove_referer","proxy_remove_scripts","proxy_remove_objects");
$proxy_varblacklist=array("proxy_url");

$postandget=array_merge($_GET,$_POST);
if($postandget['proxy_set_values']){
	if($postandget["proxy_useragent"]!="1"){
		unset($postandget["proxy_useragenttext"]);
		setcookie("proxy_useragenttext",false);
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
	header("Location: ".THIS_SCRIPT."?proxy_url={$postandget['proxy_url']}");
	exit();
}

if(!isset($postandget['proxy_url'])){

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

<DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" 
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
<head>
<title>Surrogafier</title>
<style>
<!--
	input{border: 1px solid #000000}
	select{border: 1px solid #000000}
	a{color: #000000}
	a:hover{text-decoration: none}
//-->
</style>
</head>
<body style="font-family: bitstream vera sans, arial" onload="document.getElementById('proxy_url').focus();">
<div style="font-size: 18pt; font-weight: bold; text-align: center; margin-bottom: 5px">Surrogafier</div>
<center>
<form method="get">
<input type="hidden" name="proxy_set_values" value="1" />
<table>
<tr>
	<td>URL:</td>
	<td><input type="text" name="proxy_url" id="proxy_url" style="width: 230px" /></td>
</tr>
<tr>
	<td>Proxy Server:</td>
	<td><table cellspacing="0" cellpadding="0">
	<tr>
		<td><input type="text" name="proxy_pip" style="width: 180px" value="<?=($_COOKIE['proxy_pip'])?>" /></td>
		<td style="width: 5px">&nbsp;</td>
		<td><input type="text" name="proxy_pport" maxlength="5" size="5" style="width: 45px" value="<?=($_COOKIE['proxy_pport'])?>" /></td>
	</tr>
	</table></td>
</tr>
<tr>
	<td>User-Agent:</td>
	<td><select name="proxy_useragent" style="width: 230px" onchange="if(this.value=='1'){ document.getElementById('useragent_texttr').style.display='table-row'; document.getElementById('proxy_useragenttext').focus(); } else document.getElementById('useragent_texttr').style.display='none';">
<? foreach($useragent_array as $useragent){ ?>
		<option value="<?=($useragent[0])?>"<? if($_COOKIE['proxy_useragent']==$useragent[0]) echo " selected=\"selected\""; ?>><?=($useragent[1])?></option>
<? } ?>
	</select>
	</td>
</tr>
<tr id="useragent_texttr" style="display: <?=(($_COOKIE['proxy_useragent']=="1")?"table-row":"none")?>">
	<td>&nbsp;</td>
	<td><input type="text" id="proxy_useragenttext" name="proxy_useragenttext" value="<?=($_COOKIE['proxy_useragenttext'])?>" style="width: 230px" /></td>
</tr>
<tr><td></td><td><input type="checkbox" name="proxy_remove_cookies" <? if(!empty($_COOKIE['proxy_remove_cookies'])) echo "checked=\"checked\" "; ?>/>&nbsp;Remove Cookies</td></tr>
<tr><td></td><td><input type="checkbox" name="proxy_remove_referer" <? if(!empty($_COOKIE['proxy_remove_referer'])) echo "checked=\"checked\" "; ?>/>&nbsp;Remove Referer Field</td></tr>
<tr><td></td><td><input type="checkbox" name="proxy_remove_scripts" <? if(!empty($_COOKIE['proxy_remove_scripts'])) echo "checked=\"checked\" "; ?>/>&nbsp;Remove Scripts (JS, VBS, etc)</td></tr>
<tr><td></td><td><input type="checkbox" name="proxy_remove_objects" <? if(!empty($_COOKIE['proxy_remove_objects'])) echo "checked=\"checked\" "; ?>/>&nbsp;Remove Objects (Flash, Java, etc)</td></tr>
<tr><td colspan="2"><input type="submit" value="Surrogafy" style="width: 100%; background-color: #F0F0F0" /></td></tr>
</table>
<br />
<div style="font-size: 10pt">Surrogafier v<?=VERSION?>
<br />
&copy; CopyLeft 2006 <a href="http://www.bcable.net/">Brad Cable</a></div>
</form>
</center>
</body>
</html>

<? }
else{

## The Actual Proxy Code ##

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

$curr_url=urldecode($postandget['proxy_url']);

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

function surrogafy_url($url,$add_proxy=true,$topurl=CURR_URL){
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
	if($add_proxy) $new_url=THIS_SCRIPT."?proxy_url=".urlencode(trim($new_url)).$label;
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
	if(!empty($_COOKIE["proxy_pip"]) && !empty($_COOKIE["proxy_pport"])){
		$servername=$_COOKIE["proxy_pip"];
		$portval=intval($_COOKIE["proxy_pport"]);
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

	if(!empty($_COOKIE["proxy_useragent"])){
		if($_COOKIE["proxy_useragent"]=="1") $useragent_cook=$_COOKIE["proxy_useragenttext"];
		else $useragent_cook=$_COOKIE["proxy_useragent"];
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

	$referer=urldecode(preg_replace("/^([^\?]*)(\?proxy_url=)?/i","",$_SERVER["HTTP_REFERER"]));
	if(empty($_COOKIE["proxy_remove_referer"]) && !empty($referer)) $out.="Referer: $referer\r\n";
	if(!empty($post_vars)) $out.="Content-Length: ".strlen($post_vars)."\r\nContent-Type: application/x-www-form-urlencoded\r\n";

	$cook_prefdomain=servername($url,true);
	$cook_prefix=str_replace(".","_",$cook_prefdomain).COOKIE_SEPARATOR;
	if(count($_COOKIE)>0 && empty($_COOKIE['proxy_remove_cookies'])){
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

	if(empty($_COOKIE['proxy_remove_cookies'])){
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
		#fclose($fp);
		#echo $ja.$out."\n\n\n\n".$headers;
		$redirurl=surrogafy_url(header_value("Location"),true,$url);
		#echo("<script>setTimeout(\"location.replace('$redirurl')\",5000);</script>");
		header("Location: $redirurl");
		#echo $ja.$out."\n\n\n\n".$headers;
		exit();
		#$post_vars="";
		#return getpage($redirurl);
	}
	#echo $ja.$out."\n\n\n\n".$headers;

	header("Content-Type: ".header_value("Content-Type"));

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
	return array($body,$url);

}

$pagestuff=getpage($curr_url);
$body=$pagestuff[0];
define("CURR_URL",$pagestuff[1]);
unset($pagestuff);
define("CONTENT_TYPE",preg_replace("/^([a-z0-9\-\/]+).*$/i","\\1",header_value("Content-Type")));


## Got the Page, Now Parse The Body ##

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

global $regexp_arrays,$notinscript;
$htmlattrs="(href|src|action|background|pluginspage|codebase)";
$jsattrs="(href|src|location|action|background|backgroundImage|pluginspage|codebase)";
$jsmethods="(replace)";
$notinscript="(?s)(?!((.(?<!<)(?!script[^>]*>))+?)<\/script>)";
$isinscript="(?s)(?=((.(?<!<)(?!script[^>]*>))+?)<\/script>)";
$regexp_arrays=array(
	"text/html" => array(
		array(1,"/(<form[^>]*action[\t ]*=[\t ]*([\"'])(.*?[^\\\\])\\2[^>]*>)".$notinscript."/i","\\1\n<input type=\"hidden\" name=\"proxy_url\" value=\"\\3\" />\n"),
		array(1,"/(<form[^>]*action[\t ]*=[\t ]*([^\"'\\\\][^> ]*)[^>]*>)".$notinscript."/i","\\1\n<input type=\"hidden\" name=\"proxy_url\" value=\"\\2\" />\n"),
		array(2,"/name=\"proxy_url\" value=\"(.*?)\" \/>/i",1,false),
		array(2,"/<[a-z][^>]*?[ \r\n]+".$htmlattrs."[\t ]*=[\t ]*([\"'])(.*?[^\\\\])\\2[^>]*>".$notinscript."/i",3),
		array(2,"/<[a-z][^>]*?[ \r\n]+".$htmlattrs."[\t ]*=[\t ]*([^\"'\\\\][^> ]*)[^>]*>".$notinscript."/i",2),
		array(2,"/<script[^>]*?[ \r\n]+src[\t ]*=[\t ]*([\"'])(.*?[^\\\\])\\1[^>]*>[ \t\r\n]*<\/script>/i",2),
		array(2,"/<meta[^>]*http-equiv[\t ]*=[\t ]*([\"'])refresh\\1[^>]*content[\t ]*=[\t ]*([\"'])[ 0-9\.;\t\\r\n]*url=(.*?)\\2[^>]*>/i",3),
		array(2,"/<meta[^>]*http-equiv[\t ]*=[\t ]*refresh[^>]*content[\t ]*=[\t ]*([\"'])[ 0-9\.;\t\\r\n]*url=(.*?)\\1[^>]*>/i",2)
	),
	"text/css" => array(
		array(2,"/[^a-z]url\([\t ]*(\"|')(.*)(\\1)[\t ]*\)/i",2),
		array(2,"/[^a-z]url\([\t ]*([^\"'\\\\].*)[\t ]*\)/i",1),
		array(2,"/@import (\"|')(.*)(\\1);/i",2)
	),
	"application/x-javascript" => array(
		array(1,"/([^a-z]".$jsmethods."\()([^)]*)\)".$isinscript."/i","\\1surrogafy_url(\\3))"),
		array(2,"/\.".$jsattrs."[ \t]*=[ \t]*(\"|')(.*)(\\2)([;\}])".$isinscript."/i",3),
		array(1,"/(\.".$jsattrs."[ \t]*=[ \t]*)([^\"'\=\t ].*?)([;\}])".$isinscript."/i","\\1surrogafy_url(\\3)\\4")
	)
);

function parse_all_html($html){
	global $regexp_arrays,$notinscript,$isinscript;
	reset($regexp_arrays);
	while(list($key,$arr)=each($regexp_arrays)){
		if(CONTENT_TYPE==$key || CONTENT_TYPE=="text/html"){
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

if(CONTENT_TYPE=="text/html" || CONTENT_TYPE=="application/x-javascript"){
	if(!empty($_COOKIE['proxy_remove_scripts'])){
		$body=preg_replace("/<(.?)noscript>/si","",$body);
		$body=preg_replace("/<script.+?<\/script>/si","",$body);
	}
	$body=preg_replace("/<head>/i","<head>
<!-- PROXIFIER JAVASCRIPT CODE -->
<script language=\"javascript\">
<!--

".convertarray_to_javascript().((!empty($_COOKIE['proxy_remove_objects']))?"regexp_arrays[\"text/html\"].push(Array(1,/<[\\\\/]?(embed|param|object)[^>]*>/ig,\"\"));":"")."

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

function surrogafy_url(url){
	if(url.substring(0,\"".THIS_SCRIPT."\".length)==\"".THIS_SCRIPT."\" || url.substring(0,11)==\"javascript:\") return url;
	new_url=url;
	if(new_url.substring(0,2)==\"//\") new_url=get_proto(new_url,\"".CURR_URL."\")+\":\"+new_url;
	if(!check_proto(new_url)) new_url=get_proto(new_url,\"".CURR_URL."\")+\"://\"+servername(\"".CURR_URL."\")+filepath(url);
	new_url=\"".THIS_SCRIPT."?proxy_url=\"+encodeURIComponent(new_url);
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
</script>
<!-- END PROXIFIER JAVASCRIPT CODE -->
",$body,1);
}

if(!empty($_COOKIE['proxy_remove_objects'])){
	$body=preg_replace("/<embed.*?<\/embed>/si","",$body);
	$body=preg_replace("/<object.*?<\/object>/si","",$body);
}

## Retrieved, Parsed, All Ready to Output ##
echo $body;
#echo $out."\n\n".$body;

## THE END ##

} ?>
