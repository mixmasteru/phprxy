<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', '1');
#
# Surrogafier v1.9.0b
#
# Author: Brad Cable
# Email: brad@bcable.net
# License: Modified BSD
# License Details:
# http://bcable.net/license.php
#
define('THIS_FILE',"{$_SERVER['DOCUMENT_ROOT']}{$_SERVER['PHP_SELF']}");

include 'conf.php';
include 'view/styles.php';
include 'session.php';
include 'functions.php';
include 'env.php';

include 'classes/aurl.php';

include 'view/main.php';
include 'view/frame.php';
include 'view/css.php';
include 'view/js.php';

# REGEXPS {{{

# This is where all the parsing is defined.  If a site isn't being
# parsed properly, the problem is more than likely in this section.
# The rest of the code is just there to set up this wonderful bunch
# of incomprehensible regular expressions.


# REGEXPS: CONVERSION TO JAVASCRIPT {{{

function bool_to_js($bool){ return ($bool?'true':'false'); }
function fix_regexp($regexp){
	global $js_varsect;

	// backreference cleanup
	$regexp=preg_replace('/\(\?P\<[a-z0-9_]+\>/i','(',$regexp);
	$regexp=preg_replace('/\(\?P\>[a-z0-9_]+\)/i',$js_varsect,$regexp);
	$regexp=preg_replace('/\(\?\<\![^\)]+?\)/i',$js_varsect,$regexp);

	return $regexp;
}
function convert_array_to_javascript(){
	global $regexp_arrays;
	$js='regexp_arrays=new Array('.count($regexp_arrays).");\n";
	reset($regexp_arrays);
	while(list($key,$arr)=each($regexp_arrays)){
		$js.="regexp_arrays[\"{$key}\"]=new Array(".count($arr).");\n";
		for($i=0;$i<count($arr);$i++){
			$js.="regexp_arrays[\"{$key}\"][{$i}]=new Array(";
			if($arr[$i][0]==1)
				$js.=
					'1,'.escape_regexp(fix_regexp($arr[$i][2])).'g,"'.
					escape_regexp(fix_regexp($arr[$i][3]),true).'"';
			elseif($arr[$i][0]==2)
				$js.=
					'2,'.escape_regexp(fix_regexp($arr[$i][2])).
					"g,{$arr[$i][3]}".
					(count($arr[$i])<5?null:','.bool_to_js($arr[$i][4])).
					(count($arr[$i])<6?null:",{$arr[$i][5]}");
			$js.=");\n";
		}
	}
	return stripslashes($js);
}

# }}}

# REGEXPS: VARIABLES {{{

global $regexp_arrays, $js_varsect;

/* Variable Naming Tags
js:     Javascript
html:   HTML
hook:   are used to determine what is going to be hooked by the script
g:      global helper variable
h:      local/short term helper variable
l:      'looker' (uses lookaheads or lookbehinds for anchoring)
n:      'not'
*/

# REGEXPS: VARIABLES: Helper Variables {{{

/*
$g_justspace:      just space characters (no newlines)   0+
$g_plusjustspace:  just space characters (no newlines)   1+
$g_anyspace:       any space characters at all           0+
$g_plusspace:      any space characters at all           1+
$g_anynewline:     any newline characters                0+
$g_plusnewline:    any newline characters                1+
$g_n_anynewline:   not any newline characters            0+
$g_n_plusnewline:  not any newline characters            1+
$g_bool_operand:   any boolean operand                   1
$g_operand:        any operand                           1
$g_n_operand:      anything but an operand               1
$g_quoteseg:       any quote segment                     2+
$g_regseg:         any regular expression segment        2+
*/

$g_justspace="[\t ]*";
$g_plusjustspace="[\t ]+";
$g_anyspace="[\t\r\n ]*";
$g_plusspace="[\t\r\n ]+";
$g_anynewline="[\r\n]*";
$g_plusnewline="[\r\n]+";
$g_n_anynewline="[^\r\n]*";
$g_n_plusnewline="[^\r\n]+";
$g_bool_operand='(?:\|\||\&\&|\!=|==|\>|\<|\<=|\>=)';
$g_operand=
	"(?:{$g_bool_operand}|[\+\-\/\*\|\&\%\?\:]|\>|\>\>|\>\>\>|\<|\<\<|\<\<\<)";
$g_n_operand='[^\+\-\/\*\|\&\%\?\:\<\>]';
$g_quoteseg='(?:"(?:[^"]|[\\\\]")*?"|\'(?:[^\']|[\\\\]\')*?\')';
$g_regseg='\/(?:[^\/]|[\\\\]\/)*?\/[a-z]*';

# }}}

# REGEXPS: VARIABLES: Parsing Config {{{

/*
$html_frametargets:  html list of frame targets to look out for
$hook_html_attrs:    hook html attributes
$hook_js_attrs:      js hook attributes for getting and setting
$hook_js_getattrs:   js hook attributes for getting only
$hook_js_methods:    js hook methods
$js_string_methods:  js methods for the String() object
$js_string_attrs:    js attributes for the String() object
*/

# HTML
$html_frametargets='_(?:top|parent|self)';
$hook_html_attrs='(data|href|src|background|pluginspage|codebase|action)';

# Javascript
/*$hook_js_attrs=
	'(?:href|src|location|action|backgroundImage|pluginspage|codebase|'.
	'location\.href|innerHTML|cookie|search|hostname)';
$hook_js_getattrs=
	"(?:{$hook_js_attrs}|userAgent|platform|appCodeName|appName|appVersion|".
	'language|oscpu|product|productSub|plugins)';*/
$hook_js_methods='(location\.(?:replace|assign))';

$js_string_methods=
	'(?:anchor|big|blink|bold|charAt|charCodeAt|concat|fixed|fontcolor|'.
	'fontsize|fromCharCode|indexOf|italics|lastIndexOf|link|match|replace|'.
	'search|slice|small|split|strike|sub|substr|substring|sup|toLowerCase|'.
	'toUpperCase|toSource|valueOf)';
$js_string_attrs='(?:constructor|length|prototype)';

# }}}

# REGEXPS: VARIABLES: Javascript Expressions Matching {{{

/*
$js_varsect:     flat variable section
$js_jsvarsect:   flat variable section for use in js's parsing engine
$n_js_varsect:   not a javascript variable section
$h_js_exprsect:  helper for js_exprsect
$js_exprsect:    single expression section
$h_js_expr:      helper for js_expr
$js_expr:        any javascript expression
$js_expr2, ...:  $js_expr requires use of a named submatch, so there needs
                 to be multiple versions of $js_expr for use multiple times in
                 one regular expression
*/

