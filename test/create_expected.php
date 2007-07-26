<?

if(!empty($_GET['assert'])){
	include('inc.php');
	dosandbox();
	create_expected($_GET['assert']);
	echo 'Success';
}
else
	echo 'Please specify an assert.';

?>
