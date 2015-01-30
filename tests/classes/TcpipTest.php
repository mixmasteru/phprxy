<?php
class TcpipTest extends PHPUnit_Framework_TestCase
{
    /**
     * 
     * @var tcpip
     */
	protected $obj_sut;
	
	public function setUp()
	{
		parent::setUp();
		$arr_config = array();
		$arr_config['BLOCKED_ADDRESSES']= array('10/8','172/8','192.168/16','127/8','169.254/16');
		$arr_config['BLOCKED_ADDRESSES']= array();
		$this->obj_sut = new \tcpip($arr_config);
	}
	
	public function testGetCheck()
    {
       $this->assertEquals("132.41.112.103", $this->obj_sut->get_check("http://132.41.112.103"));
    }
}
?>