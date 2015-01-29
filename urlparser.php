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