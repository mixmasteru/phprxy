<?

$_SANDBOX['_SERVER']['REQUEST_METHOD']='POST';
$_SANDBOX['_SERVER']['CONTENT_TYPE']='multipart/form-data';

$name=tempnam('/tmp','SURROASSERT');
$fp=fopen($name,'w');
fwrite($fp,"`\t~\r!\n@ #$%^&*()-=_+[]\\{}|;':\",./<>?");
fclose($fp);

$_SANDBOX['_FILES']['surro_assert']['tmp_name']=$name;
$_SANDBOX['_FILES']['surro_assert']['name']='surro_assert.html';
$_SANDBOX['_FILES']['surro_assert']['type']='text/html';
$_SANDBOX['_FILES']['surro_assert']['size']=36;
$_SANDBOX['_FILES']['surro_assert']['error']=UPLOAD_ERR_OK;

?>