$js_varsect=
	"(?:new{$g_plusspace})?[a-zA-Z_\$]".
	"(?:[a-zA-Z0-9\$\._]*[a-zA-Z0-9_])?";
$js_jsvarsect=
	"(?:new{$g_plusspace})?[a-zA-Z_\$]".
	"(?:[a-zA-Z0-9\$\._]*[a-zA-Z0-9_\[\]])?";
$n_js_varsect='[^a-zA-Z\._\[\]]';

$h_js_exprsect=
	"(?!function[^\(]|return|\/\*|\/\/)".
	"(?:{$g_quoteseg}|{$g_regseg}|{$js_varsect}|[0-9\.]+)";
$js_exprsect="(?:{$h_js_exprsect}|\({$h_js_exprsect}\))";
$h_js_expr=
	"|\[{$g_anyspace}(?:(?P>js_expr)".
		"(?:{$g_anyspace},{$g_anyspace}(?P>js_expr))*{$g_anyspace})?\]".
	"|\({$g_anyspace}(?:(?P>js_expr)".
		"(?:{$g_anyspace},{$g_anyspace}(?P>js_expr))*{$g_anyspace})?\)".
	"|\{{$g_anyspace}(?:(?P>js_expr)".
		"(?:{$g_anyspace},{$g_anyspace}(?P>js_expr))*{$g_anyspace})?\}";
$js_expr_get=
	'(?P<js_expr>(?!'.COOK_PREF.         # this makes sure COOK_PREF.purge()
		'\.(?:purge|getAttr)\()'.        # doesn't get parsed, and that
		'(?<!'.COOK_PREF.'\.getAttr\()'. # getAttr() doesn't wrap around another
		                                 # getAttr(); below, this is replaced
		                                 # for setAttr() since it needs to wrap
		                                 # around getAttr()
	"(?:{$js_exprsect}{$h_js_expr})".
	"(?:{$g_anyspace}(?:".
		"\.{$g_anyspace}(?P>js_expr)".               # recursive attribute
		"|\.{$g_anyspace}{$js_exprsect}".            # attribute
		"|{$g_operand}{$g_anyspace}(?P>js_expr)".    # any operand
		"|\?{$g_anyspace}(?P>js_expr){$g_anyspace}". # ternary operator
			"\:{$g_anyspace}(?P>js_expr)".           #
		$h_js_expr.                                  # brackets, parens, etc
	"){$g_anyspace})*)";

// for setAttr()
$js_expr_set=str_replace('getAttr','setAttr',$js_expr_get);

// for global use
$g_js_expr=str_replace('getAttr','',$js_expr_get);
$g_js_expr=str_replace('js_expr','g_js_expr',$g_js_expr);

// these should only be produced as required in the code
// setAttr()
$js_expr2_set=str_replace('js_expr','js_expr2',$js_expr_set);
$js_expr3_set=str_replace('js_expr','js_expr3',$js_expr_set);
$js_expr4_set=str_replace('js_expr','js_expr4',$js_expr_set);
// getAttr()
$js_expr2_get=str_replace('js_expr','js_expr2',$js_expr_get);
// global use
$g_js_expr2=str_replace('g_js_expr','g_js_expr2',$g_js_expr);

# }}}

# REGEXPS: VARIABLES: Miscellaneous {{{

/*
$l_js_end:          looks for if end of javascript statement
$n_l_js_end:        looks for if not end of javascript statement (#)
$js_begin:          matches beginning of javascript statement
$js_beginright:     matches beginning of javascript statement on the RHS
$js_xmlhttpreq:     XMLHttpRequest matching (plus ActiveX version)
$h_html_noquot:     matches an HTML attribute value that is not using quotes
$html_reg:          matches an HTML attribute value
$js_newobj:         matches a 'new' clause inside of Javascript
$html_formnotpost:  matches a form, given it's not of method POST
*/

$l_js_end="(?={$g_justspace}(?:[;\)\}\r\n=\!\|\&,]|{$g_n_operand}[\n\r]))";
#$n_l_js_end="(?!{$g_justspace}(?:[;\}]|{$g_n_operand}[\n\r]))";
$js_begin=
	"((?:[;\{\}\n\r\(\)\&\!]|[\!=]=)(?!{$g_anyspace}(?:#|\/\*|\/\/|'|\"))".
	"{$g_anyspace})";
$js_end="((?:{$g_operand}{$g_js_expr})*?(?:[;\)\}\r\n=\'\"\!\|\&,]|[\!=]=))";
$js_begin_strict_end=str_replace('g_js_expr','g_js_expr2',$js_end);
$js_begin_strict=
	"({$js_begin_strict_end}".
	"(?!{$g_anyspace}(?:#|\/\*|\/\/|'|\")){$g_anyspace})";
$n_js_string="(?!(?:[^\r\n']*'|[^\r\n\"]*\"))";
$n_js_set="(?!{$g_anyspace}(?:[^=]=[^=]|\+=|\-=|\*=|\/=|\+\+|\-\-))"; # DEBUG
$n_js_set_left="(?<!\-\-|\+\+)";
$wrap_js_end=
	"({$n_js_set}{$n_js_string}{$js_end}|".
		"(?={$g_anyspace}(?:{$g_bool_operand}|=)))";
# (?<!:[\/])[\/](?![\/]) - this matches a slash ('/') without being a part of
#                          "://"
$js_beginright=
	"((?:".
		"(?<!:[\/])[\/](?![\/])|[;\{\(=\+\-\*]|".
		"[\}\)]{$g_anyspace};{$g_anyspace})".
	"{$g_anyspace})";
#$js_beginright=
#	"((?:[;\{\(=\+\-\*]|[\}\)]{$g_anyspace};{$g_anyspace}|".
#	"(?<!:[\/])[\/](?![\/])){$g_justspace})";
#$js_beginright="((?:[;\{\}\(\)=\+\-\*]|(?<!:[\/])[\/](?![\/])){$g_justspace})";

$js_xmlhttpreq=
	"(?<!XMLHttpRequest_wrap\(new )".
	"(?:XMLHttpRequest{$g_anyspace}(?:\({$g_anyspace}\)|)|".
	"ActiveXObject{$g_anyspace}\({$g_anyspace}[^\)]+\.XMLHTTP['\"]".
	"{$g_anyspace}\))";

