<?php
/**
 * class for URL
 *
 * @author Ulrich Pech
 * @link https://github.com/mixmasteru
 *
 * @copyright Surrogafier, Author: Brad Cable, Email: brad@bcable.net
 * @license BSD
 */
class aurl
{
	var $url,$topurl,$locked,$force_unlocked;
	var $proto,$userpass,$servername,$portval,$path,$file,$query,$label;
	
	/**
	 * 
	 * @param string $url
	 * @param string $topurl
	 * @param string $force_unlocked
	 * @return void|string
	 */
	function aurl($url,$topurl=null,$force_unlocked=false)
	{
		global $CONFIG;
		if(strlen($url)>$CONFIG['MAXIMUM_URL_LENGTH'])
		{
			$this->url=null;
		}
		else
		{
			$url = trim(str_replace('&amp;','&',str_replace(chr(13),null,str_replace(chr(10),null,$url))));
			$this->url=preg_replace_callback('/&#([0-9]+);/',function ($matches) { return chr($matches[1]);},$url);
		}
		$this->topurl=$topurl;

		$this->force_unlocked=$force_unlocked;
		$this->determine_locked();
		if($this->locked) return;

		$urlwasvalid=true;
		if(!preg_match(URLREG,$this->url)){
			$urlwasvalid=false;
			if($this->topurl==null) $this->url=
			'http://'.
			(
					$this->url{0}==':' || $this->url{0}=='/'?
					substr($this->url,1):
					$this->url
			).
			(strpos($this->url,'/')!==false?null:'/');
			else{
				$newurl=
				$this->topurl->get_proto().
				$this->get_fieldreq(2,$this->topurl->get_userpass()).
				$this->topurl->get_servername().
				(($this->topurl->get_portval()!=80 && (
						$this->topurl->get_proto()=='https'?
						$this->topurl->get_portval()!=443:
						true
				))?':'.$this->topurl->get_portval():null);
				if($this->url{0}!='/') $newurl.=$this->topurl->get_path();
				$this->url=$newurl.$this->url;
			}
		}

		$this->set_proto((
				$urlwasvalid || $this->topurl==null?
				preg_replace('/^([^:\/]*).*$/','\1',$this->url):
				$this->topurl->get_proto()
		));
		$this->set_userpass(preg_replace(URLREG,'\2',$this->url));
		$this->set_servername(preg_replace(URLREG,'\3',$this->url));
		$this->set_portval(preg_replace(URLREG,'\4',$this->url));
		$this->set_path(preg_replace(URLREG,'\5',$this->url));
		$this->set_file(preg_replace(URLREG,'\6',$this->url));
		$this->set_query(preg_replace(URLREG,'\7',$this->url));
		$this->set_label(preg_replace(URLREG,'\8',$this->url));

		if(!$this->locked && !preg_match(URLREG,$this->url))
			havok(7,$this->url); #*
	}

	function determine_locked(){
		if($this->force_unlocked)
			$this->locked=false;
		else $this->locked=preg_match(AURL_LOCK_REGEXP,$this->url)>0;
	} #*

	function get_fieldreq($fieldno,$value){
		$fieldreqs=array(
				2 => '://'.($value!=null?"$value@":null),
				4 => ($value!=null && intval($value)!=80?':'.intval($value):null),
				7 => ($value!=null?"?$value":null),
				8 => ($value!=null?"#$value":null));
		if(!array_key_exists($fieldno,$fieldreqs))
			return (empty($value)?null:$value);
		else return $fieldreqs[$fieldno];
	}

	function set_proto($proto=''){
		if($this->locked) return;
		$this->proto=(!empty($proto)?$proto:'http');
	}
	function get_proto(){ return $this->proto; }
	function get_userpass(){ return $this->userpass; }
	function set_userpass($userpass=null){ $this->userpass=$userpass; }
	function get_servername(){ return $this->servername; }
	function set_servername($servername=null){ $this->servername=$servername; }
	function get_portval(){
		return (
				empty($this->portval)?
				($this->get_proto()=='https'?'443':'80'):
				$this->portval
		);
	}
	function set_portval($port=null){
		$this->portval=strval((intval($port)!=80)?$port:null);
	}
	function get_path(){
		if(strpos($this->path,'/../')!==false)
			$this->path=
			preg_replace('/(?:\/[^\/]+){0,1}\/\.\.\//','/',$this->path);
		if(strpos($this->path,'/./')!==false)
			while(
					($path=str_replace('/./','/',$this->path)) &&
					$path!=$this->path
			) $this->path=$path;
		return $this->path;
	}
	function set_path($path=null){ $this->path=(empty($path)?'/':$path); }
	function get_file(){ return $this->file; }
	function set_file($file=null){ $this->file=$file; }
	function get_query(){ return $this->query; }
	function set_query($query=null){ $this->query=$query; }
	function get_label(){ return $this->label; }
	function set_label($label=null){ $this->label=$label; }

	function get_url($withlabel=true){
		if($this->locked) return $this->url;
		return
		$this->get_proto().'://'.
		($this->get_userpass()==null?null:$this->get_userpass().'@').
		$this->get_servername().
		(
				(
						$this->get_proto()=='https' &&
						intval($this->get_portval())==443
				) || intval($this->get_portval())==80?
				null:
				':'.intval($this->get_portval())
		).
		$this->get_path().$this->get_file().
		($this->get_query()==null?null:'?'.$this->get_query()).
		(
				$withlabel && $this->get_label()==null?
				null:
				'#'.$this->get_label()
		);
	}

	function surrogafy(){
		global $OPTIONS;
		$label=$this->get_label();
		$this->set_label();
		$url=$this->get_url();
		$this->set_label($label);

		#$this->determine_locked();
		if($this->locked) return $url;

		if($OPTIONS['ENCRYPT_URLS'] && !$this->locked) $url=proxenc($url);
		$url=THIS_SCRIPT."?={$url}".(!empty($label)?"#$label":null);
		return $url;
	}
}

# }}}