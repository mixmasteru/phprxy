<? if(
	ini_get('magic_quotes_sybase')==1 ||
	(ini_get('magic_quotes_sybase')=='' && get_magic_quotes_gpc())
){
	$_POST['allchars'][0]=stripslashes($_POST['allchars'][0]);
	$_POST['allchars'][1]=stripslashes($_POST['allchars'][1]);
} ?>

all characters over a post array:

<?
echo $_POST['allchars'][0];
echo ':::';
echo $_POST['allchars'][1];
?>
