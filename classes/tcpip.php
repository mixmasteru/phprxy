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

# PROXY FUNCTIONS: TCP/IP {{{
class tcpip
{
	/**
	 * global config
	 * @var array
	 */
	protected $arr_config;
	
	/**
	 * 
	 * @param array $arr_config
	 */
	public function __construct(array $arr_config)
	{
		$this->arr_config = $arr_config;
	}
	
	/**
	 * 
	 * @param string $ipaddr
	 * @return string
	 */
	protected function ipbitter($ipaddr)
	{
		$ipsplit=explode('.',$ipaddr);
		for($i=0;$i<4;$i++){
			$ipsplit[$i]=decbin($ipsplit[$i]);
			$ipsplit[$i]=str_repeat('0',8-strlen($ipsplit[$i])).$ipsplit[$i];
		}
		return implode(null,$ipsplit);
	}
	
	/**
	 * 
	 * @param string $iprange
	 * @param string $ip
	 * @return boolean
	 */
	protected function ipcompare($iprange,$ip)
	{
		$iprarr=explode('/',$iprange);
		$ipaddr=$iprarr[0];
		$mask=$iprarr[1];
		$maskbits=str_repeat('1',$mask).str_repeat('0',$mask);
		$ipbits= $this->ipbitter($ipaddr);
		$ipbits2= $this->ipbitter($ip);
		return (($ipbits & $maskbits)==($ipbits2 & $maskbits));
	}
	
	/**
	 * 
	 * @param string $ip
	 * @param string $mask
	 * @return number
	 */
	protected function ip_check($ip,$mask=false){
		$ipseg='(?:[01]?[0-9]{1,2}|2(?:5[0-5]|[0-4][0-9]))';
		return preg_match("/^(?:$ipseg\.){0,3}$ipseg".($mask?'\/[0-9]{1,2}':null).
				'$/i',$ip); #*
	}
	
	/**
	 * 
	 * @param string $address
	 * @return string
	 */
	protected function gethostbyname_cacheit($address){
		global $dns_cache_array;
		$ipaddr=gethostbyname($address);
		$dns_cache_array[$address]=array('time'=>time(), 'ipaddr'=>$ipaddr);
		return $ipaddr;
	}
	
	/**
	 * 
	 * @param string $address
	 * @return string
	 */
	protected function gethostbyname_cached($address){
		global $dns_cache_array;
		if(isset($dns_cache_array[$address]))
			return $dns_cache_array[$address]['ipaddr'];
		return $this->gethostbyname_cacheit($address);
	}
	
	/**
	 * 
	 * @param string $address
	 * @return string
	 */
	public function get_check($address)
	{
		if(strrchr($address,'/'))
		{
			$address=substr(strrchr($address,'/'),1);
		}
		$ipc = $this->ip_check($address);
		
		$addressip=($this->ip_check($address)?$address:$this->gethostbyname_cached($address));
		
		if(!$this->ip_check($addressip)) havok(1,$address,$addressip);
		
		foreach($this->arr_config['BLOCKED_ADDRESSES'] as $badd)
		{
			if(!$ipc)
			{
				if(strlen($badd)<=strlen($address) && 
					substr($address,strlen($address)-strlen($badd),strlen($badd))==$badd
				)
				{
					 havok(5);
				}
			}
			if($badd==$addressip)
			{
				havok(2,$address,$addressip);
			}
			elseif($this->ip_check($badd,true))
			{
				if($this->ipcompare($badd,$addressip)) havok(2,$address,$addressip);
			}
			else
			{
				$baddip=$this->gethostbyname_cached($badd);
				if(empty($baddip)) havok(4);
				if($baddip==$addressip) havok(2,$address,$addressip);
			}
		}
		return $addressip;
	}
}
# }}}
