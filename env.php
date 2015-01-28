<?php
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
register_option(1,'ENCODE_HTML');

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