<?php
/**
 * class for URL
 *
 * @author Ulrich Pech
 * @link https://github.com/mixmasteru
 *
 * @copyright Surrogafier, Author: Brad Cable, Email: brad@bcable.net
 * @license BSD
 */
class regex
{
	/**
	 * global options
	 * @var array
	 */
	protected $arr_option;
	
	protected $arr_regex			= null;	
	protected $base_regex 			= null;
	protected $end_of_script_tag	= null;
	protected $script_onevent_regex	= null;
	protected $js_varsect			= null;
	
	public function __construct(array $arr_option)
	{
		$this->arr_option = $arr_option;
		$this->bulidRegexes();
	}
	
	public function bool_to_js($bool)
	{
		return ($bool?'true':'false');
	}
	
	protected function fix_regexp($regexp)
	{
		// backreference cleanup
		$regexp=preg_replace('/\(\?P\<[a-z0-9_]+\>/i','(',$regexp);
		$regexp=preg_replace('/\(\?P\>[a-z0-9_]+\)/i',$this->js_varsect,$regexp);
		$regexp=preg_replace('/\(\?\<\![^\)]+?\)/i',$this->js_varsect,$regexp);
	
		return $regexp;
	}
	
	public function convert_array_to_javascript()
	{
		$js='regexp_arrays=new Array('.count($this->arr_regex).");\n";
		foreach ($this->arr_regex as $key => $arr)
		{
			$js.="regexp_arrays[\"{$key}\"]=new Array(".count($arr).");\n";
			for($i=0;$i<count($arr);$i++){
				$js.="regexp_arrays[\"{$key}\"][{$i}]=new Array(";
				if($arr[$i][0]==1)
					$js.=
					'1,'.escape_regexp($this->fix_regexp($arr[$i][2])).'g,"'.
					escape_regexp($this->fix_regexp($arr[$i][3]),true).'"';
				elseif($arr[$i][0]==2)
				$js.=
				'2,'.escape_regexp($this->fix_regexp($arr[$i][2])).
				"g,{$arr[$i][3]}".
				(count($arr[$i])<5?null:','.$this->bool_to_js($arr[$i][4])).
				(count($arr[$i])<6?null:",{$arr[$i][5]}");
				$js.=");\n";
			}
		}
		return stripslashes($js);
	}
	