$h_html_noquot='(?:[^"\'\\\\][^> ]*)';
$html_reg="({$g_quoteseg}|{$h_html_noquot})";
$js_newobj="(?:new{$g_plusspace})";
$html_formnotpost="(?:(?!method{$g_anyspace}={$g_anyspace}(?:'|\")?post)[^>])";

# }}}

# }}}

# REGEXPS: JAVASCRIPT PARSING {{{

$js_regexp_arrays=array(

	# object.attribute parsing (set)

	# prepare for set for +=
	array(1,2,
		"/{$js_begin}{$js_expr_get}\.({$js_varsect}){$g_anyspace}\+=/im",
		"\\1\\2.\\3=".COOK_PREF.".getAttr(\\2,/\\3/)+"),
	# set for =
	array(1,2,
		"/{$js_begin_strict}{$js_expr_set}\.(({$js_varsect}){$g_anyspace}=".
			"(?:{$g_anyspace}{$js_expr2_set}{$g_anyspace}=)*{$g_anyspace})".
			"{$js_expr3_set}{$wrap_js_end}/im",
		#"\\1\\4.\\6=".COOK_PREF.".setAttr(\\4,/\\6/,\\8)\\9"), #TODO: new way?
		"\\1".COOK_PREF.".setAttr(\\4,/\\6/,\\8)\\9"),


	# object['attribute'] parsing (set)

	# prepare for set for +=
	array(1,2,
		"/{$js_begin}{$js_expr_get}\[{$js_expr2_get}\]{$g_anyspace}\+=/im",
		"\\1\\2[\\3]=".COOK_PREF.".getAttr(\\2,\\3)+"),
	# set for =
	array(1,2,
		"/{$js_begin_strict}{$js_expr_set}(\[{$js_expr2_set}\]{$g_anyspace}=".
			"(?:{$g_anyspace}{$js_expr3_set}{$g_anyspace}=)*{$g_anyspace})".
			"{$js_expr4_set}{$wrap_js_end}/im",
		//"\\1\\4[\\6]=".COOK_PREF.".setAttr(\\4,\\6,\\8)\\9"), #TODO: new way?
		"\\1".COOK_PREF.".setAttr(\\4,\\6,\\8)\\9"),


	# object.setAttribute parsing
	array(1,2,
		"/{$js_begin_strict}{$js_expr_set}\.setAttribute{$g_anyspace}\(".
			"{$g_anyspace}{$js_expr2_set}{$g_anyspace},{$g_anyspace}".
			"{$js_expr3_set}{$g_anyspace}\)/im",
		#"\\1\\4[\\5]=".COOK_PREF.".setAttr(\\4,\\5,\\6)"), #TODO: new way?
		"\\1".COOK_PREF.".setAttr(\\4,\\5,\\6)"),


	# get parsing

	# get: object[attribute]
	array(1,2,
		"/{$js_beginright}{$n_js_set_left}{$js_expr_get}\[{$js_expr2_get}\]".
		"{$wrap_js_end}/im",
		"\\1".COOK_PREF.".getAttr(\\2,\\3)\\4"),

	# get: object.attribute
	array(1,2,
		"/{$js_beginright}{$n_js_set_left}{$js_expr_get}\.({$js_varsect})".
		"{$wrap_js_end}/im",
		"\\1".COOK_PREF.".getAttr(\\2,/\\3/)\\4"),


	# other stuff

	# method parsing
	array(1,2,
		"/([^a-z0-9]{$hook_js_methods}{$g_anyspace}\()([^)]*)\)/im",
		"\\1".COOK_PREF.".surrogafy_url(\\3))"),

	# eval parsing
	array(1,2,
		"/([^a-z0-9])eval{$g_anyspace}\(".
			"(?!".COOK_PREF.")({$g_anyspace}{$g_js_expr})\)/im",
		"\\1eval(".COOK_PREF.".parse_all(\\2,\"application/x-javascript\"))"),

	# action attribute parsing
	array(1,2,
		"/{$js_begin}\.action{$g_anyspace}=/im",
		"\\1.".COOK_PREF.".value="),

	# XMLHttpRequest parsing
	array(1,2,
		"/({$js_newobj}{$js_xmlhttpreq})/im",
		COOK_PREF.".XMLHttpRequest_wrap(\\1)"),

	# form.submit() call parsing
	($OPTIONS['ENCRYPT_URLS']?array(1,2,
		"/{$js_begin}((?:[^\) \{\}]*(?:\)\.{0,1}))+)(\.submit{$g_anyspace}\(\)".
			"){$l_js_end}/im",
		"\\1void((\\2.method==\"post\"?null:\\2\\3));")
	:null),

);

# }}}

# REGEXPS: HTML/CSS PARSING {{{

