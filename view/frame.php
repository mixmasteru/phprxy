<?php
# FRAMED PAGE WITH URL FORM {{{

if(
PAGETYPE_ID===PAGETYPE_FRAME_TOP &&
$OPTIONS['URL_FORM'] &&
ORIG_URL!=null
){ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo($LABEL['TITLE']); ?></title>
<style>

html, body {
	font-family: bitstream vera sans, arial;
	margin: 0px;
	padding: 0px;
	height: 100%;
	overflow: hidden;
}

form#url_form {
	margin: 0px;
	padding: 0px;
	height: 100%;
}

table#url_table {
	margin: 0px;
	padding: 0px;
	height: 100%;
	width: 100%;
}

td#url_table_td_input {
	width: 100%;
	padding: 3px;
	padding-left: 10px;
}

td#url_table_td_iframe {
	margin: 0px;
	padding: 0px;
	height: 100%;
}

a#url_link {
	color: #000000;
	font-weight: bold;
	padding: 8px;
	text-decoration: none;
}

a#url_link:hover {
	color: #000000;
	font-weight: bold;
	padding: 8px;
	text-decoration: underline;
}

input {
	border: 1px solid #000000;
	color: #000000;
}

input#url_input {
	width: 100%;
}

input#url_submit {
	background-color: #FFFFFF;
	margin-right: 3px;
}

iframe#url_iframe {
	border: 0px;
	border-top: 1px solid #000000;
	width: 100%;
	height: 100%;
}

</style>

<script type="text/javascript">
<!--

<?php echo(COOK_PREF); ?>=true;

function submit_code(){
<?php if($OPTIONS['ENCRYPT_URLS']){ ?>
	document.forms[0].<?php echo(COOK_PREF); ?>.value=
		<?php echo(COOK_PREF); ?>_pe.proxenc(
			document.forms[0].<?php echo(COOK_PREF); ?>.value
		);
<?php } ?>
	return true;
}

//-->
</script>

</head>
<body>

<? if( // URL form header include
	!empty($CONFIG['INCLUDE_URL_HEADER']) &&
	file_exists($CONFIG['INCLUDE_URL_HEADER'])
) include($CONFIG['INCLUDE_URL_HEADER']); ?>

<form id="url_form" method="get" onsubmit="return submit_code();">
<input type="hidden" name="" value="" />

<table cellspacing="0" cellpadding="0" id="url_table">
<tr>
	<td>
		<a href="<?php echo(THIS_SCRIPT.'?=-&='.OENC_URL); ?>"
		   id="url_link">Surrogafier
		</a>
	</td>
	<td>&nbsp;</td>
	<td id="url_table_td_input">
		<input type="text" id="url_input" name=""
			   value="<?php echo(ORIG_URL); ?>" />
	</td>
	<td>&nbsp;</td>
	<td>
		<input type="submit" id="url_submit"
		       value="<?php echo($LABEL['SUBMIT_SIMPLE']); ?>" />
	</td>
</tr>

<tr>
	<td colspan="5" id="url_table_td_iframe">
		<iframe frameborder="0" id="url_iframe"
		        name="<?php echo(COOK_PREF); ?>_top"
		        src="<?php echo(THIS_SCRIPT.'?=_&='.OENC_URL); ?>"></iframe>
	</td>
</tr>

</table>

</form>

</body>
</html>
<?php exit(); }

# }}}