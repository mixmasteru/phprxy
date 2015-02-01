<?php
/**
 * regex
 *
 * @author Ulrich Pech
 * @link https://github.com/mixmasteru
 *
 * @copyright Surrogafier, Author: Brad Cable, Email: brad@bcable.net
 * @license BSD
 */
# REGEXPS {{{



# }}}

# REGEXPS: STATIC JAVASCRIPT REGEXPS PAGE {{{

if(QUERY_STRING=='js_regexps' || QUERY_STRING=='js_regexps_framed'){
	static_cache();
	?>//<script type="text/javascript">
<?php 
	$obj_regex = new regex($OPTIONS);
	echo(
	$obj_regex->convert_array_to_javascript().
	(
		$OPTIONS['REMOVE_OBJECTS']?
		'regexp_arrays["text/html"].push(Array(1,/<[\\\\/]?'.
			'(embed|param|object)[^>]*>/ig,""));':
		null
	)
); ?>
//</script><?php exit(); }