$regexp_arrays=array(
	'text/html' => array(
		# target attr
		(PAGETYPE_ID===PAGETYPE_FRAMED_PAGE?array(1,1,
			"/(<[a-z][^>]*{$g_anyspace}) target{$g_anyspace}={$g_anyspace}".
				"(?:{$html_frametargets}|('){$html_frametargets}'|(\")".
				"{$html_frametargets}\")".
				"/im",
			'\1')
		:null),
		(PAGETYPE_ID===PAGETYPE_FRAMED_CHILD?array(1,1,
			"/(<[a-z][^>]*{$g_anyspace} target{$g_anyspace}={$g_anyspace})".
				"(?:_top|(')_top'|(\")_top\")/im",
			'\1\2\3'.COOK_PREF.'_top\2\3')
		:null),

		# deal with <form>s
		array(1,1,
			"/(<form{$html_formnotpost}*?)".
				"(?:{$g_plusspace}action{$g_anyspace}={$g_anyspace}{$html_reg}".
				")({$html_formnotpost}*)>/im",
			'\1\3><input type="hidden" name="" class="'.COOK_PREF.'" value=\2'.
			' />'),
		array(2,1,
			"/<input type=\"hidden\" name=\"\" class=\"".COOK_PREF."\"".
				" value{$g_anyspace}={$g_anyspace}{$html_reg} \/>/im",
			1,false),
		array(1,1,
			'/(<form[^>]*?)>/im',
			'\1 target="_self"'.
				($OPTIONS['ENCRYPT_URLS']?
				 ' onsubmit="return '.COOK_PREF.'.form_encrypt(this);">':'>')),
		array(1,1,
			"/(<form{$html_formnotpost}+)>(?!<!--".COOK_PREF.'-->)/im',
			'\1 target="_parent"><!--'.COOK_PREF.
				'--><input type="hidden" name="" value="_">'),

		# deal with the form button for encrypted URLs
		($OPTIONS['ENCRYPT_URLS']?array(1,1,
			"/(<input[^>]*? type{$g_anyspace}={$g_anyspace}".
				"(?:\"submit\"|'submit'|submit)[^>]*?[^\/])((?:[ ]?[\/])?>)/im",
			'\1 onclick="'.COOK_PREF.'_form_button=this.name;"\2')
		:null),

		# parse all the other tags
		array(2,1,
			"/<[a-z][^>]*{$g_plusspace}{$hook_html_attrs}{$g_anyspace}=".
				"{$g_anyspace}{$html_reg}/im",
			2),
		array(2,1,
			"/<param[^>]*{$g_plusspace}name{$g_anyspace}={$g_anyspace}[\"']?".
				"movie[^>]*{$g_plusspace}value{$g_anyspace}={$g_anyspace}".
				"{$html_reg}/im",
			1),
		array(2,2,
			"/<script[^>]*?{$g_plusspace}src{$g_anyspace}={$g_anyspace}([\"'])".
				"{$g_anyspace}(.*?[^\\\\])\\1[^>]*>{$g_anyspace}<\/script>/im",
			2),
		($OPTIONS['URL_FORM'] && PAGE_FRAMED?array(2,1,
			"/<a(?:rea)?{$g_plusspace}[^>]*href{$g_anyspace}={$g_anyspace}".
				"{$html_reg}/im",
			1,false,NEW_PAGETYPE_FRAME_TOP)
		:null),
		($OPTIONS['URL_FORM'] && PAGE_FRAMED?array(2,1,
			"/<[i]?frame{$g_plusspace}[^>]*src{$g_anyspace}={$g_anyspace}".
			"{$html_reg}/im",
			1,false,PAGETYPE_FRAMED_CHILD)
		:null)
	),

	'text/css' => array(
		array(2,1,
			"/[^a-z]url\({$g_anyspace}(&(?:quot|#(?:3[49]));|\"|')(.*?[^\\\\])".
				"(\\1){$g_anyspace}\)/im",
			2),
		array(2,1,
			"/[^a-z]url\({$g_anyspace}((?!&(?:quot|#(?:3[49]));)[^\"'\\\\].*?".
				"[^\\\\]){$g_anyspace}\)/im",
			1),
		array(2,1,
			"/@import{$g_plusspace}(&(?:quot|#(?:3[49]));|\"|')(.*?[^\\\\])".
				"(\\1);/im",
			2)
	),

	'application/javascript' => $js_regexp_arrays,
	'application/x-javascript' => $js_regexp_arrays,
	'text/javascript' => $js_regexp_arrays
);

# }}}

# REGEXPS: STATIC JAVASCRIPT REGEXPS PAGE {{{

