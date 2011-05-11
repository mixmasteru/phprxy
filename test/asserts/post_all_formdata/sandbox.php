<?

$_SANDBOX['_SERVER']['REQUEST_METHOD']='POST';
$_SANDBOX['_SERVER']['CONTENT_TYPE']='multipart/form-data';

$name=tempnam('/tmp','SURROASSERT');
$fp=fopen($name,'w');
fwrite($fp,"`\t~\r!\n@ #$%^&*()");
fclose($fp);

$name2=tempnam('/tmp','SURROASSERT');
$fp=fopen($name2,'w');
fwrite($fp,"-=_+[]\\{}|;':\",./<>?");
fclose($fp);

$name3=tempnam('/tmp','SURROASSERT');
$fp=fopen($name3,'w');
fwrite($fp,"`\t~\r!\n@ #$%^&*()-=_+[]\\{}|;':\",./<>?");
fclose($fp);

$_SANDBOX['_FILES']['surro_assert']['tmp_name'][]=$name;
$_SANDBOX['_FILES']['surro_assert']['name'][]='surro_assert.html';
$_SANDBOX['_FILES']['surro_assert']['type'][]='text/html';
$_SANDBOX['_FILES']['surro_assert']['size'][]=16;
$_SANDBOX['_FILES']['surro_assert']['error'][]=UPLOAD_ERR_OK;

$_SANDBOX['_FILES']['surro_assert']['tmp_name'][]=$name2;
$_SANDBOX['_FILES']['surro_assert']['name'][]='surro_assert2.jpg';
$_SANDBOX['_FILES']['surro_assert']['type'][]='image/jpeg';
$_SANDBOX['_FILES']['surro_assert']['size'][]=20;
$_SANDBOX['_FILES']['surro_assert']['error'][]=UPLOAD_ERR_EXTENSION;

$_SANDBOX['_FILES']['surro_assert_solo']['tmp_name']=$name3;
$_SANDBOX['_FILES']['surro_assert_solo']['name']='surro_assert3.js';
$_SANDBOX['_FILES']['surro_assert_solo']['type']='application/x-javascript';
$_SANDBOX['_FILES']['surro_assert_solo']['size']=36;
$_SANDBOX['_FILES']['surro_assert_solo']['error']=UPLOAD_ERR_NO_FILE;

$_SANDBOX['_POST']['post_assert']="`\t~\r!\n@ #$%^&*()-=_+[]\\{}|;':\",./<>?";
$_SANDBOX['_POST']['post_assert_arr'][]=
	"`\t~\r!\n@ #$%^&*()-=_+[]\\{}|;':\",./<>?";
$_SANDBOX['_POST']['post_assert_arr'][]=
	"`\t~\r!\n@ #$%^&*()-=_+[]\\{}|;':\",./<>?";

?>
