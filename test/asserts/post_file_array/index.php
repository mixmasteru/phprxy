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
