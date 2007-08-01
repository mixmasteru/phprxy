name: <?=$_FILES['surro_assert']['name']?>

type: <?=$_FILES['surro_assert']['type']?>

size: <?=$_FILES['surro_assert']['size']?>

error: <?=$_FILES['surro_assert']['error']?>

contents:

<?=file_get_contents($_FILES['surro_assert']['tmp_name'])?>

<? unlink($_FILES['surro_assert']['tmp_name']); ?>
