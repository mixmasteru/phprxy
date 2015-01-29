<?php
# FIRST PAGE DISPLAYED WHEN ACCESSING PROXY {{{

if(
PAGETYPE_ID===PAGETYPE_FORCE_MAIN ||
(substr(QUERY_STRING,0,3)!='js_' && ORIG_URL==null)
){

	$useragent_platforms=array(
			array('Windows', 'windows', 'win32'),
			array('Linux', 'linux'),
			array('Macintosh', 'macintosh', 'mac_powerpc'),
			array('BSD', 'bsd')
	);

	$useragent_browsers=array(
			'firefox' => 'Firefox',
			'iceweasel' => 'Iceweasel',
			'konqueror' => 'Konqueror',
			'msie' => 'Internet Explorer',
			'netscape' => 'Netscape',
			'opera' => 'Opera',
			'safari' => 'Safari',
			'seamonkey' => 'SeaMonkey'
	);

	$useragentinfo=null;

	# parse platform
	$dobreak=false;
	foreach($useragent_platforms as $platform){
		for($i=1; $i<count($platform); $i++){
			if(stristr($_SERVER['HTTP_USER_AGENT'], $platform[$i])!==false){
				$useragentinfo.=$platform[0];
				$dobreak=true;
				break;
			}
		}

		if($dobreak)
			break;
	}

	if(!$dobreak)
		$useragentinfo.='Unknown';

	# separator
	$useragentinfo.=' / ';

	# parse browser
	$found=false;
	foreach($useragent_browsers as $substr=>$browser){
		if(stristr($_SERVER['HTTP_USER_AGENT'],$browser)!==false){
			$useragentinfo.=$browser;
			$found=true;
			break;
		}
	}
	if(!$found)
		$useragentinfo.='Unknown';

	# construct useragent options
	$ver=array(
	'dillo' => '0.8.6',
	'firefox' => '2.0',
	'gecko' => '20061024',
	'konq' => '3.5',
	'konq_minor' => '3.5.5',
	'links' => '2.1pre19',
	'lynx' => '2.8.5rel.1',
	'moz_rev' => '1.8.1',
	'msie6' => '6.0',
	'msie7' => '7.0',
	'opera' => '9.02',
	'safari' => '3.0',
	'webkit' => '521.25',
	'wget' => '1.10.2',
	'windows' => 'NT 5.1'
	);

	$useragent_array=array(
			array(null,"Actual ({$useragentinfo})"),
			array('-1',' [ Don\'t Send ] '),
			array("Mozilla/5.0 (Windows; U; Windows {$ver['windows']}; en-US; ".
					"rv:{$ver['moz_rev']}) Gecko/{$ver['gecko']} Firefox/".
					$ver['firefox'],
					"Windows XP / Firefox {$ver['firefox']}"),
					array("Mozilla/4.0 (compatible; MSIE {$ver['msie7']}; Windows ".
	      "{$ver['windows']}; SV1)", 'Windows XP / Internet Explorer 7'),
	      array("Mozilla/4.0 (compatible; MSIE {$ver['msie6']}; Windows ".
	 	      "{$ver['windows']}; SV1)", 'Windows XP / Internet Explorer 6'),
	 	      array("Opera/{$ver['opera']} (Windows {$ver['windows']}; U; en)",
	 	      "Windows XP / Opera {$ver['opera']}"),
	array("Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en-US; rv:".
	"{$ver['moz_rev']}) Gecko/{$ver['gecko']} Firefox/{$ver['firefox']}",
	"Mac OS X / Firefox {$ver['firefox']}"),
	array("Mozilla/5.0 (Macintosh; U; PPC Mac OS X; en) AppleWebKit/".
			"{$ver['webkit']} (KHTML, like Gecko) Safari/{$ver['webkit']}",
			'Mac OS X / Safari 3.0'),
	array("Opera/{$ver['opera']} (Macintosh; PPC Mac OS X; U; en)",
	"Mac OS X / Opera {$ver['opera']}"),
	array("Mozilla/5.0 (X11; U; Linux i686; en-US; rv:{$ver['moz_rev']}) ".
			"Gecko/{$ver['gecko']} Firefox/{$ver['firefox']}",
			"Linux / Firefox {$ver['firefox']}"),
			array("Opera/{$ver['opera']} (X11; Linux i686; U; en)",
			"Linux / Opera {$ver['opera']}"),
					array("Mozilla/5.0 (compatible; Konqueror/{$ver['konq']}; Linux) KHTML/".
					"{$ver['konq_minor']} (like Gecko)",
	      "Linux / Konqueror {$ver['konq_minor']}"),
	      array("Links ({$ver['links']}; Linux 2.6 i686; x)",
	      "Linux / Links ({$ver['links']})"),
	      array("Lynx/{$ver['lynx']}","Any / Lynx {$ver['lynx']}"),
	array("Dillo/{$ver['dillo']}","Any / Dillo {$ver['dillo']}"),
	array("Wget/{$ver['wget']}","Any / Wget {$ver['wget']}"),
	array('1',' [ Custom ]')
);

define('IPREGEXP',
	'/^((?:[0-2]{0,2}[0-9]{1,2}\.){3}[0-2]{0,2}[0-9]{1,2})\:([0-9]{1,5}$/');

$checkbox_array=array(
		'URL_FORM',
	'REMOVE_COOKIES',
	'REMOVE_REFERER',
	'REMOVE_SCRIPTS',
	'REMOVE_OBJECTS',
	'ENCRYPT_URLS',
	'ENCRYPT_COOKIES',
	'ENCODE_HTML'
);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>

<head>

<title><?php echo($LABEL['TITLE']); ?></title>
<link rel="stylesheet" type="text/css"
      href="<?php echo(THIS_SCRIPT); ?>?css_main" />

<style>
	input#proxy_submit_simple {
		display: <?php echo(($OPTIONS['SIMPLE_MODE']?'inline':'none')); ?>;
	}
