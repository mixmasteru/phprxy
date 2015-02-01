<?php
/**
 * PAGE PARSING: PARSING FUNCTIONS
 *
 * @author Ulrich Pech
 * @link https://github.com/mixmasteru
 *
 * @copyright Surrogafier, Author: Brad Cable, Email: brad@bcable.net
 * @license BSD
 */
class pageparser
{
	/**
	 * global config
	 * @var array
	 */
	protected $arr_config;
	
	/**
	 * global options
	 * @var array
	 */
	protected $arr_option;
	
	/**
	 * current aurl object
	 * @var aurl
	 */
	protected $obj_aurl;
	
	/**
	 * 
	 * @var urlparser
	 */
	protected $obj_urlparser;
	
	/**
	 * 
	 * @var array
	 */
	protected $arr_regexp;
	
	/**
	 * 
	 * @param array $arr_config
	 * @param array $arr_option
	 * @param array $arr_regexp
	 * @param aurl $obj_aurl
	 * @param urlparser $obj_urlparser
	 */
	public function __construct(array $arr_config, array $arr_option, array $arr_regexp, $obj_aurl, $obj_urlparser)
	{
		$this->arr_config 	= $arr_config;
		$this->arr_option 	= $arr_option;
		$this->arr_regexp	= $arr_regexp;
		
		$this->obj_aurl 	= $obj_aurl;
		$this->obj_urlparser= $obj_urlparser;	
	}
	
	/**
	 * 
	 * @param string $regexp
	 * @param string $partoparse
	 * @param string $html
	 * @param boolean $addproxy
	 * @param string $framify
	 * @return string
	 */
	protected function parse_html($regexp,$partoparse,$html,$addproxy,$framify)
	{
		$newhtml = null;
		while(preg_match($regexp,$html,$matcharr,PREG_OFFSET_CAPTURE))
		{
			$nurl=$this->obj_urlparser->surrogafy_url($matcharr[$partoparse][0],$this->obj_aurl,$addproxy);
			if($framify)
			{
				$nurl=$this->obj_urlparser->framify_url($nurl,$framify);
			}
			$begin=$matcharr[$partoparse][1];
			$end=$matcharr[$partoparse][1]+strlen($matcharr[$partoparse][0]);
			$newhtml.=substr_replace($html,$nurl,$begin);
			$html=substr($html,$end,strlen($html)-$end);
		}
		$newhtml.=$html;
		return $newhtml;
	}
	
	/**
	 * 
	 * @param array $arr_regexp
	 * @param unknown $thevar
	 * @return mixed
	 */
	protected function regular_express(array $arr_regexp,$thevar)
	{
		# in benchmarks, this 'optimization' appeared to not do anything at all, or
		# possibly even slow things down
		#$arr_regexp[2].='S';
		if($arr_regexp[0]==1)
		{	
			$newvar	= preg_replace($arr_regexp[2],$arr_regexp[3],$thevar);
		}
		elseif($arr_regexp[0]==2)
		{
			$addproxy	=(isset($arr_regexp[4])?$arr_regexp[4]:true);
			$framify	=(isset($arr_regexp[5])?$arr_regexp[5]:false);
			$newvar		= $this->parse_html($arr_regexp[2],$arr_regexp[3],$thevar,$addproxy,$framify);
		}
		return $newvar;
	}
	
