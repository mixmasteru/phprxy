<?php
/**
 * phprxy
 *
 * @author Ulrich Pech
 * @link https://github.com/mixmasteru
 *
 * @copyright Surrogafier, Author: Brad Cable, Email: brad@bcable.net
 * @license BSD
 */
# PROXY FUNCTIONS: HTTP {{{

function httpclean($str){
	return str_replace(' ','+',
		preg_replace('/([^":\-_\.0-9a-z ])/ie',
			'\'%\'.(strlen(dechex(ord(\'\1\')))==1?\'0\':null).'.
			'strtoupper(dechex(ord(\'\1\')))',
		$str));
}

function getpage($url){
	global $CONFIG,$OPTIONS,$headers,$out,$proxy_variables,$referer;
	
	$obj_tcpip = new tcpip($CONFIG);
	# Generate HTTP packet content {{{

	$content=null;
	$is_formdata=substr($_SERVER['CONTENT_TYPE'],0,19)=='multipart/form-data';

	# Generate for multipart & handle file uploads {{{

	if($is_formdata){
		$strnum=null;
		for($i=0; $i<29; $i++) $strnum.=rand(0,9);
		$boundary="---------------------------{$strnum}";

		# parse POST variables
		while(list($key,$val)=each($_POST)){
			if(!is_array($val)){
				$content.=
					"--{$boundary}\r\n".
					"Content-Disposition: form-data; name=\"{$key}\"\r\n".
					"\r\n{$val}\r\n";
			}
			else{
				while(list($key2,$val2)=each($val)){
					$content.=
						"--{$boundary}\r\n".
						"Content-Disposition: form-data; name=\"{$key}[]\"\r\n".
						"\r\n{$val2}\r\n";
				}
			}
		}

		# parse uploaded files
		while(list($key,$val)=each($_FILES)){
			if(!is_array($val['name'])){
				$fcont=file_get_contents($val['tmp_name']);
				@unlink($val['tmp_name']);
				$content.=
					"--{$boundary}\r\n".
					"Content-Disposition: form-data; name=\"{$key}\"; ".
						"filename=\"{$val['name']}\"\r\n".
					"Content-Type: {$val['type']}\r\n".
					"\r\n{$fcont}\r\n";
			}
			else{
				for($i=0; $i<count($val['name']); $i++){
					$fcont=file_get_contents($val['tmp_name'][$i]);
					@unlink($val['tmp_name'][$i]);
					$content.=
						"--{$boundary}\r\n".
						"Content-Disposition: form-data; name=\"{$key}[]\"; ".
							"filename=\"{$val['name'][$i]}\"\r\n".
						"Content-Type: {$val['type'][$i]}\r\n".
						"\r\n{$fcont}\r\n";
				}
			}
		}
		$content.="--{$boundary}--\r\n";
	}

	# }}}

	# Generate for standard POST {{{

	else{
		$postkeys=array_keys($_POST);
		foreach($postkeys as $postkey){
			if(!in_array($postkey,$proxy_variables)){
				if(!is_array($_POST[$postkey]))
					$content.=
						($content!=null?'&':null).
						httpclean($postkey).'='.httpclean($_POST[$postkey]);
				else{
					foreach($_POST[$postkey] as $postval)
						$content.=
							($content!=null?'&':null).
							httpclean($postkey).'%5B%5D='.httpclean($postval);
				}
			}
		}
	}

	# }}}

	# }}}

	# URL setup {{{

	$urlobj=new aurl($url);

	# don't access SSL sites unless the proxy is being accessed through SSL too
	if(
		$urlobj->get_proto()=='https' && $CONFIG['PROTO']!='https' &&
		(
			!is_array($_SESSION['ssl_domains']) ||
			(
				is_array($_SESSION['ssl_domains']) &&
				!in_array($urlobj->get_servername(),$_SESSION['ssl_domains'])
			)
		)
	){
		# ignore certain file types from worrying about this
		$skip = false;
		foreach($CONFIG['SSL_WARNING_IGNORE_FILETYPES'] as $filetype){
			if(substr($urlobj->get_file(), -strlen($filetype)) == $filetype)
				$skip = true;
		}
		if(!$skip) havok(8,$urlobj->get_servername());
	}

	# get request URL
	$query=$urlobj->get_query();
	$requrl=
		$urlobj->get_path().
		$urlobj->get_file().
		(!empty($query)?"?{$query}":null);

	# }}}

	# HTTP Authorization and Cache stuff {{{
	$http_auth=null;
	if(extension_loaded('apache')){
		$fail=false;
		$cheaders=getallheaders();
		$http_auth=$reqarray['Authorization'];
	}
	else $fail=true;

	$authorization=
		($fail?$_SERVER['HTTP_AUTHORIZATION']:$cheaders['Authorization']);
	$cache_control=
		($fail?$_SERVER['HTTP_CACHE_CONTROL']:$cheaders['Cache-Control']);
	$if_modified=
		($fail?$_SERVER['HTTP_IF_MODIFIED_SINCE']:
		 $cheaders['If-Modified-Since']);
	$if_none_match=
		($fail?$_SERVER['HTTP_IF_NONE_MATCH']:$cheaders['If-None-Match']);

	if($fail){
		if(!empty($authorization)) $http_auth=$authorization;
		elseif(
			!empty($_SERVER['PHP_AUTH_USER']) &&
			!empty($_SERVER['PHP_AUTH_PW'])
		) $http_auth=
			'Basic '.
			base64_encode(
				"{$_SERVER['PHP_AUTH_USER']}:{$_SERVER['PHP_AUTH_PW']}");
		elseif(!empty($_SERVER['PHP_AUTH_DIGEST']))
			$http_auth="Digest {$_SERVER['PHP_AUTH_DIGEST']}";
	}
	# }}}

	# HTTP packet construction {{{

	# figure out what we are connecting to
	if($OPTIONS['TUNNEL_IP']!=null && $OPTIONS['TUNNEL_PORT']!=null){
		$servername=$OPTIONS['TUNNEL_IP'];
		$ipaddress=$obj_tcpip->get_check($servername);
		$portval=$OPTIONS['TUNNEL_PORT'];
		$requrl=$urlobj->get_url(false);
	}
	else{
		$servername=$urlobj->get_servername();
		$ipaddress=
			(
				$urlobj->get_proto()=='ssl' || $urlobj->get_proto()=='https'?
				'ssl://':
				null
			).
			$obj_tcpip->get_check($servername);
		$portval=$urlobj->get_portval();
	}

	# begin packet construction
	$out=
		($content==null?'GET':'POST').' '.
			str_replace(' ','%20',$requrl)." HTTP/1.1\r\n".
		"Host: ".$urlobj->get_servername().
			(
				($portval!=80 && (
					$urlobj->get_proto()=='https'?$portval!=443:true
				))?
				":$portval":
				null
			)."\r\n";

	# user agent and auth headers
	global $useragent;
	$useragent=null;
	if($OPTIONS['USER_AGENT']!='-1'){
		$useragent=$OPTIONS['USER_AGENT'];
		if(empty($useragent)) $useragent=$_SERVER['HTTP_USER_AGENT'];
		if(!empty($useragent)) $out.="User-Agent: $useragent\r\n";
	}
	if(!empty($http_auth)) $out.="Authorization: $http_auth\r\n";

	# referer headers
	if(!$OPTIONS['REMOVE_REFERER'] && !empty($referer))
		$out.='Referer: '.str_replace(' ','+',$referer)."\r\n";

	# POST headers
	if($content!=null)
		$out.=
			'Content-Length: '.strlen($content)."\r\n".
			'Content-Type: '.
				(
					$is_formdata?
					"multipart/form-data; boundary={$boundary}":
					'application/x-www-form-urlencoded'
				)."\r\n";

	# cookie headers
	$cook_prefdomain=
		preg_replace('/^www\./i',null,$urlobj->get_servername()); #*
	$cook_prefix=str_replace('.','_',$cook_prefdomain).COOKIE_SEPARATOR;
	if(!$OPTIONS['REMOVE_COOKIES'] && count($_COOKIE)>0){
		$addtoout=null;
		reset($_COOKIE);
		while(list($key,$val)=each($_COOKIE)){
			if(
				$key{0}!='~' && strtolower(substr($key,0,3))!='%7e' &&
				str_replace(COOKIE_SEPARATOR,null,$key)==$key
			) continue;
			if($OPTIONS['ENCRYPT_COOKIES']){
				$key=proxdec($key);
				$val=proxdec($val);
			}
			$cook_domain=
				substr($key,0,strpos($key,COOKIE_SEPARATOR)).COOKIE_SEPARATOR;
			if(
				substr($cook_prefix,strlen($cook_prefix)-strlen($cook_domain),
					strlen($cook_domain))!=$cook_domain
			) continue;
			$key=
				substr($key,strlen($cook_domain),
					strlen($key)-strlen($cook_domain));
			if(!in_array($key,$proxy_variables)) $addtoout.=" $key=$val;";
		}
		if(!empty($addtoout)){
			$addtoout.="\r\n";
			$out.="Cookie:{$addtoout}";
		}
	}

	# final packet headers and content
	$out.=
		"Accept: */*;q=0.1\r\n".
		($CONFIG['GZIP_PROXY_SERVER']?"Accept-Encoding: gzip\r\n":null).
		//"Accept-Charset: ISO-8859-1,utf-8;q=0.1,*;q=0.1\r\n".
		/*/
		"Keep-Alive: 300\r\n".
		"Connection: keep-alive\r\n".                          /*/
		"Connection: close\r\n".                               //*/
		($cache_control!=null?"Cache-Control: $cache_control\r\n":null).
		($if_modified!=null?"If-Modified-Since: $if_modified\r\n":null).
		($if_none_match!=null?"If-None-Match: $if_none_match\r\n":null).
		"\r\n{$content}";

	# }}}

	# Ignore SSL errors {{{

	# This part ignores any "SSL: fatal protocol error" errors, and makes sure
	# other errors are still triggered correctly
	function errorHandle($errno,$errmsg){
		if(
			$errno<=E_PARSE && (
				$errno!=E_WARNING ||
				substr($errmsg,-25)!='SSL: fatal protocol error'
			)
		){
			restore_error_handler();
			trigger_error($errmsg,$errno<<8);
			set_error_handler('errorHandle');
		}
	}
	set_error_handler('errorHandle');

	# }}}

	# Send HTTP Packet {{{

	$fp=@fsockopen($ipaddress,$portval,$errno,$errval,5)
	    or havok(6,$servername,$portval);
	stream_set_timeout($fp,5);
	# for persistent connections, this may be necessary
	/*
	$ub=stream_get_meta_data($fp);
	$ub=$ub['unread_bytes'];
	if($ub>0) fread($fp,$ub);
	*/
	fwrite($fp,$out);

	# }}}

	# Retrieve and Parse response headers {{{

	$response='100';
	while($response=='100'){
		$responseline=fgets($fp,8192);
		$response=substr($responseline,9,3);

		$headers=array();
		while($curline!="\r\n" && $curline=fgets($fp,8192)){
			$harr=explode(':',$curline,2);
			$headers[strtolower($harr[0])][]=trim($harr[1]);
		}
	}

	//if($headers['pragma'][0]==null) header('Pragma: public');
	//if($headers['cache-control'][0]==null) header('Cache-Control: public');
	//if($headers['last-modified'][0]==null && $headers['expires']==null)
	//	header('Expires: '.date('D, d M Y H:i:s e',time()+86400));

	# read and store cookies
	if(!$OPTIONS['REMOVE_COOKIES']){
		for($i=0;$i<count($headers['set-cookie']);$i++){
			$thiscook=explode('=',$headers['set-cookie'][$i],2);
			if(!strpos($thiscook[1],';')) $thiscook[1].=';';
			$cook_val=substr($thiscook[1],0,strpos($thiscook[1],';'));
			$cook_domain=
				preg_replace('/^.*domain=[	 ]*\.?([^;]+).*?$/i','\1',
					$thiscook[1]); #*
			if($cook_domain==$thiscook[1]) $cook_domain=$cook_prefdomain;
			elseif(
				substr($cook_prefdomain,
					strlen($cook_prefdomain)-strlen($cook_domain),
					strlen($cook_domain))!=$cook_domain
			) continue;
			$cook_name=
				str_replace('.','_',$cook_domain).COOKIE_SEPARATOR.$thiscook[0];
			if($OPTIONS['ENCRYPT_COOKIES']){
				$cook_name=proxenc($cook_name);
				$cook_val=proxenc($cook_val);
			}
			dosetcookie($cook_name,$cook_val);
		}
	}

	# page redirected, send it back to the user
	if($response{0}=='3' && $response{1}=='0' && $response{2}!='4'){
		$urlobj=new aurl($url);
		$obj_urlparser = new urlparser($urlobj);
		$redirurl=$obj_urlparser->framify_url(
			$obj_urlparser->surrogafy_url($headers['location'][0],$urlobj),
			NEW_PAGETYPE_FRAMED_PAGE
		);

		fclose($fp);
		restore_error_handler();

		finish_noexit();
		header("Location: {$redirurl}");
		exit();
	}

	# parse the rest of the headers
	$oheaders=$headers;
	$oheaders['location']=$oheaders['content-length']=
		$oheaders['content-encoding']=$oheaders['set-cookie']=
		$oheaders['transfer-encoding']=$oheaders['connection']=
		$oheaders['keep-alive']=$oheaders['pragma']=$oheaders['cache-control']=
		$oheaders['expires']=null;

	while(list($key,$val)=each($oheaders))
		if(!empty($val[0])) header("{$key}: {$val[0]}");
	unset($oheaders);
	header("Status: {$response}");

	# }}}

	# Retrieve content {{{

	if(
		substr($headers['content-type'][0],0,4)=='text' ||
		substr($headers['content-type'][0],0,22)=='application/javascript' ||
		substr($headers['content-type'][0],0,24)=='application/x-javascript'
	){
		$justoutput=false;
		$justoutputnow=false;
	}
	else{
		$justoutputnow=($headers['content-encoding'][0]=='gzip'?false:true);
		$justoutput=true;
	}

	# Transfer-Encoding: chunked
	if($headers['transfer-encoding'][0]=='chunked'){
		$body=null;
		$chunksize=null;
		while($chunksize!==0){
			$chunksize=intval(fgets($fp,8192),16);
			$bufsize=$chunksize;
			while($bufsize>=1){
				$chunk=fread($fp,$bufsize);
				if($justoutputnow) echo $chunk;
				else $body.=$chunk;
				$bufsize-=strlen($chunk);
			}
			fread($fp,2);
		}
	}

	# Content-Length stuff - commented for even more chocolatey goodness
	# Some servers really botch this up it seems...
	/*elseif($headers['content-length'][0]!=null){
		$conlen=$headers['content-length'][0];
		$body=null;
		for($i=0;$i<$conlen;$i+=$read){
			$read=($conlen-$i<8192?$conlen-$i:8192);
			$byte=fread($fp,$read);
			if($justoutputnow) echo $byte;
			else $body.=$byte;
		}
	}*/

	# Generic stream getter
	else{
		if(function_exists('stream_get_contents')){
			if($justoutputnow) echo stream_get_contents($fp);
			else $body=stream_get_contents($fp);
		}
		else{
			$body=null;
			while(true){
				$chunk=fread($fp,8192);
				if(empty($chunk)) break;
				if($justoutputnow) echo $chunk;
				else $body.=$chunk;
			}
		}
	}

	fclose($fp);
	restore_error_handler();

	# }}}

	# GZIP, output, and return {{{

	if($headers['content-encoding'][0]=='gzip'){
		# http://us2.php.net/manual/en/function.gzdecode.php
		$temp=tempnam('/tmp','ff');
		@file_put_contents($temp,$body);
		ob_start();
		readgzfile($temp);
		$body=ob_get_clean();
		unlink($temp);
	}
	if($justoutput){
		if(!$justoutputnow) echo $body;
		finish();
	}

	return array($body,$url,$cook_prefix);

	# }}}

}

# }}}