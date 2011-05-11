<? if(
	ini_get('magic_quotes_sybase')==1 ||
	(ini_get('magic_quotes_sybase')=='' && get_magic_quotes_gpc())
){
	$_COOKIE['asd']=stripslashes($_COOKIE['asd']);
}

echo($_COOKIE['asd']);
include('../include/markup.php');
?>
