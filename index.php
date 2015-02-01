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

include 'src/conf.php';
include 'src/view/styles.php';
include 'src/session.php';
include 'src/functions.php';
include 'src/env.php';

include 'src/classes/aurl.php';
include 'src/classes/tcpip.php';
include 'src/classes/urlparser.php';
include 'src/classes/pageparser.php';
include 'src/classes/http.php';
include 'src/classes/regex.php';

include 'src/view/main.php';
include 'src/view/frame.php';
include 'src/view/css.php';
include 'src/view/js.php';

include 'src/regex.php';
include 'src/error.php';
include 'src/proxy.php';


# BENCHMARK
#echo
#	'total time: '.(microtime(true)-$totstarttime).
#	"<br />parse time: {$parsetime} seconds".
#	(isset($oparsetime)?"<br />other time 1: {$oparsetime} seconds":null).
#	(isset($oparsetime2)?"<br />other time 2: {$oparsetime2} seconds":null);

# }}}

finish_noexit();