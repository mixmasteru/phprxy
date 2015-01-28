<?php
# JAVASCRIPT STATIC CONTENT/FUNCTIONS {{{

if(
QUERY_STRING=='js_funcs' ||
QUERY_STRING=='js_funcs_framed' ||
QUERY_STRING=='js_funcs_nowrap'
){

	if(QUERY_STRING=='js_funcs_nowrap')
		$do_wrap=false;
	else $do_wrap=true;

	static_cache();

	?>//<script type="text/javascript">

// JAVASCRIPT FUNCS: FUNCTIONS FOR NON-WRAPPED PAGES {{{

<?php if(!$do_wrap){ ?>

function useragent_change(){
	var ua=document.getElementById('proxy_useragent');
	var uac=document.getElementById('proxy_useragent_custom');
	var uacTR=document.getElementById('proxy_useragent_custom_tr');

	if(parseInt(ua.value)==1) uacTR.className="display_tr";
	else uacTR.className="display_none";
}

function toggle_mode(){
	var url=document.getElementById('proxy_url');
	var simpBut=document.getElementById('proxy_submit_simple');
	var modeLink=document.getElementById('proxy_link_mode');
	var advTR=document.getElementsByName('advanced_mode');

	for(var i=0; i<advTR.length; i++){
		if(advanced_mode) advTR[i].style.display="none";
		else advTR[i].style.display="table-row";
	}

	if(advanced_mode){
		url.style.width="<?php echo($CONFIG['SIMPLE_MODE_URLWIDTH']) ?>";
		simpBut.style.display="inline";
		modeLink.innerHTML="Advanced&nbsp;Mode";
	}

	else{
		url.style.width="100%";
		simpBut.style.display="none";
		modeLink.innerHTML="Simple&nbsp;Mode";
	}

	advanced_mode=!advanced_mode;
}

function main_submit_code(){
	var dgEBI=function(id){ return document.getElementById(id); }
	dgEBI('proxy_url_hidden').disabled=false;
	if(dgEBI('proxy_encrypt_urls').checked)
		dgEBI('proxy_url_hidden').value=
			<?php echo(COOK_PREF); ?>_pe.proxenc(dgEBI('proxy_url').value);
	else dgEBI('proxy_url_hidden').value=dgEBI('proxy_url').value;
	return true;
}

<?php } ?>

// }}}

// JAVASCRIPT FUNCS: CRYPTOGRAPHIC FUNCTIONS {{{

<?php echo(COOK_PREF); ?>_pe={

expon:function(a,b){
	var num;
	if(b==0) return 1;
	num=a; b--;
	while(b>0){ num*=a; b--; }
	return num;
},

dectobin:function(){
	var dec=arguments[0],chars=arguments[1]||8,binrep="";
	for(j=chars-1;j>=0;j--){
		if(dec>=this.expon(2,j)){
			binrep+="1"; dec-=this.expon(2,j);
		}
		else binrep+="0";
	}
	return binrep;
},

bintodec:function(){
	var bin=arguments[0],chars=arguments[1]||8,dec=0;
	for(var j=0;j<chars;j++)
		if(bin.substring(j,j+1)=="1") dec+=this.expon(2,chars-1-j);
	return dec;
},

b64e:function(string){
	var encstr="",binrep="";
	var charbin,charnum;
	for(var i=0;i<string.length;i++){
		charnum=string.charCodeAt(i);
		binrep+=this.dectobin(charnum);
	}
	while(binrep.length%6) binrep+="00";
	for(var i=1;i*6<=binrep.length;i++){
		charbin=binrep.substring((i-1)*6,i*6);
		charnum=this.bintodec(charbin,6);
		if(charnum<=25) charnum+=65;
		else if(charnum<=51) charnum+=71;
		else if(charnum<=61) charnum-=4;
		else if(charnum==62) charnum=43;
		else if(charnum==63) charnum=47;
		encstr+=String.fromCharCode(charnum);
	}
	while(encstr.length%8) encstr+="=";
	return encstr;
},

proxenc:function(url){
	var new_url="";
	var charnum;
	if(url.substring(0,1)=="~" || url.substring(0,3).toLowerCase()=="%7e")
		return url;
	url=encodeURIComponent(url);
	var sess_pref="<?php echo(SESS_PREF); ?>";
	for(i=0;i<url.length;i++){
		charnum=url.charCodeAt(i);
		charnum+=sess_pref.charCodeAt(i%sess_pref.length);
		while(charnum>126) charnum-=94;
		new_url+=String.fromCharCode(charnum);
	}
	return "~"+encodeURIComponent(this.b64e(new_url));
},

b64d:function(str){
	var binrep="",decstr="";
	var charnum,charbin;
	str=str.replace(/[=]*$/,"");
	for(var i=0;i<str.length;i++){
		charnum=str.charCodeAt(i);
		if(charnum>=97) charnum-=71;
		else if(charnum>=65) charnum-=65;
		else if(charnum>=48) charnum+=4;
		else if(charnum==43) charnum=62;
		else if(charnum==47) charnum=63;
		binrep+=this.dectobin(charnum,6);
	}
	for(var i=0;i+8<binrep.length;i+=8){
		charbin=binrep.substr(i,8);
		decstr+=String.fromCharCode(this.bintodec(charbin));
	}
	return decstr;
},

proxdec:function(url){
	var new_url,charnum;
	if(url.substr(0,1)!='~' && url.substr(0,3).toLowerCase()!='%7e') return url;
	while(url.substr(0,1)=='~' || url.substr(0,3).toLowerCase()=='%7e'){
		url=url.substr(1,url.length-1);
		url=this.b64d(url);
		new_url="";
		for(i=0;i<url.length;i++){
			charnum=url.charCodeAt(i);
			charnum-="<?php echo(SESS_PREF); ?>".charCodeAt(
				i%"<?php echo(SESS_PREF); ?>".length);
			while(charnum<32) charnum+=94;
			new_url+=String.fromCharCode(charnum);
		}
		url=new_url;
	}
	return decodeURIComponent(url); // urldecode()
},

}

// }}}

// JAVASCRIPT FUNCS: COOK_PREF OBJECT {{{

<?php if($do_wrap){ ?>

<?php echo(COOK_PREF); ?>={

parse_attrs:new Array(
	'action',
	'backgroundImage',
	'codebase',
	'href',
	'location',
	'pluginspage',
	'src'
),

URLREG:<?php echo(substr(URLREG,0,strlen(URLREG)-1)); ?>,
THIS_SCRIPT:"<?php echo(THIS_SCRIPT); ?>",
COOK_PREF:"<?php echo(COOK_PREF); ?>",
pe:<?php echo(COOK_PREF); ?>_pe,
gen_curr_urlobj:function(){ this.curr_urlobj=new this.aurl(this.CURR_URL); },

getCookieArr:function(){ return document.cookie.split("; "); },

aurl:function(url,topurl){
	this.URLREG=<?php echo(COOK_PREF); ?>.URLREG;
	this.THIS_SCRIPT=<?php echo(COOK_PREF); ?>.THIS_SCRIPT;
	this.ENCRYPT_URLS=<?php echo(COOK_PREF); ?>.ENCRYPT_URLS;

	this.trim=function(str){ return str.replace(/^\s*([\s\S]*?)\s*$/,"$1"); }

	this.get_fieldreq=function(fieldno,value){
		var fieldreqs=new Array();
		fieldreqs[2]="://"+(value!=""?value+"@":"");
		fieldreqs[4]=(value!="" && parseInt(value)!=80?":"+parseInt(value):"");
		fieldreqs[7]=(value!=""?"?"+value:"");
		fieldreqs[8]=(value!=""?"#"+value:"");
		if(fieldreqs[fieldno]!=undefined) return value;
		// return (value!=""?null:value);
		else return fieldreqs[fieldno];
	}

	this.set_proto=function(proto){
		if(proto==undefined) proto="http";
		if(this.locked) return;
		this.proto=proto;
	}
	this.get_proto=function(){ return this.proto; }

	this.get_userpass=function(){ return this.userpass; }
	this.set_userpass=function(userpass){
		if(userpass==undefined) userpass="";
		this.userpass=userpass;
	}
	this.get_servername=function(){ return this.servername; }
	this.set_servername=function(servername){
		if(servername==undefined) servername="";
		this.servername=servername;
	}
	this.get_portval=function(){
		return (
			this.portval==""?
			(this.get_proto()=="https"?"443":"80"):
			this.portval
		);
	}
	this.set_portval=function(port){
		if(port==undefined) port="";
		this.portval=((parseInt(port)!=80)?port:"").toString();
	}
	this.get_path=function(){ // ***
		if(this.path.indexOf("/../")!=-1)
			this.path=this.path.replace(/(?:\/[^\/]+){0,1}\/\.\.\//g,"/");
		if(this.path.indexOf("/./")!=-1)
			while((path=this.path.replace("/./","/")) && path!=this.path)
				this.path=path;
		return this.path;
	}
	this.set_path=function(path){
		if(path==undefined) path="/"; this.path=path;
	}
	this.get_file=function(){ return this.file; }
	this.set_file=function(file){ if(file==undefined) file=""; this.file=file; }
	this.get_query=function(){ return this.query; }
	this.set_query=function(query){
		if(query==undefined) query="";
		this.query=query;
	}
	this.get_label=function(){ return this.label; }
	this.set_label=function(label){
		if(label==undefined) label="";
		this.label=label;
	}

	this.get_url=function(){
		if(this.locked) return this.url;
		return this.get_proto()+"://"+
			(this.get_userpass()==""?"":this.get_userpass()+"@")+
			this.get_servername()+
			(
				this.get_portval()==undefined || this.get_portval()?
				"":
				":"+parseInt(this.get_portval())
			)+
			this.get_path()+this.get_file()+
			(this.get_query()==""?"":"?"+this.get_query())+
			(this.get_label()==""?"":"#"+this.get_label());
	}

	this.surrogafy=function(){
		var url=this.get_url();
		if(
			this.locked ||
			this.get_proto()+
			this.get_fieldreq(2,this.get_userpass())+
			this.get_servername()+
			this.get_path()+
			this.get_file()
			 ==
			this.THIS_SCRIPT
		) return url;
		var label=this.get_label();
		this.set_label();
		if(this.ENCRYPT_URLS && !this.locked)
			url=<?php echo(COOK_PREF); ?>.pe.proxenc(url);
		// urlencode()d
		//url=this.THIS_SCRIPT+"?="+(!this.ENCRYPT_URLS?escape(url):url);
		url=this.THIS_SCRIPT+"?="+url;
		this.set_label(label);
		return url;
	}

	if(url.length><?php echo($CONFIG['MAXIMUM_URL_LENGTH'])?>)
		this.url="";
	else{
		// parse like PHP does for &#num; HTML entities? // TODO?
		//this.url=preg_replace("/&#([0-9]+);/e","chr(\\1)");
		this.url=this.trim(url.
			replace("&amp;","&").
			replace("\r","").
			replace("\n","")
		);
	}

	this.topurl=topurl;
	this.locked=this.url.match(<?php echo(AURL_LOCK_REGEXP); ?>);
	this.locked=(this.locked==null?false:true);

	if(!this.locked){
		var urlwasvalid=true;
		if(!this.url.match(this.URLREG)){
			urlwasvalid=false;
			if(this.topurl==undefined)
				this.url=
					"http://"+
					(
						this.url.charAt(0)==":" || this.url.charAt(0)=="/"?
						this.url.substring(1):
						this.url
					)+
					(this.url.indexOf("/")!=-1?"":"/");
			else{
				var newurl=
					this.topurl.get_proto()+
					"://"+
					this.get_fieldreq(2,this.topurl.get_userpass())+
					this.topurl.get_servername()+
					(
						this.topurl.get_portval()!=80 && (
							this.topurl.get_proto()=="https"?
							this.topurl.get_portval()!=443:true
						)?
						":"+this.topurl.get_portval():
						""
					);
				if(this.url.substring(0,1)!="/") newurl+=this.topurl.get_path();
				this.url=newurl+this.url;
			}
		}

		this.set_proto(
			(
				urlwasvalid || this.topurl==undefined?
				this.url.replace(/^([^:]+).*$/,"\$1"):
				this.topurl.get_proto()
			)
		);
		this.set_userpass(this.url.replace(this.URLREG,"\$2"));
		this.set_servername(this.url.replace(this.URLREG,"\$3"));
		this.set_portval(this.url.replace(this.URLREG,"\$4"));
		this.set_path(this.url.replace(this.URLREG,"\$5"));
		this.set_file(this.url.replace(this.URLREG,"\$6"));
		this.set_query(this.url.replace(this.URLREG,"\$7"));
		this.set_label(this.url.replace(this.URLREG,"\$8"));
	}

	//if(!this.locked && !this.url.match(this.URLREG)) havok(7,this.url); //*
},

surrogafy_url:function(url,topurl,addproxy){
	url=url.toString();
	if(!url.substring) return;
	if(addproxy==undefined) addproxy=true;
	var urlquote="";
	if(
		(url.substring(0,1)=="\"" || url.substring(0,1)=="'") &&
		url.substring(0,1)==url.substring(url.length-1,url.length)
	){
		urlquote=url.substring(0,1);
		url=url.substring(1,url.length-1);
	}
	if(topurl==undefined) topurl=this.curr_urlobj;
	var urlobj=new this.aurl(url,topurl);
	var new_url=(addproxy?urlobj.surrogafy():urlobj.get_url());
	if(urlquote!="") new_url=urlquote+new_url+urlquote;
	return new_url;
},

surrogafy_url_toobj:function(url,topurl,addproxy){
	url=url.toString();
	if(!url.substring) return;
	if(addproxy==undefined) addproxy=true;
	if(
		(url.substring(0,1)=="\"" || url.substring(0,1)=="'") &&
		url.substring(0,1)==url.substring(url.length-1,url.length)
	) url=url.substring(1,url.length-1);
	if(topurl==undefined) topurl=this.curr_urlobj;
	return new this.aurl(url,topurl);
},

de_surrogafy_url:function(url){
	if(url==undefined) return "";
	url=url.toString();
	if(
		url.match(<?php echo(FRAME_LOCK_REGEXP); ?>) ||
		!url.match(<?php echo(AURL_LOCK_REGEXP); ?>)
	) return url;
	// urldecode()
	return this.pe.proxdec(decodeURIComponent(
		url.substring(url.indexOf('?')+1).replace(
			<?php echo(PAGETYPE_REGEXP); ?>,"\$2")));
},

add_querystuff:function(url,querystuff){
	var pos=url.indexOf('?');
	return url.substr(0,pos+1)+querystuff+url.substr(pos+1,url.length-pos);
},

preg_match_all:function(regexpstr,string){
	var matcharr=new Array();
	var regexp=new RegExp(regexpstr);
	var result;
	while(true){
		result=regexp.exec(string);
		if(result!=null) matcharr.push(result);
		else break;
	}
	return matcharr;
},

framify_url:function(url,frame_type){
	if(frame_type===<?php echo(PAGETYPE_NULL); ?>)
		return url;
	var urlquote="";
	if(
		(url.substring(0,1)=="\"" || url.substring(0,1)=="'") &&
		url.substring(0,1)==url.substring(url.length-1,url.length)
	){
		urlquote=url.substring(0,1);
		url=url.substring(1,url.length-1);
	}
	if(!url.match(<?php echo(FRAME_LOCK_REGEXP); ?>)){
		var query;
		if(frame_type===<?php echo(PAGETYPE_FRAME_TOP); ?>)
			query='&=';
		else if(frame_type===<?php echo(PAGETYPE_FRAMED_CHILD); ?>) query='.&=';
		else if(
			frame_type===<?php echo(PAGETYPE_FRAMED_PAGE); ?> ||
			this.PAGE_FRAMED
		) query='_&=';
		else query='';
		url=url.replace(
			/^([^\?]*)[\?]?<?php echo(PAGETYPE_MINIREGEXP); ?>([^#]*?[#]?.*?)$/,
			'\$1?='+query+'\$3');
	}
	if(urlquote!="") url=urlquote+url+urlquote;
	return url;
},

parse_html:function(regexp,partoparse,html,addproxy,framify){
	var match,begin,end,nurl;
	if(html.match(regexp)){
		var matcharr=this.preg_match_all(regexp,html);
		var newhtml="";
		for(var key in matcharr){
			/*match=matcharr[i];
			nurl=this.surrogafy_url(match[partoparse],undefined,addproxy);
			nhtml=match[0].replace(match[partoparse],nurl);
			html=html.replace(match[0],nhtml);*/
			match=matcharr[key];
			if(match[partoparse]!=undefined){
				begin=html.indexOf(match[partoparse]);
				end=begin+match[partoparse].length;
				nurl=this.surrogafy_url(match[partoparse],undefined,addproxy);
				if(framify) nurl=this.framify_url(nurl,framify);
				newhtml+=html.substring(0,begin)+nurl;
				html=html.substring(end);
			}
		}
		html=newhtml+html;
	}
	return html;
},

parse_all:function(){
	if(arguments[0]==null) return;
	var html=arguments[0].toString();
	var key;
	for(var key in regexp_arrays){
		if((arguments.length>1 && key!=arguments[1]) || key=='text/javascript')
			continue;
		arr=regexp_arrays[key];
		for(var regexp_arraykey in arr){
			regexp_array=arr[regexp_arraykey];
			if(regexp_array[0]==undefined) continue;
			if(regexp_array[0]==1)
				html=html.replace(regexp_array[1],regexp_array[2]);
			else if(regexp_array[0]==2){
				addproxy=(regexp_array.length>3?regexp_array[3]:true);
				framify=(regexp_array.length>4?regexp_array[4]:false);
				html=this.parse_html(
					regexp_array[1],regexp_array[2],html,addproxy,framify);
			}
		}
	}
	return html;
},

form_button:null,
form_encrypt:function(form){
	if(form.method=='post') return true;
	//action=form.<php echo(COOK_PREF); ?>.value;
	var action=form.getElementsByName(this.COOK_PREF)[0].value;
	for(var i=1;i<form.elements.length;i++){
		if(
			form.elements[i].disabled || form.elements[i].name=='' ||
			form.elements[i].value=='' || form.elements[i].type=='reset'
		) continue;
		if(form.elements[i].type=='submit'){
			if(form.elements[i].name!=this.form_button) continue;
			this.form_button=null;
		}
		var pref;
		if(!action.match(/\?/)) pref="?";
		else pref="&";
		action+=pref+form.elements[i].name+"="+form.elements[i].value;
	}
	location.href=this.surrogafy_url(action);
	return false;
},

setAttr:function(obj,attr,val){
	if(obj==undefined || val==undefined)
		return undefined;

	if(typeof(attr)==typeof(/ /)){
		attr=attr.toString();
		attr=attr.substr(1,attr.length-2);
	}

	if(attr=="innerHTML"){
		obj[attr]=this.parse_all(val);
		return obj[attr];
	}

	if(val=="bottom" || val=="right"){
		return obj[attr];
	}

	if(obj==document && attr=="cookie"){
		var COOK_REG=/^([^=]*)=([^;]*)(?:;[\s\S]*?)?$/i;
		var realhost=
			this.LOCATION_HOSTNAME.replace("/^www/i","").replace(".","_");
		var cookkey=val.replace(COOK_REG,"\$1");
		var cookval=val.replace(COOK_REG,"\$2");
		if(this.ENCRYPT_COOKIES){
			cookkey=proxenc(cookkey);
			cookval=proxenc(cookval);
		}
		var newcookie=
			realhost+"<?php echo(COOKIE_SEPARATOR); ?>"+
			cookkey+"="+cookval+"; ";
		document.cookie=newcookie;
		return newcookie;
	}

	if(obj==location && attr=="hostname") return this.LOCATION_HOSTNAME;

	if(obj==location && attr=="search"){
		if(val.substr(0,1)=="?") val=val.substr(1);
		this.curr_urlobj.set_query(val);
		val=this.curr_urlobj.get_url();
		attr="href";
	}

	var is_parse_attr=false;
	for(var parse_attr in this.parse_attrs){
		if(attr==this.parse_attrs[parse_attr]){
			is_parse_attr=true;
			break;
		}
	}

	var proxval=val;
	if(is_parse_attr){
		proxval=this.surrogafy_url(val);

		// tags framified must match REGEXPS with regexp_array[5]
		if(obj.tagName=="A" || obj.tagName=="AREA")
			proxval=this.framify_url(
				proxval,<?php echo(COOK_PREF); ?>.NEW_PAGETYPE_FRAME_TOP);
		else if(obj.tagName=="FRAME" || obj.tagName=="IFRAME")
			proxval=this.framify_url(
				proxval,<?php echo(PAGETYPE_FRAMED_CHILD); ?>);
	}

	if(this.URL_FORM){
		if((obj==location && attr=="href") || attr=="location"){
			urlobj=this.surrogafy_url_toobj(val);
			if(!urlobj.locked) proxval=this.add_querystuff(proxval,"=&");
			ret=this.thetop.location.href=proxval;
		}
		else ret=this.doSet(obj,attr,proxval);
	}
	else ret=this.doSet(obj,attr,proxval);

	return ret;
},

doSet:function(obj,attr,val){
	if(typeof(val)!="function" && typeof(val)!="object"){
		if(isNaN(val) || typeof(val)==typeof(""))
			val="\""+this.doEscape(val)+"\"";
		obj[attr]=eval(val);
	}
	else obj[attr]=val;

	return obj[attr];
},

doEscape:function(val){
	if(val!==undefined){
		val=val.replace(/\\/g,"\\\\",val);
		val=val.replace(/\n/g,"\\n",val);
		val=val.replace(/\"/g,"\\\"",val);
	}
	return val;
},

getAttr:function(obj,attr){
	if(this.log == undefined) // DEBUG
		this.log = 0; // DEBUG

	if(obj===undefined)
		return undefined;

	if(typeof(attr)==typeof(/ /)){
		attr=attr.toString();
		attr=attr.substr(1,attr.length-2);
	}

	if(obj==window && attr=="top"){
		return window.self;
	}

	if(obj==document && attr=="cookie"){
		var ocookies=this.getCookieArr();
		var cookies="",ocook;
		var COOK_REG=
			/^([\s\S]*)<?php echo(COOKIE_SEPARATOR); ?>([^=]*)=([\s\S]*)(?:; )?$/ig;
		for(var key in ocookies){
			ocook=ocookies[key];
			if(typeof(ocook)!=typeof("")) continue;
			if(ocook.match(COOK_REG)==null) continue;
			var realhost=
				this.LOCATION_HOSTNAME.replace("/^www/ig","").replace(".","_");
			var cookhost=ocook.replace(COOK_REG,"\$1");
			if(cookhost==realhost){
				if(this.ENCRYPT_COOKIES){
					var cookkey=this.pe.proxdec(ocook.replace(COOK_REG,"\$2"));
					var cookval=this.pe.proxdec(ocook.replace(COOK_REG,"\$3"));
					cookies+=cookkey+"="+cookval+"; ";
				}
				else cookies+=ocook.replace(COOK_REG,"\$2=\$3; ");
			}
		}
		return cookies;
	}

	if(obj==navigator){
		if(this.USERAGENT=="-1" && (attr!="plugins" && attr!="mimeType"))
			return undefined;
		if(this.USERAGENT=="") return obj[attr];
		var msie=this.USERAGENT.match(/msie/ig);
		var UA_REG=
			/^([^\/\(]*)\/?([^ \(]*)[ ]*(\(?([^;\)]*);?([^;\)]*);?([^;\)]*);?([^;\)]*);?([^;\)]*);?[^\)]*\)?)[ ]*([^ \/]*)\/?([^ \/]*).*$/ig;
		switch(attr){
			case "appName":
				var tempappname=(
					msie?
					"Microsoft Internet Explorer":
					this.USERAGENT.replace(UA_REG,"\$1")
				);
				if(tempappname=="Opera" || tempappname=="Mozilla")
					tempappname="Netscape";
				return tempappname;
			case "appCodeName": return this.USERAGENT.replace(UA_REG,"\$1");
			case "appVersion":
				return (
					msie?
					this.USERAGENT.replace(UA_REG,"\$2 \$3"):
					this.USERAGENT.replace(UA_REG,"\$2 (\$4; \$7)")
				);
			case "language":
				return (msie?undefined:this.USERAGENT.replace(UA_REG,"\$7"));
			case "mimeType": return navigator.mimeType;
			case "oscpu":
				return (msie?undefined:this.USERAGENT.replace(UA_REG,"\$6"));
			case "platform":
				var tempplatform=this.USERAGENT.replace(UA_REG,"\$4");
				return (
					tempplatform=="compatible" || tempplatform=="Windows"?
					"Win32":
					this.USERAGENT.replace(UA_REG,"\$6")
				);
			case "plugins":
				return (
					!<?php echo(COOK_PREF); ?>.REMOVE_OBJECTS?
					navigator.plugins:
					undefined
				);
			case "product":
				return (msie?undefined:this.USERAGENT.replace(UA_REG,"\$9"));
			case "productSub":
				return (msie?undefined:this.USERAGENT.replace(UA_REG,"\$10"));
			case "userAgent": return this.USERAGENT;
			default: return undefined;
		}
	}

	var val;
	if(obj==location && attr=="search") val=location.href;
	else val=obj[attr];

	var is_parse_attr=false;
	for(var parse_attr in this.parse_attrs){
		if(attr==this.parse_attrs[parse_attr]){
			is_parse_attr=true;
			break;
		}
	}

	if(is_parse_attr)
		val=this.de_surrogafy_url(val);

	if(obj==location && attr=="search") val=val.replace(/^[^?]*/g,"");
	if(obj==document && attr=="domain") val=this.aurl.get_servername();
	return val;
},

eventify:function(a1,a2){
	document.getElementsByTagName("head")[0].addEventListener("load",function(){
		<?php echo(COOK_PREF); ?>.setParentStuff(a1,a2);
	},false);
	window.addEventListener("load",function(){
		<?php echo(COOK_PREF); ?>.setParentStuff(a1,a2);
	},false);
	this.setParentURL(this.CURR_URL);
},

setParentURL:function(url){
	if(
		this.thetop!=null && this.thetop!=window && this.thetop.document!=null
		&& this.thetop.document.getElementById('url')!=null
	){
		this.thetop.document.getElementById('url').value=url;
		this.thetop.document.getElementById('proxy_link').href=
			this.add_querystuff(this.surrogafy_url(url),"=-&");
	}
},

// amazing creativity with the name on my part
setParentStuff:function(proto,server){
	var topdoc=this.thetop.document;
	topdoc.title=document.title;

	// find and set shortcut icon
	var tophead=topdoc.getElementsByTagName("head")[0];
	var links=tophead.getElementsByTagName("link");
	var link=null;
	for(var i=0; i<links.length; i++){
		if(links[i].type=="image/x-icon" && links[i].rel=="shortcut icon")
			link=links[i];
	}

	if(tophead.getElementsByTagName("link").length>0)
		tophead.removeChild(topdoc.getElementsByTagName("link")[0]);

	var favicon=topdoc.createElement("link");
	favicon.type="image/x-icon";
	favicon.rel="shortcut icon";
	favicon.href=(
		link==null?
		this.surrogafy_url(proto+"://"+server+"/favicon.ico"):
		link.href
	);
	tophead.appendChild(favicon);
},

XMLHttpRequest_wrap:function(xmlhttpobj){
	xmlhttpobj.<?php echo(COOK_PREF); ?>_open=xmlhttpobj.open;
	xmlhttpobj.open=<?php echo(COOK_PREF); ?>.XMLHttpRequest_open;
	return xmlhttpobj;
},

XMLHttpRequest_open:function(){
	if(arguments.length<2) return;
	arguments[1]=<?php echo(COOK_PREF); ?>.surrogafy_url(arguments[1]);
	return this.<?php echo(COOK_PREF); ?>_open.apply(this,arguments);
},

// WRAPPED FUNCTIONS AND OBJECTS
thetop:top,
theparent:parent,
setTimeout:window.setTimeout,
setInterval:window.setInterval,
document_write_queue:"",
purge:function(){
	thehtml=this.document_write_queue;
	if(thehtml=="") return;
	thehtml=this.parse_all(thehtml);
	this.document_write_queue="";
	document.write_<?php echo(COOK_PREF); ?>(thehtml);
},

purge_noparse:function(){
	thehtml=this.document_write_queue;
	if(thehtml=="") return;
	this.document_write_queue="";
	document.write_<?php echo(COOK_PREF); ?>(thehtml);
}

}

<?php } ?>

