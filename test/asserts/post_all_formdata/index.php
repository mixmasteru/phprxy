<? if(
	ini_get('magic_quotes_sybase')==1 ||
	(ini_get('magic_quotes_sybase')=='' && get_magic_quotes_gpc())
){
	$_POST['post_assert']=stripslashes($_POST['post_assert']);
	$_POST['post_assert_arr'][0]=stripslashes($_POST['post_assert_arr'][0]);
	$_POST['post_assert_arr'][1]=stripslashes($_POST['post_assert_arr'][1]);
} ?>

--POST--

<?=$_POST['post_assert']?>



--POST0--

<?=$_POST['post_assert_arr'][0]?>



--POST1--

<?=$_POST['post_assert_arr'][1]?>



--FILE--

name: <?=$_FILES['surro_assert_solo']['name']?>

type: <?=$_FILES['surro_assert_solo']['type']?>

size: <?=$_FILES['surro_assert_solo']['size']?>

error: <?=$_FILES['surro_assert_solo']['error']?>

contents:

<?=file_get_contents($_FILES['surro_assert_solo']['tmp_name'])?>

<? unlink($_FILES['surro_assert_solo']['tmp_name']); ?>



--FILE0--

name: <?=$_FILES['surro_assert']['name'][0]?>

type: <?=$_FILES['surro_assert']['type'][0]?>

size: <?=$_FILES['surro_assert']['size'][0]?>

error: <?=$_FILES['surro_assert']['error'][0]?>

contents:

<?=file_get_contents($_FILES['surro_assert']['tmp_name'][0])?>

<? unlink($_FILES['surro_assert']['tmp_name'][0]); ?>



--FILE1--

name: <?=$_FILES['surro_assert']['name'][1]?>

type: <?=$_FILES['surro_assert']['type'][1]?>

size: <?=$_FILES['surro_assert']['size'][1]?>

error: <?=$_FILES['surro_assert']['error'][1]?>

contents:

<?=file_get_contents($_FILES['surro_assert']['tmp_name'][1])?>

<? unlink($_FILES['surro_assert']['tmp_name'][1]); ?>
