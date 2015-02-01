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
# PROXY FUNCTIONS: ERRORS {{{

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

	include 'view/error.php';
	finish(); }

# }}}