<html>
<head>
	<title>Surrogafier Testing</title>
	<link href="assert.css" rel="stylesheet" type="text/css" />
	<style> body{ padding: 5px; } </style>
</head>
<body>
<div>
	<font class="working">
		Working:
		<font id="working">0</font>
	</font>
	&nbsp;<b>&middot;</b>&nbsp;
	<font class="true">
		True:
		<font id="true">0</font>
	</font>
	&nbsp;<b>&middot;</b>&nbsp;
	<font class="false">
		False:
		<font id="false">0</font>
	</font>
</div>
<b>DETAILS:</b>
<div id="iframes"></div>
<script language="javascript">
<!--

function incWorking(){
	var working=document.getElementById('working');
	working.innerHTML=parseInt(working.innerHTML)+1;
}

function decWorking(){
	var working=document.getElementById('working');
	working.innerHTML=parseInt(working.innerHTML)-1;
}

function incTrue(){
	var ttrue=document.getElementById('true');
	ttrue.innerHTML=parseInt(ttrue.innerHTML)+1;
}

function incFalse(){
	var ffalse=document.getElementById('false');
	ffalse.innerHTML=parseInt(ffalse.innerHTML)+1;
}

function doassert(name){
	var iframes=document.getElementById('iframes');
	iframes.innerHTML+=
		'<iframe id="'+name+'" src="doassert.php?assert='+name+'">'+
		'</iframe><br />';
	incWorking();
}

function endassert(success){
	if(success) incTrue();
	else incFalse();
	decWorking();
}

function toggle_expand(name){
	var ifrm=document.getElementById(name);
	if(ifrm.offsetHeight==14){
		var frmheight=
			ifrm.contentDocument.getElementsByTagName('body')[0].offsetHeight;
		ifrm.style.height=frmheight;
	}
	else
		ifrm.style.height='14px';
}

doassert('markup');
doassert('markup_remove');
doassert('post_simple');
doassert('post_simple_array');
doassert('post_file');
doassert('post_file_array');
doassert('post_all_formdata');

//-->
</script>
</body>
</html>
