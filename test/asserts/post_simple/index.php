<? if(
	ini_get('magic_quotes_sybase')==1 ||
	(ini_get('magic_quotes_sybase')=='' && get_magic_quotes_gpc())
){
	$_POST['allchars']=stripslashes($_POST['allchars']);
} ?>

all characters over post:

<?=$_POST['allchars'];?>
