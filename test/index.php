<html>
<head>
	<title>Surrogafier Testing</title>
	<link href="assert.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="status"></div>
<div id="iframes"></div>
<script language="javascript">
<!--

var status=new Array();
status['working']=0;
status['true']=0;
status['false']=0;

function redraw_status(){
	var idstatus=document.getElementById('status');
	idstatus.innerHTML=
		'Working: '+status['working']+
		'&nbsp;&nbsp;'+
		'<font class="true">'+
		'True: '+status['true']+
		'</font>'+
		'&nbsp;&nbsp;'+
		'<font class="false">'+
		'False: '+status['false']+
		'</font>';
}

function doassert(name){
	var iframes=document.getElementById('iframes');
	iframes.innerHTML+=
		'<iframe src="doassert.php?assert='+name+'">'+
		'</iframe><br />';
	status['working']++;
}

function endassert(success){
	if(success)
		status['true']++;
	else
		status['false']++;
	status['working']--;
	redraw_status();
}

doassert('markup');
doassert('markup_remove');
doassert('post');
redraw_status();

//-->
</script>
</body>
</html>
