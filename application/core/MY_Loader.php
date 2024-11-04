<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Loader class */
require APPPATH."third_party/MX/Loader.php";

class MY_Loader extends MX_Loader {
	function __construct()
	{
		parent::__construct();
	}
	
	protected function _ci_object_to_array($object){
		return is_object($object) ? get_object_vars($object) : $object;
	}
}