if(QUERY_STRING=='js_regexps' || QUERY_STRING=='js_regexps_framed'){
	static_cache();
?>//<script type="text/javascript">
<?php echo(
	convert_array_to_javascript().
	(
		$OPTIONS['REMOVE_OBJECTS']?
		'regexp_arrays["text/html"].push(Array(1,/<[\\\\/]?'.
			'(embed|param|object)[^>]*>/ig,""));':
		null
	)
); ?>
//</script><?php exit(); }

# }}}

# REGEXPS: SERVER-SIDE ONLY PARSING {{{

array_push($regexp_arrays['text/html'],
	array(2,1,
		"/<meta[^>]*{$g_plusspace}http-equiv{$g_anyspace}={$g_anyspace}".
		"([\"']|)refresh\\1[^>]* content{$g_anyspace}={$g_anyspace}([\"']|)".
		"[ 0-9\.;\t\\r\n]*url=(.*?)\\2[^>]*>/i",
		3,true,NEW_PAGETYPE_FRAMED_PAGE),
	array(1,1,
		"/(<meta[^>]*{$g_plusspace}http-equiv{$g_anyspace}={$g_anyspace}".
		"([\"']|)set-cookie\\2[^>]* content{$g_anyspace}={$g_anyspace})([\"'])".
		"(.*?[^\\\\]){$g_anyspace}\\3/i",
		'\1\3'.PAGECOOK_PREFIX.'\4\3')
);

# }}}

# REGEXPS: CLEANUP {{{

# needed later, but $g_anyspace and $html_reg are unset below
define('BASE_REGEXP',
	"<base[^>]* href{$g_anyspace}={$g_anyspace}{$html_reg}[^>]*>");
define('END_OF_SCRIPT_TAG',
	"(?:{$g_anyspace}(?:\/\/)?{$g_anyspace}-->{$g_anyspace})?<\/script>");
define('REGEXP_SCRIPT_ONEVENT',
	"( on[a-z]{3,20}=(?:\"[^\"]+\"|'[^']+'|[^\"' >][^ >]+[^\"' >])|".
	" href=(?:\"{$g_anyspace}javascript:[^\"]+\"|".
	"'{$g_anyspace}javascript:[^']+'|".
	"{$g_anyspace}javascript:[^\"' >][^ >]+[^\"' >]))");

unset(
	$g_justspace, $g_plusjustspace, $g_anyspace, $g_plusspace, $g_operand,
	$g_n_operand, $g_quoteseg, $g_regseg,

	$hook_html_attrs, $html_frametargets, $hook_js_attrs, $hook_js_getattrs,
	$hook_js_methods, $js_string_methods, $js_string_attrs,

	$js_varsect, $js_jsvarsect, $n_js_varsect, $h_js_exprsect, $js_exprsect,
	$js_expr, $js_expr2, $js_expr3, $js_expr4,

	$l_js_end, $n_l_js_end, $js_begin, $js_end, $js_begin_strict_end,
	$js_begin_strict, $n_js_string, $n_js_set, $n_js_set_left, $wrap_js_end,
	$js_beginright, $js_xmlhttpreq,

	$h_html_noquot, $html_reg, $js_newobj, $html_formnotpost,

	$js_regexp_arrays
);

# }}}

# }}}

# PROXY FUNCTIONS {{{



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

# PROXY FUNCTIONS: ERRORS & EXITING {{{

function finish_noexit(){
	global $dns_cache_array;
	# save DNS Cache before exiting
	$_SESSION['DNS_CACHE_ARRAY']=$dns_cache_array;
}

function finish(){
	finish_noexit();
	exit();
}

function havok($errorno,$arg1=null,$arg2=null,$arg3=null){
	global $curr_url;
	$url=$curr_url;
	switch($errorno){
		case 1:
			$et='Bad IP Address';
			$ed=
				"The IP address given ({$arg2}) is an impossible IP address, ".
				"or the domain given ({$arg1}) was resolved to an impossible ".
				'IP address.';
			break;
		case 2:
			$et='Address is Blocked';
			$ed=
				'The administrator of this proxy service has decided to '.
				"block this address, domain, or subnet.\n<br /><br />\n".
				"Domain: {$arg1}\n<br />\nAddress: {$arg2}";
			break;
		case 3:
			$et='Could Not Resolve Domain';
			$ed=
				"The domain of the URL given ({$arg1}) could not be resolved ".
				'due to DNS issues or an errorneous domain name.';
			break;
		case 4:
			$et='Bad Filters';
			$ed=
				'The administrator of this proxy has incorrectly configured '.
				'his domain filters, or a domain given could not be resolved.';
			break;
		case 5:
			$et='Domain is Blocked';
			$ed=
				'The administrator of this proxy has decided to block this '.
				'domain.';
			break;
		case 6:
			$et='Could Not Connect to Server';
			$ed=
				'An error has occurred while attempting to connect to '.
				"\"{$arg1}\" on port \"{$arg2}\".";
			break;
		case 7:
			$et='Invalid URL';
			$ed='The URL below was detected to be an invalid URL.';
			$url=$arg1;
			break;
		case 8:
			$et='Trying to Access Secure Page Through Insecure Connection';
			$ed=
				'The site you are trying to access is secured by SSL, however '.
				'you are accessing this proxy through an insecure connection. '.
				'Please realize that any information you pass to this site is '.
				'going to be transmitted on an insecure connection, with the '.
				'potential of being intercepted.'.
				'<br /><br />'.
				"Domain to unlock: {$arg1}".
				'<br /><br />'.
				'If you wish to allow secure connections to this domain for '.
				'this session, press continue below.  Otherwise, hit back.'.
				'<br /><br />'.
				'<input type="button" value="Back" style="float: left"'.
				' onclick="history.go(-1);" />'.
				'<input type="button" value="Continue" style="float: right"'.
				' onclick="'.
					'var ifrm=document.createElement(\'iframe\');'.
					'ifrm.onload=function(){ location.reload(true); };'.
					'ifrm.src=\''.THIS_SCRIPT.'?'.COOK_PREF.'_ssl_domain='.
					"{$arg1}';".
					'ifrm.style.height=\'0px\';'.
					'ifrm.style.width=\'0px\';'.
					'ifrm.style.border=\'0px\';'.
					'var body=document.getElementsByTagName(\'body\')[0];'.
					'body.appendChild(ifrm);'.
				'" />'.
				'<br />';
			break;
	}
	$ed.="\n<br /><br />\nURL:&nbsp;{$url}";
?>
<html>
<head>
	<title>Proxy Error</title>
</head>
<body>
	<div style="font-family: bitstream vera sans, trebuchet ms">
	<div style="border: 3px solid #FFFFFF; padding: 2px">
		<div style="
			float: left; border: 1px solid #602020; padding: 1px;
			background-color: #FFFFFF">
			<div style="
				float: left; background-color: #801010; color: #FFFFFF;
				font-weight: bold; font-size: 54px; padding: 2px;
				padding-left: 12px; padding-right: 12px"
			>!</div>
		</div>
		<div style="float: left; width: 500px; padding-left: 20px">
			<div style="
				border-bottom: 1px solid #000000; font-size: 12pt;
				text-align: center; font-weight: bold; padding: 2px"
			>Error: <?php echo($et); ?></div>
			<div style="padding: 6px"><?php echo($ed); ?></div>
		</div>
	</div></div>
</body>
</html>
<?php finish(); }

# }}}

# PROXY FUNCTIONS: TCP/IP {{{

function ipbitter($ipaddr){
	$ipsplit=explode('.',$ipaddr);
	for($i=0;$i<4;$i++){
		$ipsplit[$i]=decbin($ipsplit[$i]);
		$ipsplit[$i]=str_repeat('0',8-strlen($ipsplit[$i])).$ipsplit[$i];
	}
	return implode(null,$ipsplit);
}

function ipcompare($iprange,$ip){
	$iprarr=explode('/',$iprange);
	$ipaddr=$iprarr[0];
	$mask=$iprarr[1];
	$maskbits=str_repeat('1',$mask).str_repeat('0',$mask);
	$ipbits=ipbitter($ipaddr);
	$ipbits2=ipbitter($ip);
	return (($ipbits & $maskbits)==($ipbits2 & $maskbits));
}

function ip_check($ip,$mask=false){
	$ipseg='(?:[01]?[0-9]{1,2}|2(?:5[0-5]|[0-4][0-9]))';
	return preg_match("/^(?:$ipseg\.){0,3}$ipseg".($mask?'\/[0-9]{1,2}':null).
		'$/i',$ip); #*
}

function gethostbyname_cacheit($address){
	global $dns_cache_array;
	$ipaddr=gethostbyname($address);
	$dns_cache_array[$address]=array('time'=>time(), 'ipaddr'=>$ipaddr);
	return $ipaddr;
}

function gethostbyname_cached($address){
	global $dns_cache_array;
	if(isset($dns_cache_array[$address]))
		return $dns_cache_array[$address]['ipaddr'];
	return gethostbyname_cacheit($address);
}

function get_check($address){
	global $CONFIG;
	if(strrchr($address,'/')) $address=substr(strrchr($address,'/'),1);
	$ipc=ip_check($address);
	$addressip=(ip_check($address)?$address:gethostbyname_cached($address));
	if(!ip_check($addressip)) havok(1,$address,$addressip);
	foreach($CONFIG['BLOCKED_ADDRESSES'] as $badd){
		if(!$ipc)
			if(
				strlen($badd)<=strlen($address) &&
				substr($address,strlen($address)-strlen($badd),
					strlen($badd))==$badd
			) havok(5);
		if($badd==$addressip) havok(2,$address,$addressip);
		elseif(ip_check($badd,true)){
			if(ipcompare($badd,$addressip)) havok(2,$address,$addressip);
		}
		else{
			$baddip=gethostbyname_cached($badd);
			if(empty($baddip)) havok(4);
			if($baddip==$addressip) havok(2,$address,$addressip);
		}
	}
	return $addressip;
}

# }}}

# PROXY FUNCTIONS: HTTP {{{

function httpclean($str){
	return str_replace(' ','+',
		preg_replace('/([^":\-_\.0-9a-z ])/ie',
			'\'%\'.(strlen(dechex(ord(\'\1\')))==1?\'0\':null).'.
			'strtoupper(dechex(ord(\'\1\')))',
		$str));
}

function getpage($url){
	global $CONFIG,$OPTIONS,$headers,$out,$proxy_variables,$referer;

	# Generate HTTP packet content {{{

	$content=null;
	$is_formdata=substr($_SERVER['CONTENT_TYPE'],0,19)=='multipart/form-data';

	# Generate for multipart & handle file uploads {{{

	if($is_formdata){
		$strnum=null;
		for($i=0; $i<29; $i++) $strnum.=rand(0,9);
		$boundary="---------------------------{$strnum}";

		# parse POST variables
		while(list($key,$val)=each($_POST)){
			if(!is_array($val)){
				$content.=
					"--{$boundary}\r\n".
					"Content-Disposition: form-data; name=\"{$key}\"\r\n".
					"\r\n{$val}\r\n";
			}
			else{
				while(list($key2,$val2)=each($val)){
					$content.=
						"--{$boundary}\r\n".
						"Content-Disposition: form-data; name=\"{$key}[]\"\r\n".
						"\r\n{$val2}\r\n";
				}
			}
		}

		# parse uploaded files
		while(list($key,$val)=each($_FILES)){
			if(!is_array($val['name'])){
				$fcont=file_get_contents($val['tmp_name']);
				@unlink($val['tmp_name']);
				$content.=
					"--{$boundary}\r\n".
					"Content-Disposition: form-data; name=\"{$key}\"; ".
						"filename=\"{$val['name']}\"\r\n".
					"Content-Type: {$val['type']}\r\n".
					"\r\n{$fcont}\r\n";
			}
			else{
				for($i=0; $i<count($val['name']); $i++){
					$fcont=file_get_contents($val['tmp_name'][$i]);
					@unlink($val['tmp_name'][$i]);
					$content.=
						"--{$boundary}\r\n".
						"Content-Disposition: form-data; name=\"{$key}[]\"; ".
							"filename=\"{$val['name'][$i]}\"\r\n".
						"Content-Type: {$val['type'][$i]}\r\n".
						"\r\n{$fcont}\r\n";
				}
			}
		}
		$content.="--{$boundary}--\r\n";
	}

	# }}}

	# Generate for standard POST {{{

	else{
		$postkeys=array_keys($_POST);
		foreach($postkeys as $postkey){
			if(!in_array($postkey,$proxy_variables)){
				if(!is_array($_POST[$postkey]))
					$content.=
						($content!=null?'&':null).
						httpclean($postkey).'='.httpclean($_POST[$postkey]);
				else{
					foreach($_POST[$postkey] as $postval)
						$content.=
							($content!=null?'&':null).
							httpclean($postkey).'%5B%5D='.httpclean($postval);
				}
			}
		}
	}

	# }}}

	# }}}

	# URL setup {{{

	$urlobj=new aurl($url);

	# don't access SSL sites unless the proxy is being accessed through SSL too
	if(
		$urlobj->get_proto()=='https' && $CONFIG['PROTO']!='https' &&
		(
			!is_array($_SESSION['ssl_domains']) ||
			(
				is_array($_SESSION['ssl_domains']) &&
				!in_array($urlobj->get_servername(),$_SESSION['ssl_domains'])
			)
		)
	){
		# ignore certain file types from worrying about this
		$skip = false;
		foreach($CONFIG['SSL_WARNING_IGNORE_FILETYPES'] as $filetype){
			if(substr($urlobj->get_file(), -strlen($filetype)) == $filetype)
				$skip = true;
		}
		if(!$skip) havok(8,$urlobj->get_servername());
	}

	# get request URL
	$query=$urlobj->get_query();
	$requrl=
		$urlobj->get_path().
		$urlobj->get_file().
		(!empty($query)?"?{$query}":null);

	# }}}

	# HTTP Authorization and Cache stuff {{{
	$http_auth=null;
	if(extension_loaded('apache')){
		$fail=false;
		$cheaders=getallheaders();
		$http_auth=$reqarray['Authorization'];
	}
	else $fail=true;

	$authorization=
		($fail?$_SERVER['HTTP_AUTHORIZATION']:$cheaders['Authorization']);
	$cache_control=
		($fail?$_SERVER['HTTP_CACHE_CONTROL']:$cheaders['Cache-Control']);
	$if_modified=
		($fail?$_SERVER['HTTP_IF_MODIFIED_SINCE']:
		 $cheaders['If-Modified-Since']);
	$if_none_match=
		($fail?$_SERVER['HTTP_IF_NONE_MATCH']:$cheaders['If-None-Match']);

	if($fail){
		if(!empty($authorization)) $http_auth=$authorization;
		elseif(
			!empty($_SERVER['PHP_AUTH_USER']) &&
			!empty($_SERVER['PHP_AUTH_PW'])
		) $http_auth=
			'Basic '.
			base64_encode(
				"{$_SERVER['PHP_AUTH_USER']}:{$_SERVER['PHP_AUTH_PW']}");
		elseif(!empty($_SERVER['PHP_AUTH_DIGEST']))
			$http_auth="Digest {$_SERVER['PHP_AUTH_DIGEST']}";
	}
	# }}}

	# HTTP packet construction {{{

	# figure out what we are connecting to
	if($OPTIONS['TUNNEL_IP']!=null && $OPTIONS['TUNNEL_PORT']!=null){
		$servername=$OPTIONS['TUNNEL_IP'];
		$ipaddress=get_check($servername);
		$portval=$OPTIONS['TUNNEL_PORT'];
		$requrl=$urlobj->get_url(false);
	}
	else{
		$servername=$urlobj->get_servername();
		$ipaddress=
			(
				$urlobj->get_proto()=='ssl' || $urlobj->get_proto()=='https'?
				'ssl://':
				null
			).
			get_check($servername);
		$portval=$urlobj->get_portval();
	}

	# begin packet construction
	$out=
		($content==null?'GET':'POST').' '.
			str_replace(' ','%20',$requrl)." HTTP/1.1\r\n".
		"Host: ".$urlobj->get_servername().
			(
				($portval!=80 && (
					$urlobj->get_proto()=='https'?$portval!=443:true
				))?
				":$portval":
				null
			)."\r\n";

	# user agent and auth headers
	global $useragent;
	$useragent=null;
	if($OPTIONS['USER_AGENT']!='-1'){
		$useragent=$OPTIONS['USER_AGENT'];
		if(empty($useragent)) $useragent=$_SERVER['HTTP_USER_AGENT'];
		if(!empty($useragent)) $out.="User-Agent: $useragent\r\n";
	}
	if(!empty($http_auth)) $out.="Authorization: $http_auth\r\n";

	# referer headers
	if(!$OPTIONS['REMOVE_REFERER'] && !empty($referer))
		$out.='Referer: '.str_replace(' ','+',$referer)."\r\n";

	# POST headers
	if($content!=null)
		$out.=
			'Content-Length: '.strlen($content)."\r\n".
			'Content-Type: '.
				(
					$is_formdata?
					"multipart/form-data; boundary={$boundary}":
					'application/x-www-form-urlencoded'
				)."\r\n";

	# cookie headers
	$cook_prefdomain=
		preg_replace('/^www\./i',null,$urlobj->get_servername()); #*
	$cook_prefix=str_replace('.','_',$cook_prefdomain).COOKIE_SEPARATOR;
	if(!$OPTIONS['REMOVE_COOKIES'] && count($_COOKIE)>0){
		$addtoout=null;
		reset($_COOKIE);
		while(list($key,$val)=each($_COOKIE)){
			if(
				$key{0}!='~' && strtolower(substr($key,0,3))!='%7e' &&
				str_replace(COOKIE_SEPARATOR,null,$key)==$key
			) continue;
			if($OPTIONS['ENCRYPT_COOKIES']){
				$key=proxdec($key);
				$val=proxdec($val);
			}
			$cook_domain=
				substr($key,0,strpos($key,COOKIE_SEPARATOR)).COOKIE_SEPARATOR;
			if(
				substr($cook_prefix,strlen($cook_prefix)-strlen($cook_domain),
					strlen($cook_domain))!=$cook_domain
			) continue;
			$key=
				substr($key,strlen($cook_domain),
					strlen($key)-strlen($cook_domain));
			if(!in_array($key,$proxy_variables)) $addtoout.=" $key=$val;";
		}
		if(!empty($addtoout)){
			$addtoout.="\r\n";
			$out.="Cookie:{$addtoout}";
		}
	}

	# final packet headers and content
	$out.=
		"Accept: */*;q=0.1\r\n".
		($CONFIG['GZIP_PROXY_SERVER']?"Accept-Encoding: gzip\r\n":null).
		//"Accept-Charset: ISO-8859-1,utf-8;q=0.1,*;q=0.1\r\n".
		/*/
		"Keep-Alive: 300\r\n".
		"Connection: keep-alive\r\n".                          /*/
		"Connection: close\r\n".                               //*/
		($cache_control!=null?"Cache-Control: $cache_control\r\n":null).
		($if_modified!=null?"If-Modified-Since: $if_modified\r\n":null).
		($if_none_match!=null?"If-None-Match: $if_none_match\r\n":null).
		"\r\n{$content}";

	# }}}

	# Ignore SSL errors {{{

	# This part ignores any "SSL: fatal protocol error" errors, and makes sure
	# other errors are still triggered correctly
	function errorHandle($errno,$errmsg){
		if(
			$errno<=E_PARSE && (
				$errno!=E_WARNING ||
				substr($errmsg,-25)!='SSL: fatal protocol error'
			)
		){
			restore_error_handler();
			trigger_error($errmsg,$errno<<8);
			set_error_handler('errorHandle');
		}
	}
	set_error_handler('errorHandle');

	# }}}

	# Send HTTP Packet {{{

	$fp=@fsockopen($ipaddress,$portval,$errno,$errval,5)
	    or havok(6,$servername,$portval);
	stream_set_timeout($fp,5);
	# for persistent connections, this may be necessary
	/*
	$ub=stream_get_meta_data($fp);
	$ub=$ub['unread_bytes'];
	if($ub>0) fread($fp,$ub);
	*/
	fwrite($fp,$out);

	# }}}

	# Retrieve and Parse response headers {{{

	$response='100';
	while($response=='100'){
		$responseline=fgets($fp,8192);
		$response=substr($responseline,9,3);

		$headers=array();
		while($curline!="\r\n" && $curline=fgets($fp,8192)){
			$harr=explode(':',$curline,2);
			$headers[strtolower($harr[0])][]=trim($harr[1]);
		}
	}

	//if($headers['pragma'][0]==null) header('Pragma: public');
	//if($headers['cache-control'][0]==null) header('Cache-Control: public');
	//if($headers['last-modified'][0]==null && $headers['expires']==null)
	//	header('Expires: '.date('D, d M Y H:i:s e',time()+86400));

	# read and store cookies
	if(!$OPTIONS['REMOVE_COOKIES']){
		for($i=0;$i<count($headers['set-cookie']);$i++){
			$thiscook=explode('=',$headers['set-cookie'][$i],2);
			if(!strpos($thiscook[1],';')) $thiscook[1].=';';
			$cook_val=substr($thiscook[1],0,strpos($thiscook[1],';'));
			$cook_domain=
				preg_replace('/^.*domain=[	 ]*\.?([^;]+).*?$/i','\1',
					$thiscook[1]); #*
			if($cook_domain==$thiscook[1]) $cook_domain=$cook_prefdomain;
			elseif(
				substr($cook_prefdomain,
					strlen($cook_prefdomain)-strlen($cook_domain),
					strlen($cook_domain))!=$cook_domain
			) continue;
			$cook_name=
				str_replace('.','_',$cook_domain).COOKIE_SEPARATOR.$thiscook[0];
			if($OPTIONS['ENCRYPT_COOKIES']){
				$cook_name=proxenc($cook_name);
				$cook_val=proxenc($cook_val);
			}
			dosetcookie($cook_name,$cook_val);
		}
	}

	# page redirected, send it back to the user
	if($response{0}=='3' && $response{1}=='0' && $response{2}!='4'){
		$urlobj=new aurl($url);
		$redirurl=framify_url(
			surrogafy_url($headers['location'][0],$urlobj),
			NEW_PAGETYPE_FRAMED_PAGE
		);

		fclose($fp);
		restore_error_handler();

		finish_noexit();
		header("Location: {$redirurl}");
		exit();
	}

	# parse the rest of the headers
	$oheaders=$headers;
	$oheaders['location']=$oheaders['content-length']=
		$oheaders['content-encoding']=$oheaders['set-cookie']=
		$oheaders['transfer-encoding']=$oheaders['connection']=
		$oheaders['keep-alive']=$oheaders['pragma']=$oheaders['cache-control']=
		$oheaders['expires']=null;

	while(list($key,$val)=each($oheaders))
		if(!empty($val[0])) header("{$key}: {$val[0]}");
	unset($oheaders);
	header("Status: {$response}");

	# }}}

	# Retrieve content {{{

	if(
		substr($headers['content-type'][0],0,4)=='text' ||
		substr($headers['content-type'][0],0,22)=='application/javascript' ||
		substr($headers['content-type'][0],0,24)=='application/x-javascript'
	){
		$justoutput=false;
		$justoutputnow=false;
	}
	else{
		$justoutputnow=($headers['content-encoding'][0]=='gzip'?false:true);
		$justoutput=true;
	}

	# Transfer-Encoding: chunked
	if($headers['transfer-encoding'][0]=='chunked'){
		$body=null;
		$chunksize=null;
		while($chunksize!==0){
			$chunksize=intval(fgets($fp,8192),16);
			$bufsize=$chunksize;
			while($bufsize>=1){
				$chunk=fread($fp,$bufsize);
				if($justoutputnow) echo $chunk;
				else $body.=$chunk;
				$bufsize-=strlen($chunk);
			}
			fread($fp,2);
		}
	}

	# Content-Length stuff - commented for even more chocolatey goodness
	# Some servers really botch this up it seems...
	/*elseif($headers['content-length'][0]!=null){
		$conlen=$headers['content-length'][0];
		$body=null;
		for($i=0;$i<$conlen;$i+=$read){
			$read=($conlen-$i<8192?$conlen-$i:8192);
			$byte=fread($fp,$read);
			if($justoutputnow) echo $byte;
			else $body.=$byte;
		}
	}*/

	# Generic stream getter
	else{
		if(function_exists('stream_get_contents')){
			if($justoutputnow) echo stream_get_contents($fp);
			else $body=stream_get_contents($fp);
		}
		else{
			$body=null;
			while(true){
				$chunk=fread($fp,8192);
				if(empty($chunk)) break;
				if($justoutputnow) echo $chunk;
				else $body.=$chunk;
			}
		}
	}

	fclose($fp);
	restore_error_handler();

	# }}}

	# GZIP, output, and return {{{

	if($headers['content-encoding'][0]=='gzip'){
		# http://us2.php.net/manual/en/function.gzdecode.php
		$temp=tempnam('/tmp','ff');
		@file_put_contents($temp,$body);
		ob_start();
		readgzfile($temp);
		$body=ob_get_clean();
		unlink($temp);
	}
	if($justoutput){
		if(!$justoutputnow) echo $body;
		finish();
	}

	return array($body,$url,$cook_prefix);

	# }}}

}

# }}}

# }}}

# PROXY EXECUTION {{{

# PROXY EXECUTION: COOKIE VARIABLES {{{

global $proxy_variables;
$proxy_variables=array(
	'user', COOK_PREF, COOK_PREF.'_set_values',
	COOK_PREF.'_tunnel_ip',COOK_PREF.'_tunnel_port',
	COOK_PREF.'_useragent',COOK_PREF.'_useragent_custom',
	COOK_PREF.'_url_form',
	COOK_PREF.'_remove_cookies',COOK_PREF.'_remove_referer',
	COOK_PREF.'_remove_scripts',COOK_PREF.'_remove_objects',
	COOK_PREF.'_encrypt_urls',COOK_PREF.'_encrypt_cookies');

# }}}

# PROXY_EXECUTION: REDIRECT IF FORM INPUT {{{

if(IS_FORM_INPUT){
	$theurl=framify_url(surrogafy_url(ORIG_URL),PAGETYPE_FRAME_TOP);
	header("Location: {$theurl}");
	finish();
}

# }}}

# PROXY EXECUTION: REFERER {{{

global $referer;
if($_SERVER['HTTP_REFERER']!=null && !$OPTIONS['REMOVE_REFERER']){
	$refurlobj=new aurl($_SERVER['HTTP_REFERER'], null, true);
	$referer=proxdec(preg_replace(
		'/^=(?:\&=|_\&=|\.\&=)?([^\&]*)[\s\S]*$/i','\1',
		$refurlobj->get_query()
	));
}
else $referer=null;

#$getkeys=array_keys($_GET);
#foreach($getkeys as $getvar){
#	if(!in_array($getvar,$proxy_variables)){
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

global $headers;
$pagestuff=getpage($curr_url);
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

$curr_url=$pagestuff[1];
define('PAGECOOK_PREFIX',$pagestuff[2]);
unset($pagestuff);
define('CONTENT_TYPE',
	preg_replace('/^([a-z0-9\-\/]+).*$/i','\1',$headers['content-type'][0])); #*

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

# PROXY EXECUTION: PAGE PARSING: PARSING FUNCTIONS {{{

function parse_html($regexp,$partoparse,$html,$addproxy,$framify){
	global $curr_urlobj;
	$newhtml=null;
	while(preg_match($regexp,$html,$matcharr,PREG_OFFSET_CAPTURE)){
		$nurl=surrogafy_url($matcharr[$partoparse][0],$curr_urlobj,$addproxy);
		if($framify) $nurl=framify_url($nurl,$framify);
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
			surrogafy_url(
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

# BENCHMARK
#echo
#	'total time: '.(microtime(true)-$totstarttime).
#	"<br />parse time: {$parsetime} seconds".
#	(isset($oparsetime)?"<br />other time 1: {$oparsetime} seconds":null).
#	(isset($oparsetime2)?"<br />other time 2: {$oparsetime2} seconds":null);

# }}}

finish_noexit();

############
## THE END ##
##############
#
# VIM is the ideal way to edit this file.  Automatic folding occurs making the
# blocks of code easier to read and navigate
# vim:foldmethod=marker
#
################## ?>
