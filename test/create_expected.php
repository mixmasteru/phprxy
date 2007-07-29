<?

$ce_assert=$_GET['assert'];
if(!empty($ce_assert)){
	include('inc.php');
	create_expected($ce_assert);
	echo 'Success';
}
else
	echo 'Please specify an assert.';

?>
