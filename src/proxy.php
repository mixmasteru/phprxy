<?php
/**
 * proxy
 *
 * @author Ulrich Pech
 * @link https://github.com/mixmasteru
 *
 * @copyright Surrogafier, Author: Brad Cable, Email: brad@bcable.net
 * @license BSD
 */
# PROXY EXECUTION {{{

# PROXY EXECUTION: COOKIE VARIABLES {{{

$arr_proxy_variable = array('user', COOK_PREF, COOK_PREF.'_set_values',
							COOK_PREF.'_tunnel_ip',COOK_PREF.'_tunnel_port',
							COOK_PREF.'_useragent',COOK_PREF.'_useragent_custom',
							COOK_PREF.'_url_form',
							COOK_PREF.'_remove_cookies',COOK_PREF.'_remove_referer',
							COOK_PREF.'_remove_scripts',COOK_PREF.'_remove_objects',
							COOK_PREF.'_encrypt_urls',COOK_PREF.'_encrypt_cookies');

# }}}

# PROXY_EXECUTION: REDIRECT IF FORM INPUT {{{

if(IS_FORM_INPUT){
	$obj_urlparser = new urlparser(null);
	$theurl=$obj_urlparser->framify_url($obj_urlparser->surrogafy_url(ORIG_URL),PAGETYPE_FRAME_TOP);
	header("Location: {$theurl}");
	finish();
}

# }}}

# PROXY EXECUTION: REFERER {{{

$referer = null;
if($_SERVER['HTTP_REFERER']!=null && !$OPTIONS['REMOVE_REFERER']){
	$refurlobj = new aurl($_SERVER['HTTP_REFERER'], null, true);
	$referer=proxdec(preg_replace(
			'/^=(?:\&=|_\&=|\.\&=)?([^\&]*)[\s\S]*$/i','\1',
			$refurlobj->get_query()
	));
}

#$getkeys=array_keys($_GET);
#foreach($getkeys as $getvar){
#	if(!in_array($getvar,$arr_proxy_variable)){
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

$obj_regex 	= new regex($OPTIONS);
$obj_http 	= new http($CONFIG, $OPTIONS, $arr_proxy_variable, $referer);
$pagestuff	= $obj_http->getpage($curr_url);
$body		= $pagestuff[0];

$tbody=trim($body);
if(($tbody{0}=='"' && substr($tbody,-1)=='"') || ($tbody{0}=='\'' && substr($tbody,-1)=='\''))
{
	echo $body;
	finish();
}
unset($tbody);

$curr_url	 = $pagestuff[1];
$arr_headers = $obj_http->getHeaders();
define('PAGECOOK_PREFIX',$pagestuff[2]);
unset($pagestuff);
define('CONTENT_TYPE',preg_replace('/^([a-z0-9\-\/]+).*$/i','\1',$arr_headers['content-type'][0])); #*

# }}}

# PROXY EXECUTION: PAGE PARSING {{{
$base = pageparser::checkForBase($body);
if(!empty($base))
{
	$curr_url=$base;
}

$curr_urlobj	=new aurl($curr_url);
$obj_urlparser = new urlparser($curr_urlobj);
$obj_pageparser= new pageparser($CONFIG, $OPTIONS, $obj_regex, $curr_urlobj, $obj_urlparser);

# }}}

//$starttime=microtime(true); # BENCHMARK
$body = $obj_pageparser->parse_all($body);
//$parsetime=microtime(true)-$starttime; # BENCHMARK

# PROXY EXECUTION: PAGE PARSING: PROXY HEADERS/JAVASCRIPT {{{

if(CONTENT_TYPE=='text/html')
{
	$big_headers=
		'<meta name="robots" content="noindex, nofollow" />'.
		($OPTIONS['URL_FORM'] && PAGETYPE_ID===PAGETYPE_FRAMED_PAGE?
		'<base target="_top">':null).
		'<link rel="shortcut icon" href="'.
		$obj_urlparser->surrogafy_url(
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
									$obj_regex->bool_to_js($OPTIONS['ENCRYPT_COOKIES']).';'.

											COOK_PREF.'.ENCRYPT_URLS='.$obj_regex->bool_to_js($OPTIONS['ENCRYPT_URLS']).
				';'.

													COOK_PREF.'.ENCODE_HTML='.$obj_regex->bool_to_js($OPTIONS['ENCODE_HTML']).
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

															COOK_PREF.'.PAGE_FRAMED='.$obj_regex->bool_to_js(PAGE_FRAMED).';'.

															COOK_PREF.'.REMOVE_OBJECTS='.
															$obj_regex->bool_to_js($OPTIONS['REMOVE_OBJECTS']).';'.

			COOK_PREF.'.URL_FORM='.$obj_regex->bool_to_js($OPTIONS['URL_FORM']).';'.

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
	CONTENT_TYPE=='application/javascript' ||
	CONTENT_TYPE=='application/x-javascript' ||
	CONTENT_TYPE=='text/javascript'
) $body.=';'.COOK_PREF.'.purge();';

# }}}

# }}}

## Retrieved, Parsed, All Ready to Output ##

// encoded output
if($OPTIONS['ENCODE_HTML']){
	function parse_letter($letter){
		$strhex=dechex(ord($letter));
		while(strlen($strhex)<2){
			$strhex="0{$strhex}";
		}
		return "\\x{$strhex}";
	}

	$body=utf8_decode($body);
	echo '<script language="javascript">document.write("';
	for($i=0; $i<strlen($body); $i++){
		echo parse_letter(substr($body,$i,1));
	}
	echo '");</script>';

	// plain output
} else {
	echo $body;
}