</style>

<noscript><style>
	input#proxy_url { display: none; }
	a#proxy_link_author { float: none; }
	a#proxy_link_mode { display: none; }
	td#proxy_links_td { text-align: center; }
</style></noscript>

<script type="text/javascript"
        src="<?php echo(THIS_SCRIPT); ?>?js_funcs_nowrap"></script>

<script type="text/javascript" language="javascript"><!--
var advanced_mode=<?php echo(($OPTIONS['SIMPLE_MODE']?'false':'true')); ?>;
//--></script>

</head>

<body>

<? if( // main header include
	!empty($CONFIG['INCLUDE_MAIN_HEADER']) &&
	file_exists($CONFIG['INCLUDE_MAIN_HEADER'])
) include($CONFIG['INCLUDE_MAIN_HEADER']); ?>

<form method="post" id="proxy_form" onsubmit="return main_submit_code();">
<input type="hidden" name="<?php echo(COOK_PREF); ?>_set_values" value="1" />
<input type="hidden" id="proxy_url_hidden" disabled="disabled"
       name="<?php echo(COOK_PREF); ?>" />
<table id="proxy_table" cellpadding="0" cellspacing="4">

<tr>
	<td colspan="2" id="proxy_title"><?php echo($LABEL['TITLE']); ?></td>
</tr>

<tr>
	<td><?php echo($LABEL['URL']); ?></td>
	<td>
		<input type="text" id="proxy_url" class="proxy_text"
		       value="<?php echo(ORIG_URL); ?>" />
		<noscript>
			<input type="text" id="proxy_url_noscript" class="proxy_text"
			       name="<?php echo(COOK_PREF); ?>"
			       value="<?php echo(ORIG_URL); ?>" />
		</noscript>
		<input type="submit" id="proxy_submit_simple" class="proxy_submit"
		       value="<?php echo($LABEL['SUBMIT_SIMPLE']); ?>" />
	</td>
</tr>