	/**
	 * This is where all the parsing is defined.  If a site isn't being
	 * parsed properly, the problem is more than likely in this section.
	 * The rest of the code is just there to set up this wonderful bunch
	 * of incomprehensible regular expressions.
	 */
	protected function bulidRegexes()
	{
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
		$hook_js_methods = '(location\.(?:replace|assign))';
		
		$js_string_methods ='(?:anchor|big|blink|bold|charAt|charCodeAt|concat|fixed|fontcolor|'.
							'fontsize|fromCharCode|indexOf|italics|lastIndexOf|link|match|replace|'.
							'search|slice|small|split|strike|sub|substr|substring|sup|toLowerCase|'.
							'toUpperCase|toSource|valueOf)';
		$js_string_attrs ='(?:constructor|length|prototype)';
		
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
		$this->js_varsect = 	"(?:new{$g_plusspace})?[a-zA-Z_\$]".
						"(?:[a-zA-Z0-9\$\._]*[a-zA-Z0-9_])?";
		$js_jsvarsect = "(?:new{$g_plusspace})?[a-zA-Z_\$]".
						"(?:[a-zA-Z0-9\$\._]*[a-zA-Z0-9_\[\]])?";
		$n_js_varsect = '[^a-zA-Z\._\[\]]';
		
		$h_js_exprsect=
		"(?!function[^\(]|return|\/\*|\/\/)".
		"(?:{$g_quoteseg}|{$g_regseg}|{$this->js_varsect}|[0-9\.]+)";
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
		$js_beginright="((?:".
						"(?<!:[\/])[\/](?![\/])|[;\{\(=\+\-\*]|".
		"[\}\)]{$g_anyspace};{$g_anyspace})".
		"{$g_anyspace})";
		#$js_beginright=
		#	"((?:[;\{\(=\+\-\*]|[\}\)]{$g_anyspace};{$g_anyspace}|".
		#	"(?<!:[\/])[\/](?![\/])){$g_justspace})";
		#$js_beginright="((?:[;\{\}\(\)=\+\-\*]|(?<!:[\/])[\/](?![\/])){$g_justspace})";
		
		$js_xmlhttpreq="(?<!XMLHttpRequest_wrap\(new )".
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
		
		$js_regexp_arrays = array(
		
		# object.attribute parsing (set)
		
		# prepare for set for +=
		array(1,2,
		"/{$js_begin}{$js_expr_get}\.({$this->js_varsect}){$g_anyspace}\+=/im",
		"\\1\\2.\\3=".COOK_PREF.".getAttr(\\2,/\\3/)+"),
		# set for =
		array(1,2,
				"/{$js_begin_strict}{$js_expr_set}\.(({$this->js_varsect}){$g_anyspace}=".
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
		"/{$js_beginright}{$n_js_set_left}{$js_expr_get}\.({$this->js_varsect})".
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
				($this->arr_option['ENCRYPT_URLS']?array(1,2,
				"/{$js_begin}((?:[^\) \{\}]*(?:\)\.{0,1}))+)(\.submit{$g_anyspace}\(\)".
		"){$l_js_end}/im",
				"\\1void((\\2.method==\"post\"?null:\\2\\3));")
				:null),
		
				);
		
				# }}}
		
		# REGEXPS: HTML/CSS PARSING {{{

		$this->arr_regex = array(
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
													($this->arr_option['ENCRYPT_URLS']?
													' onsubmit="return '.COOK_PREF.'.form_encrypt(this);">':'>')),
	array(1,1,
		"/(<form{$html_formnotpost}+)>(?!<!--".COOK_PREF.'-->)/im',
				'\1 target="_parent"><!--'.COOK_PREF.
						'--><input type="hidden" name="" value="_">'),
		
						# deal with the form button for encrypted URLs
	($this->arr_option['ENCRYPT_URLS']?array(1,1,
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
					($this->arr_option['URL_FORM'] && PAGE_FRAMED?array(2,1,
							"/<a(?:rea)?{$g_plusspace}[^>]*href{$g_anyspace}={$g_anyspace}".
							"{$html_reg}/im",
								1,false,NEW_PAGETYPE_FRAME_TOP)
								:null),
								($this->arr_option['URL_FORM'] && PAGE_FRAMED?array(2,1,
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
		
		array_push($this->arr_regex['text/html'],
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
		$this->base_regex 			= 	"<base[^>]* href{$g_anyspace}={$g_anyspace}{$html_reg}[^>]*>";
		$this->end_of_script_tag	= 	"(?:{$g_anyspace}(?:\/\/)?{$g_anyspace}-->{$g_anyspace})?<\/script>";
		$this->script_onevent_regex	= 	"( on[a-z]{3,20}=(?:\"[^\"]+\"|'[^']+'|[^\"' >][^ >]+[^\"' >])|".
										" href=(?:\"{$g_anyspace}javascript:[^\"]+\"|".
										"'{$g_anyspace}javascript:[^']+'|".
										"{$g_anyspace}javascript:[^\"' >][^ >]+[^\"' >]))";
		
		unset(
				$g_justspace, $g_plusjustspace, $g_anyspace, $g_plusspace, $g_operand,
				$g_n_operand, $g_quoteseg, $g_regseg,
		
				$hook_html_attrs, $html_frametargets, $hook_js_attrs, $hook_js_getattrs,
				$hook_js_methods, $js_string_methods, $js_string_attrs,
		
				$js_jsvarsect, $n_js_varsect, $h_js_exprsect, $js_exprsect,
				$js_expr, $js_expr2, $js_expr3, $js_expr4,
		
				$l_js_end, $n_l_js_end, $js_begin, $js_end, $js_begin_strict_end,
				$js_begin_strict, $n_js_string, $n_js_set, $n_js_set_left, $wrap_js_end,
				$js_beginright, $js_xmlhttpreq,
		
				$h_html_noquot, $html_reg, $js_newobj, $html_formnotpost,
		
				$js_regexp_arrays
		);
	}
	
	//define('BASE_REGEXP
	//define('END_OF_SCRIPT_TAG
	//define('REGEXP_SCRIPT_ONEVENT
	//$regexp_arrays
	/*********************** GET SET *************************/
	
	public function getRegexes()
	{
		return $this->arr_regex;
	}
	
	public function getBaseRegex()
	{
		return $this->base_regex;
	}
	
	public function getEndOfScriptTag()
	{
		return $this->end_of_script_tag;
	}
	
	public function getScriptOnEventRegex()
	{
		return $this->script_onevent_regex;
	}
}