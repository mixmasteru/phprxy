<?

#
# Surrogafier v0.7.8.1b
#
# Author: Brad Cable
# License: GPL Version 2
#


/*/ Address Blocking Notes \*\

Formats for address blocking are as follows:

  1.2.3.4     - plain IP address
  1.2.3.4/24  - subnet blocking
  php.net     - domain blocking

\*\ End Address Blocking Notes /*/

$blocked_addresses=array("10.0.0.0/24","172.0.0.0/24","192.168.0.0/16","127.0.0.0/24");


// DON'T EDIT ANYTHING AFTER THIS POINT \\

define("VERSION","0.7.8.1b");
define("THIS_SCRIPT","http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}");

# Randomized cookie prefixes #
function gen_randstr($len){
	$chars="";
	for($i=0;$i<$len;$i++){
		$char=rand(0,25);
		$char=chr($char+97);
		$chars.=$char;
	}
	return $chars;
}

session_start();
if(empty($_SESSION['sesspref'])){
	$sesspref=gen_randstr(30);
	$_SESSION["sesspref"]=$sesspref;
}
else $sesspref=$_SESSION['sesspref'];

if(empty($_COOKIE['user'])){
	$cookpref=gen_randstr(12);
	setcookie("user",$cookpref);
}
else $cookpref=$_COOKIE['user'];

define("SESS_PREF",$sesspref);
define("COOK_PREF",$cookpref);
define("COOKIE_SEPARATOR","__".COOK_PREF."__");

# end #

$js_proxenc="

function expon(a,b){
	if(b==0) return 1;
	num=a; b--;
	while(b>0){ num*=a; b--; }
	return num;
}