// }}}

// JAVASCRIPT FUNCS: WRAPPING/HOOKING {{{

<?php if($do_wrap){ ?>

document.write_<?php echo(COOK_PREF); ?>=document.write;
document.writeln_<?php echo(COOK_PREF); ?>=document.writeln;
document.write=function(html){
	<?php echo(COOK_PREF); ?>.document_write_queue+=html;
}
document.writeln=function(html){
	<?php echo(COOK_PREF); ?>.document_write_queue+=html+"\n";
}

window.open_<?php echo(COOK_PREF); ?>=window.open;
window.open=document.open=function(){
	if(arguments.length<1) return;
	var url=<?php echo(COOK_PREF); ?>.surrogafy_url(arguments[0]);
	if(
		(url.substring(0,1)=="\"" || url.substring(0,1)=="'") &&
		url.substring(0,1)==url.substring(url.length-1,url.length)
	) url=url.substring(1,url.length-1);
	arguments[0]=url;
	return window.open_<?php echo(COOK_PREF); ?>.apply(this.caller,arguments);
}

setTimeout=function(){
	if(arguments.length<1) return;
	if(typeof(arguments[0])==typeof("")){
		arguments[0]=<?php echo(COOK_PREF); ?>.parse_all(
			arguments[0],"application/x-javascript");
	}
	return <?php echo(COOK_PREF); ?>.setTimeout.apply(this,arguments);
}

setInterval=function(){
	if(arguments.length<1) return;
	if(typeof(arguments[0])==typeof("")){
		arguments[0]=<?php echo(COOK_PREF); ?>.parse_all(
			arguments[0],"application/x-javascript");
	}
	return <?php echo(COOK_PREF); ?>.setInterval.apply(this,arguments);
}

/* hooking for eval(), not necessary anymore, but worked relatively well in the
 * past
/*eval_<?php echo(COOK_PREF); ?>=eval;
eval=function(){
	if(arguments.length<1) return;
	arguments[0]=<?php echo(COOK_PREF); ?>.parse_all(
		arguments[0],"application/x-javascript");
	return eval_<?php echo(COOK_PREF); ?>.apply(this.caller,arguments);
}*/

// wrap top and parent objects for anti-frame breaking
if(<?php echo(COOK_PREF); ?>.PAGE_FRAMED){
	if(parent==top) parent=self;
	if(window.parent==top) window.parent=self;
	if(top!=self) top=<?php echo(COOK_PREF); ?>.thetop.frames[0];
	if(window.top!=self) window.top=<?php echo(COOK_PREF); ?>.thetop.frames[0];
}

<?php } ?>

// }}}

//</script><?php exit(); }

# }}}
