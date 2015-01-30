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

$obj_http = new http($CONFIG, $OPTIONS, $arr_proxy_variable, $referer);
$pagestuff= $obj_http->getpage($curr_url);
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

$curr_url	 = $pagestuff[1];
$arr_headers = $obj_http->getHeaders();
define('PAGECOOK_PREFIX',$pagestuff[2]);
unset($pagestuff);
define('CONTENT_TYPE',preg_replace('/^([a-z0-9\-\/]+).*$/i','\1',$arr_headers['content-type'][0])); #*

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
$obj_urlpaser = new urlparser($curr_urlobj);

# PROXY EXECUTION: PAGE PARSING: PARSING FUNCTIONS {{{

function parse_html($regexp,$partoparse,$html,$addproxy,$framify){
	global $curr_urlobj;
	global $obj_urlpaser;
	$newhtml=null;
	while(preg_match($regexp,$html,$matcharr,PREG_OFFSET_CAPTURE)){
		$nurl=$obj_urlpaser->surrogafy_url($matcharr[$partoparse][0],$curr_urlobj,$addproxy);
		if($framify) $nurl=$obj_urlpaser->framify_url($nurl,$framify);
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

function parse_all($html){
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
		if($firstjsrun && (
				$key=='application/javascript' ||
				$key=='application/x-javascript'
		)){
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
						){
						$splitarr[$i]=
								preg_replace('/'.END_OF_SCRIPT_TAG.'$/i',
								';'.COOK_PREF.'.purge();//--></script>',
								$splitarr[$i]);
		}

			}

			$firstrun=false;
			if($firstjsrun && (
				$key=='application/javascript' ||
				$key=='application/x-javascript'
				))
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
$body=parse_all($body);
//$parsetime=microtime(true)-$starttime; # BENCHMARK

# PROXY EXECUTION: PAGE PARSING: PROXY HEADERS/JAVASCRIPT {{{

if(CONTENT_TYPE=='text/html'){
$big_headers=
		'<meta name="robots" content="noindex, nofollow" />'.
		($OPTIONS['URL_FORM'] && PAGETYPE_ID===PAGETYPE_FRAMED_PAGE?
		'<base target="_top">':null).
		'<link rel="shortcut icon" href="'.
		$obj_urlpaser->surrogafy_url(
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

													COOK_PREF.'.ENCODE_HTML='.bool_to_js($OPTIONS['ENCODE_HTML']).
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