<?php if(!$CONFIG['FORCE_DEFAULT_TUNNEL']){ ?>
<tr name="advanced_mode">
	<td><?php echo($LABEL['TUNNEL']); ?></td>
	<td>
		<input type="text" id="proxy_tunnel_ip" class="proxy_text"
		       name="<?php echo(COOK_PREF); ?>_tunnel_ip"
		       value="<?php echo($CONFIG['TUNNEL_IP']); ?>" />
		<input type="text" size="5" maxlength="5"
		       id="proxy_tunnel_port" class="proxy_text"
		       name="<?php echo(COOK_PREF); ?>_tunnel_port"
		       value="<?php echo($CONFIG['TUNNEL_PORT']); ?>" />
	</td>
</tr>
<?php } ?>

<?php if(!$CONFIG['FORCE_DEFAULT_USER_AGENT']){ ?>
<tr name="advanced_mode">
	<td><?php echo($LABEL['USER_AGENT']); ?></td>
	<td>
		<select name="<?php echo(COOK_PREF); ?>_useragent"
		        id="proxy_useragent" class="proxy_select"
		        onchange="useragent_change();">
			<?php foreach($useragent_array as $useragent){ ?>
			<option value="<?php echo($useragent[0]); ?>"
			 <?php if($OPTIONS['USER_AGENT']==$useragent[0])
			 	echo ' selected="selected"'; ?>
			><?php echo($useragent[1]); ?></option>
			<?php } ?>
		</select>
	</td>
</tr>
<tr id="proxy_useragent_custom_tr" name="advanced_mode"
    class="display_<?php echo(($OPTIONS['USER_AGENT']=='1'?'tr':'none')); ?>">
	<td><?php echo($LABEL['USER_AGENT_CUSTOM']); ?></td>
	<td>
		<input type="text" id="proxy_useragent_custom" class="proxy_text"
		       name="<?php echo(COOK_PREF); ?>_useragent_custom"
		       value="<?php echo($OPTIONS['USER_AGENT']); ?>" />
	</td>
</tr>
<?php } ?>

<?php
foreach($checkbox_array as $checkbox){
	if(!$CONFIG['FORCE_DEFAULT_'.$checkbox]){
		$lowername=strtolower($checkbox);
?>

<tr name="advanced_mode">
	<td>&nbsp;</td>
	<td>
		<input type="checkbox" id="proxy_<?php echo($lowername); ?>"
		       class="proxy_checkbox"
		       name="<?php echo(COOK_PREF); ?>_<?php echo($lowername); ?>"
		       <?php if($OPTIONS[$checkbox]) echo 'checked="checked"'; ?>
		/>&nbsp;<?php echo($LABEL[$checkbox]); ?>
	</td>
</tr>
<?php }
} ?>

<tr name="advanced_mode">
	<td colspan="2">
		<input type="submit" id="proxy_submit_main" class="proxy_submit"
		       value="<?php echo($LABEL['SUBMIT_MAIN']); ?>" />
	</td>
</tr>

<tr>
	<td colspan="2" id="proxy_links_td">
		<a id="proxy_link_author" class="proxy_link" href="http://bcable.net/">
			phprxy&nbsp;v<?php echo(VERSION); ?>
			<b>&middot;</b>&nbsp;Brad&nbsp;Cable
		</a>
		<a id="proxy_link_mode" class="proxy_link" href="#"
		   onclick="toggle_mode();">
			<?php echo($OPTIONS['SIMPLE_MODE']?'Advanced':'Simple');
			?>&nbsp;Mode
		</a>
	</td>
</tr>

</table>
</form>

<? if( // main footer include
	!empty($CONFIG['INCLUDE_MAIN_FOOTER']) &&
	file_exists($CONFIG['INCLUDE_MAIN_FOOTER'])
) include($CONFIG['INCLUDE_MAIN_FOOTER']); ?>

<noscript>
<br />
<b>**</b> phprxy has detected that your browser does not have Javascript
enabled. <b>**</b>
<br />
<b>**</b> phprxy requires Javascript in order to function to its full
potential. It is highly recommended that you have Javascript enabled for
privacy and security reasons. <b>**</b>
</noscript>

</body>

</html>



<?php exit(); }

# }}}

