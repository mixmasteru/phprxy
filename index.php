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
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', '1');
define('THIS_FILE',"{$_SERVER['DOCUMENT_ROOT']}{$_SERVER['PHP_SELF']}");

include 'conf.php';
include 'view/styles.php';
include 'session.php';
include 'functions.php';
include 'env.php';

include 'classes/aurl.php';
include 'classes/tcpip.php';
include 'classes/urlparser.php';

include 'view/main.php';
include 'view/frame.php';
include 'view/css.php';
include 'view/js.php';

include 'regex.php';
include 'error.php';
include 'http.php';
include 'proxy.php';


# BENCHMARK
#echo
#	'total time: '.(microtime(true)-$totstarttime).
#	"<br />parse time: {$parsetime} seconds".
#	(isset($oparsetime)?"<br />other time 1: {$oparsetime} seconds":null).
#	(isset($oparsetime2)?"<br />other time 2: {$oparsetime2} seconds":null);

# }}}

finish_noexit();