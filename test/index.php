<? include('inc.php');
dosandbox();
doassert('markup',true); ?>
<html>
<head>
	<title>Surrogafier Testing</title>
	<style>
		.true { color: #20A020; }
		.false { color: #A02020; }
		.h { font-weight: bold; }
		.real { float: left; width: 50%; white-space: pre; }
		.expected { float: right; width: 50%; white-space: pre; }
		.clrb { clear: both; }
	</style>
</head>
<body>
<? dump_results(); ?>
</body>
</html>