	/**
	 * 
	 * @param string $html
	 * @return string
	 */
	public function parse_all($html)
	{
		if(CONTENT_TYPE != 'text/html')
		{
			return $this->parsenonhtml($html);
		}
	
		#if($this->arr_option['REMOVE_SCRIPTS']) $splitarr=array($html);
		$splitarr = preg_split(
		'/(<!--(?!\[if).*?-->|<style.*?<\/style>|<script.*?<\/script>)/is',
		$html,-1,PREG_SPLIT_DELIM_CAPTURE);
		unset($html);
		$firstrun	= true;
		$firstjsrun	= true;
		
		for(reset($this->arr_regexp);list($key,$arr)=each($this->arr_regexp);)
		{
			if($key=='text/javascript') continue;
	
			// OPTION1: use ONLY if no Javascript REGEXPS affect HTML sections and
			// all HTML modifying Javascript REGEXPS are performed after HTML
			// regexps.  This gives a pretty significant speed boost.  If used,
			// make sure "OPTION2" lines are commented, and other "OPTION1" lines
			// AREN'T.
			if($firstjsrun && (
					$key=='application/javascript' ||
					$key=='application/x-javascript'
			)){
				if($this->arr_option['REMOVE_SCRIPTS']) break;
				$splitarr2=array();
				for($i=0;$i<count($splitarr);$i+=2){
					$splitarr2[$i]=preg_split(
							'/'.REGEXP_SCRIPT_ONEVENT.'/is',$splitarr[$i],-1,
							PREG_SPLIT_DELIM_CAPTURE);
				}
			}
			// END OPTION1
	
			# firstrun remove scripts: on<event>s and noscript tags; also remove
			# objects
			if(
			$firstrun &&
			($this->arr_option['REMOVE_SCRIPTS'] || $this->arr_option['REMOVE_OBJECTS'])
			){
				for($i=0;$i<count($splitarr);$i+=2){
					if($this->arr_option['REMOVE_SCRIPTS'])
						$splitarr[$i]=preg_replace(
								'/(?:'.REGEXP_SCRIPT_ONEVENT.'|<.?noscript>)/is',null,
								$splitarr[$i]);
					if($this->arr_option['REMOVE_OBJECTS'])
						$splitarr[$i]=preg_replace(
								'/<(embed|object).*?<\/\1>/is',null,$splitarr[$i]);
				}
			}
	
			foreach($arr as $regexp_array)
			{
				if($regexp_array==null) continue;
				for($i=0;$i<count($splitarr);$i++)
				{
	
					# parse scripts for on<event>s
					// OPTION1
					if($i%2==0 && isset($splitarr2) && $regexp_array[1]==2){
	
						// OPTION2
						//if($regexp_array[1]==2 && $i%2==0){
						//$splitarr2[$i]=preg_split(
						//	'/( on[a-z]{3,20}=(?:"(?:[^"]+)"|\'(?:[^\']+)\'|'.
						//	'[^"\' >][^ >]+[^"\' >]))/is',$splitarr[$i],-1,
						//	PREG_SPLIT_DELIM_CAPTURE);
						// END OPTION2
	
						// UNRELATED TO OPTIONS
						//if(count($splitarr2[$i])<2)
						//	$splitarr[$i]=$this->regular_express(
						//		$regexp_array,$splitarr[$i]);
						if(count($splitarr2[$i])>1)
						{
							for($j=1;$j<count($splitarr2[$i]);$j+=2)
							{
								$begin = preg_replace('/^([^=]+=.).*$/i','\1',$splitarr2[$i][$j]);
								$quote = substr($begin,-1);
								if($quote!='"' && $quote!='\'')
								{
									$quote=null;
									$begin=substr($begin,0,-1);
								}
								$code = preg_replace('/^[^=]+='.($quote==null?'(.*)$/i':'.(.*).$/i'),'\1',$splitarr2[$i][$j]);
								if(substr($code,0,11)=='javascript:')
								{
									$begin.='javascript:';
									$code=substr($code,11);
								}
								if($firstjsrun) $code=";{$code};";
								$splitarr2[$i][$j]=
								$begin.$this->regular_express($regexp_array,$code).
								$quote;
							}
							// OPTION2
							//$splitarr[$i]=implode(null,$splitarr2[$i]);
						}
					}
	
					# remove scripts
					elseif(
					$firstrun &&
					$this->arr_option['REMOVE_SCRIPTS'] &&
					strtolower(substr($splitarr[$i],0,7))=='<script'
							) $splitarr[$i]=null;
	
							# parse valid HTML in HTML section
							elseif($i%2==0 && $regexp_array[1]==1)
							$splitarr[$i]=$this->regular_express($regexp_array,$splitarr[$i]);
	
							# parse valid other things
							elseif(
							(
								# HTML key but not in HTML section
								$regexp_array[1]==1 ||
	
								( # javascript section
								$regexp_array[1]==2 &&
								strtolower(substr($splitarr[$i],0,7))=='<script'
									) ||
	
									( # CSS section
									$key=='text/css' &&
									strtolower(substr($splitarr[$i],0,6))=='<style'
											)
	
							) && # not in comment
							substr($splitarr[$i],0,4)!="<!--"
									){									
										# DE-STROY!
										$pos=strpos($splitarr[$i],'>');
										$l_html=substr($splitarr[$i],0,$pos+1);
										$l_body=substr($splitarr[$i],$pos+1);
										# HTML parses just HTML
										if($key=='text/html')
										{
											$l_html=$this->regular_express($regexp_array,$l_html);
	
											# javascript, CSS, and such parses just their own
										}	
										else
										{										
											$l_body=$this->regular_express($regexp_array,$l_body);
											# put humpty-dumpty together again
											$splitarr[$i]=$l_html.$l_body;
										}
									}
	
									# script purge cleanup
									if(
									$firstrun &&
					!$this->arr_option['REMOVE_SCRIPTS'] &&
						strtolower(substr($splitarr[$i],-9))=='</script>' &&
						!preg_match('/^[^>]*src/i',$splitarr[$i])
									){
									$splitarr[$i]=
									preg_replace('/'.END_OF_SCRIPT_TAG.'$/i',
											';'.COOK_PREF.'.purge();//--></script>',
											$splitarr[$i]);
				}
		
				}
		
				$firstrun=false;
				if($firstjsrun && (
					$key=='application/javascript' ||
					$key=='application/x-javascript'
				))
				$firstjsrun=false;
			}
		}
	
		// OPTION1
		if(!$this->arr_option['REMOVE_SCRIPTS'])
		{
			for($i=0;$i<count($splitarr);$i+=2)
			{
				$splitarr[$i]=implode(null,$splitarr2[$i]);
			}
		}
		// END OPTION1
	
		return implode(null,$splitarr);
	}
	
	/**
	 * 
	 * @param string $html
	 * @return string
	 */
	protected function parsenonhtml($html)
	{
		for(reset($this->arr_regexp);list($key,$arr)=each($this->arr_regexp);)
		{
			if($key==CONTENT_TYPE)
			{
				foreach($arr as $regarr)
				{
					if($regarr==null) continue;
					$html=$this->regular_express($regarr,$html);
				}
			}
		}
		return $html;
	}
}
