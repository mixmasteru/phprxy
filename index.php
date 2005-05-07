<? 
# Surrogafier - Version 0.1
# Author: Brad Cable
# License: GPL Version 2
#
if(!isset($_REQUEST['url'])){ ?>
<html>
<head>
  <title>Web Proxy</title>

  <style>
    body{font-family:bitstream vera sans,arial}
    td{white-space:nowrap;padding-right:10px}
    input{border:1px solid #000000;background-color:#FFFFFF;width:100%;padding:2px}
  </style>
</head>
<body>
  <center>
    <form method="get">
      <table>
        <tr><td colspan="2" style="text-align:center;font-size:16pt;font-weight:bold">Web Proxy</td></tr>
        <tr><td>Proxy Server IP:Port</td><td><input type="text" name="ip" value="<?=($_REQUEST['ip'])?>" style="width:146px;margin-right:4px" /><input type="text" name="port" value="<?=($_REQUEST['port'])?>" style="width:50px"/></td></tr>

        <tr><td>URL to Fetch:</td><td><input type="text" name="url" style="width:200px" /></td></tr>
        <tr><td colspan="2"><input type="submit" value="View Page" /></td></tr>
      </table>
    </form>
  </center>
</body>
</html>
<? die();
}
function parseurl($url){
  return eregi_replace("\?","XqmarkX",eregi_replace("&","XampX",eregi_replace("&amp;","&",$url)));
}
function getfullpath($url){
  global $sitehost,$path;
  $url=eregi_replace("\n","",$url);
  if(strtolower(substr($url,0,7)!="http://")){
    if(substr($url,0,1)=="/"){
      if(substr($url,1,1)=="/") $url="http:$url";
      else $url="http://$sitehost$url";
    }
    else $url="http://$sitehost$path".trim($url);
  }
  return $url;
}
//global $redir,$pageurl,$fp,$out,$headers,$query,$sitehost,$path,$serverurl;
// echo(count($_COOKIE));
function getworkingpath($url){
  global $sitehost,$path;
  $shurl=eregi_replace("([^\?]+)\?(.*)","\\1",$url);
  if(strtolower(substr($shurl,0,7))=="http://") $sitehost=spliti("/",substr($shurl,7));
  else $sitehost=$shurl;
  // $packethost=((substr($sitehost[0],0,4)=="www.")?substr($sitehost[0],4):$sitehost[0]);
  if(count($sitehost)<=2) $path="/";
  else{
    for($i=1;$i<=count($sitehost)-2;$i++) $pathar[]=$sitehost[$i];
    $path="/".implode("/",$pathar)."/";
  }
  $sitehost=$sitehost[0];
}
function geturl($url){
  global $redir,$pageurl,$fp,$out,$headers,$query,$sitehost,$path,$serverurl,$newcookies,$cookies,$setcooks;
  // if($ignorepost==1) $_POST="";
  if(count($newcookies)>0){
    if(count($cookies)>0) $cookies=array_merge($cookies,$newcookies);
    else $cookies=$newcookies;
  }
  while(eregi("%25",$url)) $url=urldecode($url);
  if(stristr("+",$url)!==false) $url=urldecode($url);
  $url=eregi_replace(" ","+",$url);
  if(!empty($_REQUEST['ip']) && !empty($_REQUEST['port'])) $query="?ip={$_REQUEST['ip']}&port={$_REQUEST['port']}&";
  else $query="?";
  if(substr($url,0,7)!="http://") $url="http://$url";
  if(!eregi("/",substr($url,7))) $url.="/";
  $url=eregi_replace("XqmarkX","?",eregi_replace("XampX","&",$url));
  $url=eregi_replace("/\./","/",$url);
  while(eregi("/\.\./",$url)){
    $i=1;
    while(eregi(str_repeat("\.\./",$i),$url)) $i++;
    $i--;
    $url=eregi_replace(str_repeat("/([^/]+)",$i).str_repeat("/\.\.",$i),"",$url);
  }
  getworkingpath($url);
  if(!empty($_REQUEST['ip']) && !empty($_REQUEST['port'])) $fp=@fsockopen($_REQUEST['ip'],$_REQUEST['port']);
  else $fp=fsockopen($sitehost,80);
  if(!$fp) die();
  $serverurl=eregi_replace("([^/]+)(.*)","\\2",substr($url,7));
  if(empty($redir)) $request=$serverurl;
  else $request=$url;
  $out=((!empty($_POST) && count($_POST)>0)?"POST":"GET")." $request";
  // echo($_POST.count($_POST));
  if(count($_GET)>0){
    reset($_GET);
    unset($getvars);
    while(list($key,$val)=each($_GET)) if(!empty($key) && $key!="url" && $key!="ip" && $key!="port" && $key!="headers"/* && $key!="redir"*/) $getvars[]="$key=".eregi_replace("&amp;","&",urlencode($val));
    if(count($getvars)>0) $out.=(ereg("\?",$url)?"&":"?").implode("&",$getvars);
  }
  if(empty($_REQUEST['http1'])) $out.=" HTTP/1.1\r\n";
  $out.="Host: $sitehost\r\n";
  // $out.="Referer: $sitehost\r\n";
  // $out.="Referer: chkpt.zdnet.com";
  $out.="User-Agent: Windows 1.0 Special Edition Ultra Mega Super Dee Duper PLUS!\r\n";
  if(count($cookies)>0){
  // echo(count($_COOKIE));die();
    $out.="Cookie: ";
    reset($cookies);
    unset($cookarr);
    while(list($key,$val)=each($cookies)) $cookarr[]="$key=$val";
    $out.=implode("; ",$cookarr)."\r\n";
  }
  $out.="Connection: close\r\n";
  if(!empty($_POST) && count($_POST)>0){
  /*Content-Type: application/x-www-form-urlencoded
  Content-Length: 101*/
    reset($_POST);
    unset($postarr);
    while(list($key,$val)=each($_POST)) if($key!="url") $postarr[]="$key=$val";
    $posttext=implode("&",$postarr);
    $out.="Content-Type: application/x-www-form-urlencoded\r\n";
    $out.="Content-Length: ".strlen($posttext);
    $out.="\r\n\r\n";
    $out.=$posttext."\r\n";
    //echo($url);
  }
  $out.="\r\n";
  fwrite($fp,$out);
  $headers="";
  do $headers.=fread($fp,1); while(!preg_match("/\\r\\n\\r\\n$/",$headers));
  $headerarr=spliti("\n",$headers);
  for($i=0;$i<count($headerarr);$i++){
    if(eregi("location:",$headerarr[$i])){
      $location=trim(eregi_replace("([^:]+):(.*)","\\2",$headerarr[$i]));
      if(strtolower(substr($location,0,7)!="http://")){
        if(substr($location,0,1)=="/") $location="http://$sitehost$location";
        else $location="http://$sitehost$path".trim($location);
      }
    }
    else if(eregi("set-cookie:",$headerarr[$i])){
      $setcooks[]=trim($headerarr[$i]);
      $cookname=trim(eregi_replace("([^:]+):([^=]+)=([^;]*);(.*)","\\2",$headerarr[$i]));
      $cookval=trim(eregi_replace("([^:]+):([^=]+)=([^;]*);(.*)","\\3",$headerarr[$i]));
      if(!empty($cookval)){
	$newcookies[$cookname]=$cookval;
	setcookie($cookname,$cookval);
      }
      else{
        unset($newcookies[$cookname]);
        unset($cookies[$cookname]);
	setcookie($cookname,"");
      }
    }
  }
  // echo("$out<br/><br/>$headers");//."<br><br>"."Location: http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}{$query}url=".(($location=="http://$sitehost/" || (eregi($serverurl,$location) && $serverurl!="/"))?"http://$sitehost$serverurl&redir=1":"$location"));
  if(!empty($location)){
    if(($location=="http://$sitehost/" || (stristr($serverurl,$location)!==false && $serverurl!="/")) && eregi("set-cookie:",$headers)){
      $location="http://$sitehost$serverurl";
      $redir=1;
    }
    $_POST="";
    geturl($location);
    return;
  }
  $pageurl=$url;
}
function getpage(){
  global $headers,$fp;
  while(!feof($fp)) $fc.=fread($fp,4096);
  fclose($fp);
  if(eregi("transfer-encoding: chunked",$headers)){
    $uffc=$fc;
    $fc="";
    $pointer=0;
    do{
      $byte="";
      $len="";
      do{
        $len.=$byte;
        $byte=substr($uffc,$pointer++,1);
      } while($byte!="\r");
      $pointer++;
      $len=hexdec(trim($len));
      if($len!="0"){
        $fc.=substr($uffc,$pointer,$len);
        $pointer+=$len+2;
      }
    } while($len!="0");
  }
  return $fc;
}
$cookies=$_COOKIE;
if(empty($cookies)) $cookies[]="";
if(!empty($_REQUEST['ip']) && !empty($_REQUEST['port'])) $redir=1;
geturl($_REQUEST['url']);
$fc=getpage();
if(eregi("content-type:",$headers)) $mimetype=trim(eregi_replace("(.*)content-type:([^\r]+)(.*)","\\2",$headers));
if(empty($mimetype)) $mimetype="text/html";
header("Content-Type: $mimetype");
$ext=substr($pageurl,-3,3);
if(strtolower(substr($fc,0,4))=="http") $fc=substr(strstr($fc,"\r\n\r\n"),4);
if(substr($mimetype,0,4)!="text" || $mimetype=="text/plain") echo($fc);
else{
  $tags[]="url";
  $tags[]="src";
  $tags[]="href";
  $tags[]="background";
  $jstags[]="src";
  $jstags[]="href";
  $parenthtags[]="url";
  $parenthtags[]="replace";
  if(eregi("<base",$fc)){
    $baseurl=eregi_replace("(.*)<base([^>]+)href=([^>]+)>(.*)","\\3",$fc);
    $sepchr=substr($baseurl,0,1);
    if($sepchr!="\"" && $sepchr!="'") $sepchr=" ";
    if($sepchr!=" ") $baseurl=eregi_replace("$sepchr([^".addslashes(addslashes($sepchr))."]+)$sepchr(.*)","\\1",$baseurl);
    else $baseurl=eregi_replace("([^ >]+)(.*)","\\1",$baseurl);
    getworkingpath($baseurl);
  }
  $fc=eregi_replace("http://","http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}{$query}url=http://",$fc);
  foreach($tags as $tag){
    $fcarr=spliti("$tag=",$fc);
    for($i=1;$i<count($fcarr);$i++){
      $sepchr=substr($fcarr[$i],0,1);
      if($sepchr!="\"" && $sepchr!="'") $sepchr=" ";
      if($sepchr!=" "){
        $theurl=eregi_replace("$sepchr([^".addslashes(addslashes($sepchr))."]+)$sepchr(.*)","\\1",$fcarr[$i]);
        $therest=eregi_replace("$sepchr([^".addslashes(addslashes($sepchr))."]+)$sepchr(.*)","\\2",$fcarr[$i]);
      }
      else{
	$theurl=eregi_replace("([^ >]+)(.*)","\\1",$fcarr[$i]);
	$therest=eregi_replace("([^ >]+)(.*)","\\2",$fcarr[$i]);
	$sepchr="";
      }
      if(strtolower(substr($theurl,0,11))!="javascript:" && strtolower(substr($theurl,0,7))!="http://"){
        $theurl=parseurl(getfullpath($theurl));
        $fcarr[$i]="{$sepchr}http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}{$query}url=$theurl$sepchr$therest";
      }
    }
    $fc=implode("$tag=",$fcarr);
  }
  /*foreach($jstags as $jstag){
    $fcarr=spliti("\.$jstag=",$fc);
    for($i=1;$i<count($fcarr);$i++) $fcarr[$i]="\"http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}{$query}url=\"+".$fcarr[$i];
    $fc=implode(".$jstag=",$fcarr);
  }*/
  foreach($parenthtags as $parenthtag){
    $fcarr=spliti("$parenthtag\(",$fc);
    for($i=1;$i<count($fcarr);$i++){
      $sepchr=substr($fcarr[$i],0,1);
      if($sepchr!="\"" && $sepchr!="'") $sepchr=" ";
      if($sepchr!=" "){
        $theurl=eregi_replace("$sepchr([^".addslashes(addslashes($sepchr))."]+)$sepchr\)(.*)","\\1",$fcarr[$i]);
        $therest=eregi_replace("$sepchr([^".addslashes(addslashes($sepchr))."]+)$sepchr\)(.*)","\\2",$fcarr[$i]);
      }
      else{
	$theurl=eregi_replace("([^\)]+)\)(.*)","\\1",$fcarr[$i]);
	$therest=eregi_replace("([^\)]+)\)(.*)","\\2",$fcarr[$i]);
	$sepchr="";
      }
      if(strtolower(substr($theurl,0,11))!="javascript:" && strtolower(substr($theurl,0,7))!="http://"){
        $theurl=parseurl(getfullpath($theurl));
        $fcarr[$i]="{$sepchr}http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}{$query}url=".addslashes(urlencode($theurl))."$sepchr)$therest";
      }
    }
    $fc=implode("$parenthtag(",$fcarr);
  }
  $fcarr=spliti("<form",$fc);
  for($i=1;$i<count($fcarr);$i++){
    $formarr=spliti(">",$fcarr[$i]);
    $actionarr=spliti("action=",$formarr[0]);
    if(count($actionarr)!=1){
      $sepchr=substr($actionarr[1],0,1);
      if($sepchr!="\"" && $sepchr!="'") $sepchr=" ";
      if($sepchr!=" "){
        $theurl=eregi_replace("$sepchr([^".addslashes(addslashes($sepchr))."]+)$sepchr(.*)","\\1",$actionarr[1]);
        $actionarr[1]=eregi_replace("$sepchr([^".addslashes(addslashes($sepchr))."]+)$sepchr(.*)","{$sepchr}http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}$sepchr\\2",$actionarr[1]);
      }
      else{
        $theurl=eregi_replace("([^ ]+)(.*)","\\1",$actionarr[1]);
        $actionarr[1]=eregi_replace("([^ ]+)(.*)","http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}\\2",$actionarr[1]);
	$sepchr="";
      }
      if(strtolower(substr($theurl,0,11))!="javascript:" && strtolower(substr($theurl,0,7))!="http://"){
        $theurl=parseurl(getfullpath($theurl));
        $formarr[0]=implode("action=",$actionarr);
      }
    }
    else{
      $formarr[0].=" action=\"http://{$_SERVER['HTTP_HOST']}{$_SERVER['PHP_SELF']}\" ";
      $theurl=parseurl($pageurl);
    }
    $method=(eregi("method=(\"|')post",$formarr[0]) || eregi("method=post",$formarr[0]));
    $getvars="";
    if(!empty($_REQUEST['ip'])) $getvars.="\n<input type=\"hidden\" name=\"ip\" value=\"{$_REQUEST['ip']}\" />";
    if(!empty($_REQUEST['port'])) $getvars.="\n<input type=\"hidden\" name=\"port\" value=\"{$_REQUEST['port']}\" />";
    if(!empty($pageurl)) $getvars.="\n<input type=\"hidden\" name=\"url\" value=\"$theurl\" />";
    $formarr[1]="$getvars\n".$formarr[1];
    $fcarr[$i]=implode(">",$formarr);
  }
  $page=implode("<form",$fcarr);
}
if(!empty($_REQUEST['headers'])) echo("$out<br /><br />$headers<br /><br />");
echo($page); ?>
