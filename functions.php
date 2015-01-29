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

function escape_regexp($regexp,$dollar=false)
{
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

# EXITING {{{

function finish_noexit(){
	global $dns_cache_array;
	# save DNS Cache before exiting
	$_SESSION['DNS_CACHE_ARRAY']=$dns_cache_array;
}

function finish(){
	finish_noexit();
	exit();
}