function b64e(string){
	if(window.btoa) return btoa(string);
	binrep=\"\";
	for(i=0;i<string.length;i++){
		charnum=string.charCodeAt(i);
		for(j=7;j>=0;j--){
			if(charnum>=expon(2,j)){
				binrep+=\"1\"; charnum-=expon(2,j);
			}
			else binrep+=\"0\";
		}
	}
	while(binrep.length%6) binrep+=\"00\";
	encstr=\"\";
	for(i=1;i*6<=binrep.length;i++){
		charbin=binrep.substring((i-1)*6,i*6);
		charnum=0;
		for(j=0;j<6;j++) if(charbin.substring(j,j+1)==\"1\") charnum+=expon(2,5-j);
		if(charnum<=25) charnum+=65;
		else if(charnum<=51) charnum+=71;
		else if(charnum<=61) charnum-=4;
		else if(charnum==62) charnum=43;
		else if(charnum==63) charnum=47;
		encstr+=String.fromCharCode(charnum);
	}
	while(encstr.length%8) encstr+=\"=\";
	return encstr;
}

function proxenc(url){
	if(url.substring(0,1)==\"~\" || url.substring(0,3).toLowerCase()==\"%7e\") return url;
	new_url=\"\";
	sess_pref=\"".SESS_PREF."\";
	for(i=0;i<url.length;i++){
		char=url.charCodeAt(i);
		char+=sess_pref.charCodeAt(i%sess_pref.length);
		while(char>126) char-=94;
		new_url+=String.fromCharCode(char);
	}
	return encodeURIComponent(\"~\"+b64e(new_url));
}";

## JAVASCRIPT ##
if($_SERVER['QUERY_STRING']=="js_funcs"){ ?>//<script>

function check_proto(url){ return ((url.replace(/^[a-z]*\:\/\//i,"")!=url)?true:false); }

function protostrip(url){
	if(url.substring(0,2)=="//") url=url.substring(2,url.length-2);
	else if(check_proto(url)) url=url.replace(/^[a-z]*\:\/\/(.*)$/i,"\$1");
	return url;
}

function get_proto(url,topurl){
	if(check_proto(url)) return url.replace(/^([a-z]*)\:\/\/.*$/i,"\$1");
	else{
		if(topurl=="" || !check_proto(topurl)) return "http";
		else return get_proto(topurl,"");
	}
}

function protofilestrip(url){
	url=protostrip(url);
	url=url.replace(/^([^\?\#]*).*$/i,"\$1");
	if(url.replace("/","")!=url) url=url.replace(/^([^\/]*)\/.*$/i,"\$1");
	return url;
}

function servername(url){
	server=protofilestrip(url);
	return server.replace(/^([^:]+).*$/,"\$1",server);
}

function filepath(url){
	if(protostrip(url)!=url || url.substring(0,1)=="/"){
		url=protostrip(url);
		if(url.replace(/^([^\?\#]*).*$/i,"\$1").split("/").length>=2) url=url.replace(/^[^\/]*\/([^\?\#]*)/i,"\$1");
		else url="";
		url="/"+url;
		return url;
	}
	else{
		curr_url_path=filepath(proxy_current_url);
		if(curr_url_path.replace("/","")!=curr_url_path){
			curr_url_path=curr_url_path.replace(/^(.*\/)[^\/]*$/i,"\$1");
			return curr_url_path+url;
		}
		else return "/"+url;
	}
}

<?=$js_proxenc?>

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

function surrogafy_url(){
	addproxy=true;
	switch(arguments.length){
		case 0: return;
		case 2: addproxy=arguments[1];
		case 1: url=arguments[0];
	}
	if(url==undefined || url.length==0) return;
	ourl=url;
	resturl=null;
	urlquote=null;
	if((ourl.substring(0,1)=="\"" || ourl.substring(0,1)=="'") && ourl.substring(0,1)==ourl.substring(ourl.length-1,ourl.length)){
		urlquote=ourl.substring(0,1);
		ourl=ourl.substring(1,ourl.length-1);
		url=ourl;
	}
	url=url.replace(/^url\(([^)]+)\).*$/i,"\$1");
	if(url!=ourl) resturl=ourl.replace(/^url\([^)]+(\).*)$/i,"\$1");
	if(url.substring(0,proxy_this_script.length)==proxy_this_script || url.substring(0,11)=="javascript:") return url;
	if(url.substring(0,1)=="#") return url;
	new_url=url;
	if(new_url.substring(0,2)=="//") new_url=get_proto(new_url,proxy_current_url)+":"+new_url;
	if(!check_proto(new_url)) new_url=get_proto(new_url,proxy_current_url)+"://"+servername(proxy_current_url)+filepath(url);
	if(proxy_encode_urls) new_url=proxenc(new_url);
	else new_url=encodeURIComponent(new_url);
	if(addproxy) new_url=proxy_this_script+"?<?=COOK_PREF?>_"+(proxy_encode_urls?"e":"")+"url="+new_url;
	url=url.replace(/^url\(([^)]*)\)$/i,"\$1");
	if(resturl!=null) new_url="url("+new_url+resturl;
	if(urlquote!=null) new_url=urlquote+new_url+urlquote;
	return new_url;
}

function parse_html(regexp,partoparse,html,addproxy){
	if(html.match(regexp)){
		matcharr=preg_match_all(regexp,html);
		for(i=0;i<matcharr.length;i++){
			match=matcharr[i];
			nurl=surrogafy_url(match[partoparse],addproxy);
			nhtml=match[0].replace(match[partoparse],nurl);
			html=html.replace(match[0],nhtml);
		}
	}
	return html;
}

function parse_all_html(){
	html=arguments[0];
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

function proxy_form_encode(form){
	if(form.method=='post') return true;
	action=(proxy_encode_urls?form.<?=COOK_PREF?>_eurl.value:form.<?=COOK_PREF?>_url.value);
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

//</script><? exit(); }
## END JAVASCRIPT ##

$postandget=array_merge($_GET,$_POST);
if(empty($postandget[COOK_PREF.'_url']) && empty($postandget[COOK_PREF.'_eurl'])){

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

$ipregexp="/^((?:[0-2]{0,2}[0-9]{1,2}\.){3}[0-2]{0,2}[0-9]{1,2})\:([0-9]{1,5})$/";

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
<form method="post" onsubmit="if(this.<?=COOK_PREF?>_encode_urls.checked){this.<?=COOK_PREF?>_eurl.value=proxenc(this.<?=COOK_PREF?>_url.value);this.<?=COOK_PREF?>_url.value='';this.submit();}">
<input type="hidden" name="<?=COOK_PREF?>_set_values" value="1" />
<input type="hidden" name="<?=COOK_PREF?>_eurl" />
<table>
<tr>
	<td style="text-align: left">URL:</td>
	<td><input type="text" name="<?=COOK_PREF?>_url" id="url" style="width: 99%" /></td>
</tr>
<tr>
	<td style="text-align: left">Proxy Server:</td>
	<td><table cellspacing="0" cellpadding="0">
	<tr>
		<td style="width: 100%"><input type="text" name="<?=COOK_PREF?>_pip" onkeyup="if(this.value.match(<?=$ipregexp?>)){ document.forms[0].<?=COOK_PREF?>_pport.value=this.value.replace(<?=$ipregexp?>,'\$2'); this.value=this.value.replace(<?=$ipregexp?>,'\$1'); document.forms[0].<?=COOK_PREF?>_pport.focus(); };" style="width: 100%; text-align: left" value="<?=($_COOKIE[COOK_PREF.'_pip'])?>" /></td>
		<td style="width: 5px">&nbsp;&nbsp;</td>
		<td style="width: 50px"><input type="text" name="<?=COOK_PREF?>_pport" maxlength="5" size="5" style="width: 50px" value="<?=($_COOKIE[COOK_PREF.'_pport'])?>" /></td>
	</tr>
	</table></td>
</tr>
<tr>
	<td style="text-align: left">User-Agent:</td>
	<td><select name="<?=COOK_PREF?>_useragent" style="width: 100%" onchange="if(this.value=='1'){ document.getElementById('useragent_texttr').style.display=(document.all?'inline':'table-row'); document.getElementById('<?=COOK_PREF?>_useragenttext').focus(); } else document.getElementById('useragent_texttr').style.display='none';">
<? foreach($useragent_array as $useragent){ ?>
		<option value="<?=($useragent[0])?>"<? if($_COOKIE[COOK_PREF.'_useragent']==$useragent[0]) echo " selected=\"selected\""; ?>><?=($useragent[1])?></option>
<? } ?>
	</select>
	</td>
</tr>
<tr id="useragent_texttr"<?=(($_COOKIE[COOK_PREF.'_useragent']=="1")?"":" style=\"display: none\"")?>>
	<td>&nbsp;</td>
	<td><input type="text" id="<?=COOK_PREF?>_useragenttext" name="<?=COOK_PREF?>_useragenttext" value="<?=($_COOKIE[COOK_PREF.'_useragenttext'])?>" style="width: 99%" /></td>
</tr>
<tr><td>&nbsp;</td><td style="text-align: left"><input type="checkbox" name="<?=COOK_PREF?>_remove_cookies" style="border: 0px" <? if(!empty($_COOKIE[COOK_PREF.'_remove_cookies'])) echo "checked=\"checked\" "; ?>/>&nbsp;Remove Cookies</td></tr>
<tr><td>&nbsp;</td><td style="text-align: left"><input type="checkbox" name="<?=COOK_PREF?>_remove_referer" style="border: 0px" <? if(!empty($_COOKIE[COOK_PREF.'_remove_referer'])) echo "checked=\"checked\" "; ?>/>&nbsp;Remove Referer Field</td></tr>
<tr><td>&nbsp;</td><td style="text-align: left"><input type="checkbox" name="<?=COOK_PREF?>_remove_scripts" style="border: 0px" <? if(!empty($_COOKIE[COOK_PREF.'_remove_scripts'])) echo "checked=\"checked\" "; ?>/>&nbsp;Remove Scripts (JS, VBS, etc)</td></tr>
<tr><td>&nbsp;</td><td style="text-align: left"><input type="checkbox" name="<?=COOK_PREF?>_remove_objects" style="border: 0px" <? if(!empty($_COOKIE[COOK_PREF.'_remove_objects'])) echo "checked=\"checked\" "; ?>/>&nbsp;Remove Objects (Flash, Java, etc)</td></tr>
<tr><td>&nbsp;</td><td style="text-align: left"><input type="checkbox" name="<?=COOK_PREF?>_encode_urls" style="border: 0px" <? if(!empty($_COOKIE[COOK_PREF.'_encode_urls'])) echo "checked=\"checked\" "; ?>/>&nbsp;Encode URLs<noscript><b>**</b></noscript></td></tr>
<tr><td>&nbsp;</td><td style="text-align: left"><input type="checkbox" name="<?=COOK_PREF?>_encode_cooks" style="border: 0px" <? if(!empty($_COOKIE[COOK_PREF.'_encode_cooks'])) echo "checked=\"checked\" "; ?>/>&nbsp;Encode Cookies<noscript><b>**</b></noscript></td></tr>
<tr><td colspan="2"><input type="submit" value="Surrogafy" style="width: 100%; background-color: #F0F0F0" /></td></tr>
</table>
<br />
<div style="font-size: 10pt">Surrogafier v<?=VERSION?>
<br />
&copy; CopyLeft 2006 <a href="http://bcable.net/">Brad Cable</a></div>
<noscript>
<br />
<b>**</b> Surrogafier has detected that your browser does not have Javascript enabled. <b>**</b>
<br />
<b>**</b> This feature requires Javascript in order to function to its full potential. <b>**</b>
</form>
</center>
</body>
</html>

<? }
else{

## PROXY FUNCTIONS ##

# class for URL
class aurl{
	var $url,$topurl,$locked,$urlreg;
	function aurl($url,$topurl=null){
		$this->urlreg="/^(?:([a-z]*)?(?:\:?\/\/))(?:([^\@\/]*)\@)?([^\/:\?\#\&]*)(?:\:([0-9]+))?(\/[^\&\?\#]*?)?([^\/\?\#\&]*(?:\&[^\?\#]*)?)(?:\?(.*?))?(?:\#(.*))?$/i";
		$this->url=preg_replace(array("/&amp;/i","/&#([0-9]+);/e"),array("&","chr(\\1)"),trim($url));
		$this->topurl=$topurl;
		$this->locked=(preg_match("/^(?:(?:javascript|mailto|about):|~|%7e)/i",$url)?true:false);
		$this->parseit();
	}

	function parseit(){
		$this->set_proto($this->get_proto());
		$this->set_servername($this->get_servername());
		$this->set_portval($this->get_portval());
		$this->set_path($this->get_path());
		if(!$this->locked && !preg_match($this->urlreg,$this->url)) havok(7,$this->url);
	}

	function get_fieldreq($fieldno,$value){
		$fieldreqs=array(2 => "://".($value!=""?"$value@":""), 4 => ($value!="" && $value!=80?":$value":""), 7 => ($value!=""?"?$value":""), 8 => ($value!=""?"#$value":""));
		if(!array_key_exists($fieldno,$fieldreqs)) return $value;
		else return $fieldreqs[$fieldno];
	}

	function set_field($fieldno,$value){
		if($this->locked) return;
		$replacestr="";
		for($i=1;$i<=8;$i++){
			if($i==$fieldno) $replacestr.=$this->get_fieldreq($fieldno,$value);
			else{
				$fieldreq=$this->get_fieldreq($i,$this->get_field($i));
				$replacestr.=(empty($fieldreq)?"\\0".$i:$fieldreq);
			}
		}
		$this->url=preg_replace($this->urlreg,$replacestr,$this->url);
	}

	function get_field($fieldno){ return preg_replace($this->urlreg,"\\".$fieldno,$this->url); }

	function get_userpass(){ return $this->get_field(2); }
	function set_userpass($userpass=""){ $this->set_field(2,$userpass); }

	function get_servername(){ return $this->get_field(3); }
	function set_servername($servername=""){ $this->set_field(3,$servername); }

	function get_portval(){ $port=$this->get_field(4); return ($port==""?($this->get_proto()=="https"?"443":"80"):$port); }
	function set_portval($port=""){ $this->set_field(4,strval($port)); }

	function get_path(){ return $this->get_field(5); }
	function set_path($path=""){ $this->set_field(5,($path==""?"/":$path)); }

	function get_file(){ return $this->get_field(6); }
	function set_file($file=""){ $this->set_field(6,$file); }

	function get_query(){ return $this->get_field(7); }
	function set_query($query=""){ $this->set_field(7,$query); }

	function get_label(){ return $this->get_field(8); }
	function set_label($label=""){ $this->set_field(8,$label); }

	function set_proto($proto="http"){
		if($this->locked) return;
		if($this->url!=preg_replace($this->urlreg,"\\1",$this->url)) $this->set_field(1,$proto);
		else{
			if($this->topurl==null) $this->url="http://".preg_replace("/^([\/:])/i","",$this->url)."/";
			else{
				$newurl=$this->topurl->get_proto().$this->get_fieldreq(2,$this->topurl->get_userpass()).$this->topurl->get_servername();
				if(!preg_match("/^\//i",$this->url)) $newurl.=$this->topurl->get_path();
				$this->url=$newurl.$this->url;
				$this->set_portval($this->topurl->get_portval());
			}
		}
	}
	function get_proto(){ return $this->get_field(1); }

	function surrogafy(){
		if($this->get_proto().$this->get_fieldreq(2,$this->get_userpass()).$this->get_servername().$this->get_path().$this->get_file()==THIS_SCRIPT || $this->locked) return $this->url;
		$label=$this->get_label();
		$this->set_label();
		$url=$this->url;
		if(ENCODE_URLS && !$this->locked) $url=proxenc($url);
		$url=THIS_SCRIPT."?".COOK_PREF."_".URLVAR."=".(!ENCODE_URLS?urlencode($url):$url);
		$this->set_label($label);
		return $url;
	}
}

function surrogafy_url($url,$topurl=false,$addproxy=true){
	global $curr_urlobj;
	if(preg_match("/^([\"']).*\\1$/i",$url)>0){
		$urlquote=substr($url,0,1);
		$url=substr($url,1,strlen($url)-2);
	}
	$urlobj=new aurl($url,$topurl);
	if($topurl===false) $topurl=$curr_urlobj;
	$new_url=($addproxy?$urlobj->surrogafy():$urlobj->url);
	if(!empty($urlquote)) $new_url=$urlquote.$new_url.$urlquote;
	return $new_url;
}

function proxdec($url){
	if(substr($url,0,1)!="~" && strtolower(substr($url,0,3))!="%7e") return $url;
	while(preg_match("/%/",$url)) $url=urldecode($url);
	while(substr($url,0,1)=="~" || strtolower(substr($url,0,3))=="%7e"){
		$url=substr($url,1,strlen($url)-1);
		$url=base64_decode($url);
		$new_url="";
		for($i=0;$i<strlen($url);$i++){
			$char=ord(substr($url,$i,1));
			$char-=ord(substr(SESS_PREF,$i%strlen(SESS_PREF),1));
			while($char<32) $char+=94;
			$new_url.=chr($char);
		}
		$url=$new_url;
	}
	return $url;
}

function proxenc($url){
	if(substr($url,0,1)=="~" || strtolower(substr($url,0,3))=="%7e") return $url;
	$new_url="";
	for($i=0;$i<strlen($url);$i++){
		$char=ord(substr($url,$i,1));
		$char+=ord(substr(SESS_PREF,$i%strlen(SESS_PREF),1));
		while($char>126) $char-=94;
		$new_url.=chr($char);
	}
	return urlencode("~".base64_encode($new_url));
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

function havok($errorno,$arg1=null,$arg2=null,$arg3=null){
	global $curr_url;
	$url=$curr_url;
	switch($errorno){
		case 1:
			$et="Bad IP Address";
			$ed="The IP address given ($arg2) is an impossible IP address, or the domain given ($arg1) was resolved to an impossible IP address.";
			break;
		case 2:
			$et="Address is Blocked";
			$ed="The administrator of this proxy service has decided to block this address, domain, or subnet.\n<br /><br />\nDomain: $arg1\n<br />\nAddress: $arg2";
			break;
		case 3:
			$et="Could Not Resolve Domain";
			$ed="The domain of the URL given ($arg1) could not be resolved due to DNS issues or an errorneous domain name.";
			break;
		case 4:
			$et="Bad Filters";
			$ed="The administrator of this proxy has incorrectly configured his domain filters, or a domain given could not be resolved.";
			break;
		case 5:
			$et="Domain is Blocked";
			$ed="The administrator of this proxy has decided to block this domain.";
			break;
		case 6:
			$et="Could Not Connect to Server";
			$ed="An error has occurred while attempting to connect to \"$arg1\" on port \"$arg2\".";
			break;
		case 7:
			$et="Invalid URL";
			$ed="The URL below was detected to be an invalid URL.";
			$url=$arg1;
			break;
	}
	$ed.="\n<br /><br />\nURL:&nbsp;$url";
?>
<div style="font-family: Bitstream Vera Sans, Arial"><div style="border: 3px solid #FFFFFF; padding: 2px">
	<div style="float: left; border: 1px solid #602020; padding: 1px; background-color: #FFFFFF">
	<div style="float: left; background-color: #801010; color: #FFFFFF; font-weight: bold; font-size: 54px; padding: 2px; padding-left: 12px; padding-right: 12px">!</div>
	</div>
	<div style="float: left; width: 500px; padding-left: 20px">
		<div style="border-bottom: 1px solid #000000; font-size: 12pt; text-align: center; font-weight: bold; padding: 2px">Error: <?=$et?></div>
		<div style="padding: 6px"><?=$ed?></div>
	</div>
</div></div>
<?	exit();
}

function ipbitter($ipaddr){
	$ipsplit=explode(".",$ipaddr);
	for($i=0;$i<count($ipsplit);$i++){
		$ipsplit[$i]=decbin($ipsplit[$i]);
		$ipsplit[$i]=$ipsplit[$i].str_repeat("0",8-strlen($ipsplit[$i]));
	}
	return implode("",$ipsplit);
}

function ipcompare($iprange,$ip){
	$iprarr=split("/",$iprange);
	$ipaddr=$iprarr[0];
	$mask=$iprarr[1];
	$maskbits=str_repeat("1",32-$mask).str_repeat("0",$mask);
	$ipbits=ipbitter($ipaddr);
	$ipbits2=ipbitter($ip);
	return (($ipbits & $maskbits) == ($ipbits2 & $maskbits));
}


function ip_check($ip,$mask=false){
	$ipseg="(?:[01]?[0-9]{1,2}|2(?:5[0-5]|[0-4][0-9]))";
	return preg_match("/^(?:$ipseg\.){3}$ipseg".($mask?"\/[0-9]{1,2}":"")."$/i",$ip);
}

function get_check($address){
	global $blocked_addresses;
	$address=preg_replace("/^.*?([^\/]+)$/","\\1",$address);
	$ipc=ip_check($address);
	$addressip=(ip_check($address)?$address:gethostbyname($address));
	if($addressip=="") havok(3,$address);
	if(!ip_check($addressip)) havok(1,$address,$addressip);
	foreach($blocked_addresses as $badd){
		if(!$ipc) if(strlen($badd)<=strlen($address) && substr($address,strlen($address)-strlen($badd),strlen($badd))==$badd) havok(5);
		if($badd==$addressip) havok(2,$address,$addressip);
		elseif(ip_check($badd,true)){ if(ipcompare($badd,$addressip)) havok(2,$address,$addressip); }
		else{
			$baddip=gethostbyname($badd);
			if($baddip=="") havok(4);
			if($baddip==$addressip) havok(2,$address,$addressip);
		}
	}
	return true;
}

function httpclean($str){ return preg_replace("/([^-_\.0-9a-z])/e","'%'.strtoupper(dechex(ord(\"\\1\")))",$str); }

function getpage($url){

	global $headers,$out,$post_vars,$proxy_variables,$referer;

	$urlobj=new aurl($url);
	$query=$urlobj->get_query();
	$requrl=$urlobj->get_path().$urlobj->get_file().(!empty($query)?"?$query":"");

	if(!empty($_COOKIE[COOK_PREF."_pip"]) && !empty($_COOKIE[COOK_PREF."_pport"])){
		$servername=$_COOKIE[COOK_PREF."_pip"];
		$portval=intval($_COOKIE[COOK_PREF."_pport"]);
		$requrl=$urlobj->url;
	}
	else{
		$servername=($urlobj->get_proto()=="ssl" || $urlobj->get_proto()=="https"?"ssl://":"").$urlobj->get_servername();
		$portval=$urlobj->get_portval();
	}

	get_check($servername);
	$fp=@pfsockopen($servername,$portval,$errno,$errval,5) or havok(6,$servername,$portval);
	$ub=stream_get_meta_data($fp);
	$ub=$ub['unread_bytes'];
	if($ub>0) fread($fp,$ub);

	$out="{$_SERVER['REQUEST_METHOD']} ".str_replace(" ","%20",$requrl)." HTTP/1.1\r\nHost: ".$urlobj->get_servername()."\r\n";

	if(!empty($_COOKIE[COOK_PREF."_useragent"])){
		$useragent_cook=($_COOKIE[COOK_PREF."_useragent"]==1?$_COOKIE[COOK_PREF."_useragenttext"]:$_COOKIE[COOK_PREF."_useragent"]);
		if(!empty($useragent_cook)) $out.="User-Agent: $useragent_cook\r\n";
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

	if(empty($_COOKIE[COOK_PREF."_remove_referer"]) && !empty($referer)) $out.="Referer: ".str_replace(" ","+",$referer)."\r\n";
	if($_SERVER['REQUEST_METHOD']=="POST") $out.="Content-Length: ".strlen($post_vars)."\r\nContent-Type: application/x-www-form-urlencoded\r\n";

	$cook_prefdomain=preg_replace("/^www\./i","",$urlobj->get_servername());
	$cook_prefix=str_replace(".","_",$cook_prefdomain).COOKIE_SEPARATOR;
	if(count($_COOKIE)>0 && empty($_COOKIE[COOK_PREF.'_remove_cookies'])){
		$addtoout="Cookie:";
		reset($_COOKIE);
		while(list($key,$val)=each($_COOKIE)){
			if(str_replace(COOKIE_SEPARATOR,"",$key)==$key) continue;
			$cook_domain=preg_replace("/^(.*".COOKIE_SEPARATOR.").*$/","\\1",$key);
			if(substr($cook_prefix,strlen($cook_prefix)-strlen($cook_domain),strlen($cook_domain))!=$cook_domain) continue;
			$key=substr($key,strlen($cook_domain),strlen($key)-strlen($cook_domain));
			if(ENCODE_COOKS) $val=proxdec($val);
			if(!in_array($key,$proxy_variables)) $addtoout.=" $key=$val;";
		}
		if($addtoout!="Cookie:"){
			$addtoout.="\r\n";
			$out.=$addtoout;
		}
	}

	$out.="Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5\r\n".
	      "Accept-Language: en-us,en;q=0.5\r\n".
	      "Accept-Encoding: gzip,deflate\r\n".
	      "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n".  //*/
	      "Keep-Alive: 300\r\n".
	      "Connection: keep-alive\r\n".                          /*/
	      "Connection: close\r\n".                               //*/
	      #(!empty($_SERVER['HTTP_CACHE_CONTROL'])?"Cache-Control: ".$_SERVER['HTTP_CACHE_CONTROL']:"").
	      "\r\n".$post_vars;
	fwrite($fp,$out);

	function get_byte($fp){
		$byte=fread($fp,1);
		return ($byte=="\r"?get_byte($fp):$byte);
	}

	$response="100";
	while($response=="100"){
		$responseline=fread($fp,12);
		$response=substr($responseline,-3,3);

		$headers=$responseline.fread($fp,1);
		while(true){
			$chunk="";
			$byte=get_byte($fp);
			if($byte=="\n") break;
			while($byte!="\n"){
				$chunk.=$byte;
				$byte=get_byte($fp);
			}
			$headers.=$chunk.$byte;
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
			if(ENCODE_COOKS) $cook_val=proxenc($cook_val);
			$_COOKIE[$cook_name]=$cook_val;
			setcookie($cook_name,$cook_val);
		}
	}

	if(preg_match("/^30[0-35-9]/i",$response)){
		$urlobj=new aurl($url);
		$redirurl=surrogafy_url(header_value("Location"),$urlobj);
		#if(header_value("Connection")=="close") fclose($fp);
		header("Location: $redirurl");
		exit();
	}

	if(header_value("Last-Modified")!="") header("Last-Modified: ".header_value("Last-Modified"));

	if(header_value("Content-Type")!=""){
		header("Content-Type: ".header_value("Content-Type"));
		if(!preg_match("/^(?:text|application\/x-javascript)/i",header_value("Content-Type"))){
			if(header_value("Content-Disposition")!="") header("Content-Disposition: ".header_value("Content-Disposition"));
			/*else{ $file=preg_replace("/^.*\/([^\/#\?]*).*?$/i","\\1",$url);
				if(!empty($file)) header("Content-Disposition: attachment; filename=$file"); }*/
		}
	}

	if(substr(header_value("Content-Type"),0,4)=="text" || substr(header_value("Content-Type"),0,24)=="application/x-javascript"){
		$justoutput=false;
		$justoutputnow=false;
	}
	else{
		$justoutputnow=(header_value("Content-Encoding")=="gzip"?false:true);
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

	#echo $out."\n\n\n".$headers;
	#if(header_value("Connection")=="close") fclose($fp);
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
global $proxy_variables,$proxy_varblacklist,$post_vars,$cookies,$curr_url,$curr_urlobj,$referer,$blocked_addresses;

$urlvar=((!empty($postandget[COOK_PREF.'_encode_urls']) || !empty($_COOKIE[COOK_PREF.'_encode_urls']))?"e":"")."url";

$curr_url=$postandget[COOK_PREF.'_'.$urlvar];

while(preg_match("/%/",$curr_url)) $curr_url=urldecode($curr_url);
$curr_url=stripslashes($curr_url);

$proxy_variables=array(COOK_PREF."_url",COOK_PREF."_eurl",COOK_PREF."_pip",COOK_PREF."_pport",COOK_PREF."_useragent",COOK_PREF."_useragenttext",COOK_PREF."_remove_cookies",COOK_PREF."_remove_referer",COOK_PREF."_remove_scripts",COOK_PREF."_remove_objects",COOK_PREF."_encode_urls",COOK_PREF."_encode_cooks");
$proxy_varblacklist=array(COOK_PREF."_url",COOK_PREF."_eurl");

if($postandget[COOK_PREF.'_set_values']){
	if($postandget[COOK_PREF."_useragent"]!="1"){
		unset($postandget[COOK_PREF."_useragenttext"]);
		$_COOKIE[COOK_PREF."_useragenttext"]=false;
		setcookie(COOK_PREF."_useragenttext",false,0);
	}
	while(list($key,$val)=each($proxy_variables)){
		if(!in_array($val,$proxy_varblacklist)){
			$_COOKIE[$val]=false;
			setcookie($val,false,0);
			if(isset($postandget[$val]) && !empty($postandget[$val])){
				$_COOKIE[$val]=$postandget[$val];
				setcookie($val,$postandget[$val]);
			}
		}
	}
	define("ENCODE_URLS",!empty($postandget[COOK_PREF.'_encode_urls']));
	$urlvar=(ENCODE_URLS?"e":"")."url";
	define("URLVAR",$urlvar);
	$theurl=$postandget[COOK_PREF.'_'.URLVAR];
	$theurl=surrogafy_url((ENCODE_URLS?proxdec($theurl):$theurl),null);
	header("Location: $theurl");
	exit();
}
# end #

# Deal with GET/POST/COOKIES and the URL #
define("URLVAR",$urlvar);
define("ENCODE_URLS",!empty($_COOKIE[COOK_PREF.'_encode_urls']));
define("ENCODE_COOKS",!empty($_COOKIE[COOK_PREF.'_encode_cooks']));
if(ENCODE_URLS) $curr_url=proxdec($curr_url);
$referer=proxdec(urldecode(preg_replace("/^([^\?]*)(\?".COOK_PREF."_".URLVAR."=)?/i","",$_SERVER["HTTP_REFERER"])));

$getkeys=array_keys($_GET);
foreach($getkeys as $getvar){ if(!in_array($getvar,$proxy_variables)){ $curr_url.=(!preg_match("/\?/i",$curr_url)?"?":"&")."$getvar=".urlencode($_GET[$getvar]); } }

$post_vars="";
$postkeys=array_keys($_POST);
foreach($postkeys as $postkey){ if(!in_array($postkey,$proxy_variables)){ $post_vars.=($post_vars!=""?"&":"")."$postkey=".httpclean(urldecode($_POST[$postkey])); } }

# end #

# Get the page #
$pagestuff=getpage($curr_url);
$body=$pagestuff[0];

# For AJAX, some things quote the entire HTML of a page... this makes sure it doesn't parse inside of that
if(preg_match("/^[\t\r\n ]*\".*\"[\t\r\n ]*$/i",$body)>0){
	echo $body;
	exit();
}

$curr_url=$pagestuff[1];
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

global $regexp_arrays;

$jsattrs="(href|src|location|background|backgroundImage|pluginspage|codebase|img)";
$jshtmlattrs="(innerHTML)";
$jsmethods="(location\.replace)";
$jslochost="(location\.host(?:name){0,1})";
$jsrealpage="((?:(?:document|window)\.){0,1}location(?:(?=[^\.])|\.(?!hash|host|hostname|pathname|port|protocol|search)[a-z]+)|document\.documentURI|[a-z]+\.referrer)";

$anyspace="[\t\r\n ]*";
$plusspace="[\t\r\n ]+";
$spacer="[\t ]*";
$htmlattrs="(href|src|background|pluginspage|codebase)";
$jsvarobj="(?:[a-zA-Z0-9\._\(\)\[\]\+-]+)";
$quoteseg="(?:(?:\"(?:(?:[^\"]|[\\\\]\")*?)\")|(?:'(?:(?:[^']|[\\\\]')*?)')";
$jsquotereg="((?:(?:$anyspace$quoteseg|$jsvarobj)$anyspace\+)*)$anyspace$quoteseg|$jsvarobj)$spacer(?=[;\}\n\r]))";
$jsend="(?=${anyspace}[;\}\n\r\'\"])";
$htmlreg="($quoteseg|(?:[^\"'\\\\][^> ]*)))";

$base=preg_replace("/^.*<base[^>]* href$anyspace=$anyspace{$htmlreg}[^>]*>.*$/is","\\1",$body);
$body=preg_replace("/<base[^>]* href$anyspace=$anyspace{$htmlreg}[^>]*>/i","",$body);
if(!empty($base) && $base!=$body && strlen($base)<100){
	if(preg_match("/^([\"']).*\\1$/i",$base)>0) $base=substr($base,1,strlen($base)-2);
	$curr_url=$base;
}

$curr_urlobj=new aurl($curr_url);

$js_regexp_arrays=array(
	array(1,2,"/([^a-z0-9])${jsrealpage}([^a-z0-9])/i","\\1proxy_current_url\\3"),
	array(1,2,"/([^a-z])$jslochost([^a-z])/i","\\1proxy_location_hostname\\3"),
	array(1,2,"/([^a-z]$jsmethods$anyspace\()([^)]*)\)/i","\\1surrogafy_url(\\3))"),
	array(1,2,"/(\.$jsattrs$anyspace=(?:(?:$anyspace$jsvarobj$anyspace=)*)$anyspace)($jsquotereg(?:\+$jsquotereg)*)$jsend/i","\\1surrogafy_url(\\3)"),
	array(1,2,"/(\.$jshtmlattrs$anyspace=(?:(?:$anyspace$jsvarobj$anyspace=)*)$anyspace)($jsquotereg(?:\+$jsquotereg)*)$jsend/i","\\1parse_all_html(\\3)"),
	array(1,2,"/\.action($anyspace=(?:(?:$anyspace$jsvarobj$anyspace=)*)$anyspace)($jsquotereg(?:\+$jsquotereg)*)$jsend/i",".".COOK_PREF."_".URLVAR.".value\\1surrogafy_url(\\3,false)"),
	array(1,2,"/(\.setattribute$anyspace\($anyspace(\"|')$jsattrs(\\2)$anyspace,$anyspace)(.*?)$jsend/i","\\1surrogafy_url(\\5)"),
	array(1,2,"/(([^ {>\t\r\n=;]+)$anyspace=(?:{$anyspace}new$anyspace|$anyspace)XMLHttpRequest(?:\(\);|;))/i","\\1\n\\2._realopen=\\2.open;\n\\2.open=proxy_XMLHttpRequest_open;"),
	array(1,2,"/(([^ {>\t\r\n=;]+)$anyspace=(?:{$anyspace}new$anyspace|$anyspace)ActiveXObject$anyspace\($anyspace([\"'])[a-z0-9]*\.XMLHTTP\\3$anyspace\)[;]{0,1})/i","\\1\n\\2._realopen=\\2.open;\n\\2.open=proxy_XMLHttpRequest_open;"),
	(ENCODE_URLS?array(1,2,"/((?:[^\) \{\}]*(?:\)\.{0,1}))+)(\.submit$anyspace\(\))$jsend/i","void((\\1.method=='post'?null:\\1\\2));"):""),
);

$regexp_arrays=array(
	"text/html" => array(
		array(1,1,"/( on[a-z]{3,20}$anyspace=$anyspace)(?:(\"[^\"]+[^;\"])(\")|('[^']+[^;'])('))/i","\\1\\2;\\3"),
		array(1,1,"/(<form(?:(?!action)[^>])+>)/i","\\1\n<input type=\"hidden\" name=\"".COOK_PREF."_".URLVAR."\" value=\"$curr_url\" />\n"),
		array(1,1,"/(<form[^>]*?) action$anyspace=$anyspace{$htmlreg}([^>]*>)/i","\\1\\3\n<input type=\"hidden\" name=\"".COOK_PREF."_".URLVAR."\" value=\\2 />\n"),

		(ENCODE_URLS?array(1,1,"/(<form[^>]*?)>/i","\\1 onsubmit=\"return proxy_form_encode(this);\">"):null),
		(ENCODE_URLS?array(1,1,"/(<input[^>]*? type$anyspace=$anyspace(?:\"submit\"|'submit'|submit)[^>]*?[^\/])((?:[ ]?[\/])?>)/i","\\1 onclick=\"proxy_form_button=this.name;\"\\2"):null),

		array(2,1,"/ name=\"".COOK_PREF."_".URLVAR."\" value$anyspace=$anyspace{$htmlreg} \/>/i",1,false),
		array(2,1,"/<[a-z][^>]*$plusspace$htmlattrs$anyspace=$anyspace{$htmlreg}[^>]*>/i",2),
		array(2,2,"/<script[^>]*?{$plusspace}src$anyspace=$anyspace([\"'])$anyspace(.*?[^\\\\])\\1[^>]*>$anyspace<\/script>/i",2),
		array(2,1,"/<meta[^>]*{$plusspace}http-equiv$anyspace=$anyspace([\"'])refresh\\1[^>]* content$anyspace=$anyspace([\"'])[ 0-9\.;\t\\r\n]*url=(.*?)\\2[^>]*>/i",3),
		array(2,1,"/<meta[^>]*{$plusspace}http-equiv$anyspace={$anyspace}refresh [^>]*content$anyspace=$anyspace([\"'])[ 0-9\.;\t\\r\n]*url=(.*?)\\1[^>]*>/i",2),
		array(1,1,"/(<meta[^>]*{$plusspace}http-equiv$anyspace=$anyspace([\"'])set-cookie\\2[^>]* content$anyspace=$anyspace)([\"'])(.*?[^\\\\])$anyspace\\3/i","\\1\\3".PAGECOOK_PREFIX."\\4\\3"),
		array(1,1,"/(<meta[^>]*{$plusspace}http-equiv$anyspace={$anyspace}set-cookie[^>]* content$anyspace=$anyspace)([\"'])(.*?[^\\\\])$anyspace\\2/i","\\1\\2".PAGECOOK_PREFIX."\\3\\2")
	),
	"text/css" => array(
		array(2,1,"/[^a-z]url\($anyspace(\"|')(.*)(\\1)$anyspace\)/i",2),
		array(2,1,"/[^a-z]url\($anyspace([^\"'\\\\].*)$anyspace\)/i",1),
		array(2,1,"/@import$plusspace(\"|')(.*)(\\1);/i",2)
	),
	"application/x-javascript" => $js_regexp_arrays,
	"text/javascript" => $js_regexp_arrays
);

## END REGEXPS ##


# Parsing Functions #

function parse_html($regexp,$partoparse,$html,$addproxy){
	global $curr_urlobj;
	$offset=0;
	while(preg_match($regexp,$html,$matcharr,PREG_OFFSET_CAPTURE,$offset)){
		$nurl=surrogafy_url($matcharr[$partoparse][0],$curr_urlobj,$addproxy);
		$begin=$matcharr[$partoparse][1];
		$len=strlen($matcharr[$partoparse][0]);
		$end=$matcharr[$partoparse][1]+$len;
		$html=substr($html,0,$begin).str_replace($matcharr[$partoparse][0],$nurl,substr($html,$begin,$len)).substr($html,$end,strlen($html)-$end);
		$offset=$matcharr[count($matcharr)-1][1]+strlen($nurl);
	}
	return $html;
}

function regular_express($regexp_array,$thevar){
	if($regexp_array[0]==1) $thevar=preg_replace($regexp_array[2],$regexp_array[3],$thevar);
	elseif($regexp_array[0]==2){
		$addproxy=((count($regexp_array)<5)?true:false);
		$thevar=parse_html($regexp_array[2],$regexp_array[3],$thevar,$addproxy);
	}
	return $thevar;
}

function parse_all_html($html){
	global $regexp_arrays;
	reset($regexp_arrays);
	$splitarr=preg_split("/(<script.*?<\/script>)/is",$html,-1,PREG_SPLIT_DELIM_CAPTURE);
	unset($html);
	while(list($key,$arr)=each($regexp_arrays)){
		if(CONTENT_TYPE==$key || (CONTENT_TYPE=="text/html" && $key!="text/javascript")){
			foreach($arr as $regexp_array){
				$inc=1;
				if(CONTENT_TYPE=="text/html" && $regexp_array[1]==1) $inc=2;
				for($i=0;$i<count($splitarr);$i+=$inc){
					if($regexp_array[1]==2 && $i%2==0 && CONTENT_TYPE=="text/html"){
						$splitarr2=preg_split("/( on[a-z]{3,20}=(?:\"(?:[^\"]+)\"|'(?:[^']+)'|[^\"' >][^ >]+[^\"' >]))/is",$splitarr[$i],-1,PREG_SPLIT_DELIM_CAPTURE);
						if(count($splitarr2)<=1) continue;
						for($j=1;$j<count($splitarr2);$j+=2) $splitarr2[$j]=regular_express($regexp_array,$splitarr2[$j]);
						$splitarr[$i]=implode("",$splitarr2);
						unset($splitarr2);
					}
					else $splitarr[$i]=regular_express($regexp_array,$splitarr[$i]);
				}
			}
		}
	}
	return implode("",$splitarr);
}

$body=parse_all_html($body);
# end #

# Code Conversion to Javascript #
function escape_regexp($regexp,$dollar=false){
	$regexp=str_replace("\\","\\\\",str_replace("'","\\'",str_replace("\"","\\\"",str_replace("\n","\\n",str_replace("\r","\\r",str_replace("\t","\\t",$regexp))))));
	return ($dollar?preg_replace("/[\\\\]+(?=[0-9])/","\\\\$",$regexp):preg_replace("/[\\\\]+(?=[0-9])/","\\\\\\\\",$regexp));
}

function convertarray_to_javascript(){
	global $regexp_arrays;
	$js="regexp_arrays=new Array(".count($regexp_arrays).");\n";
	reset($regexp_arrays);
	while(list($key,$arr)=each($regexp_arrays)){
		$js.="regexp_arrays[\"$key\"]=new Array(".count($arr).");\n";
		for($i=0;$i<count($arr);$i++){
			$js.="regexp_arrays[\"$key\"][$i]=new Array(";
			if($arr[$i][0]==1) $js.="1,".escape_regexp($arr[$i][2])."g,\"".escape_regexp($arr[$i][3],true)."\"";
			elseif($arr[$i][0]==2) $js.="2,".escape_regexp($arr[$i][2])."g,{$arr[$i][3]}".(count($arr[$i])<5?"":",false");
			$js.=");\n";
		}
	}
	return $js;
}
# end #


if(CONTENT_TYPE=="text/html"){
# || CONTENT_TYPE=="application/x-javascript"){ # <- i have NO idea where that came from, why in the world would a javascript file be requiring more javascript?
# Insert the code's Javascript #
	//$big_javascript="";/*
	$urlobj=new aurl($curr_url);
	$big_javascript="
	<link rel=\"icon\" href=\"".surrogafy_url("http://".$urlobj->get_servername()."/favicon.ico")."\" />
<script language=\"javascript\">
<!--

".convertarray_to_javascript().((!empty($_COOKIE[COOK_PREF.'_remove_objects']))?"regexp_arrays[\"text/html\"].push(Array(1,/<[\\\\/]?(embed|param|object)[^>]*>/ig,\"\"));":"")."

proxy_this_script=\"".THIS_SCRIPT."\";
proxy_current_url=\"$curr_url\";
proxy_location_hostname=\"".$urlobj->get_servername()."\";
proxy_encode_urls=".(ENCODE_URLS?"true":"false").";
proxy_form_button=null;

//-->
</script>
<script type=\"text/javascript\" src=\"".THIS_SCRIPT."?js_funcs\"></script>
";
	//*/
	if(preg_match("/<head[^>]*>/i",$body)>0) $body=preg_replace("/(<head[^>]*>)/i","\\1$big_javascript",$body,1);
	elseif(preg_match("/<script/i",$body)>0) $body=preg_replace("/<script/i","$big_javascript<script",$body,1);
	elseif(preg_match("/<\/head/i",$body)>0) $body=preg_replace("/<\/head/i","$big_javascript</head",$body,1);
	elseif(preg_match("/<body[^>]*>/i",$body)>0) $body=preg_replace("/(<body[^>]*>)/i","\\1$big_javascript",$body,1);
	elseif(preg_match("/<html[^>]*>/i",$body)>0) $body=preg_replace("/(<html[^>]*>)/i","\\1$big_javascript",$body,1);
	#else $body=$big_javascript.$body;
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
//*/
echo $body;
/*/
echo $out."\n\n".$body;
//*/

## THE END ##

} ?>
