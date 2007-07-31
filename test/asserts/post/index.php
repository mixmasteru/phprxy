all characters over post:
<? if(get_magic_quotes_gpc())
	$_POST['allchars']=stripslashes($_POST['allchars']);
echo $_POST['allchars']; ?>
