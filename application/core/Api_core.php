<?php
class Api_core extends App_core
{	
	public $a_api_data;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Api_core_model', 'Api');
		
		$s_json = file_get_contents('php://input');
		$o_data = json_decode($s_json, true);
		
		if($s_json != ''){
			$this->check_token($o_data);
		}
	}
	
	public function return_json($a_data)
	{
		header('Content-Type: application/json');
		print json_encode($a_data);
		exit;
	}
	
	public function check_token($o_data)
	{	
		if(isset($o_data['access_token'])){
			$s_access_token = trim($o_data['access_token']);
			$o_api_data = $this->Api->get_api_data_by_access_token($s_access_token);
			
			if(!$o_api_data){
				$this->return_json(array('code' => 998, 'message' => 'Not allowed'));
			}
			else{
				if(is_null($o_api_data->api_secret_token)){
					$parsed_data = $o_data;
				}
				else{
					$parsed_data = $this->libapi->parse_data($o_data['data'], $s_access_token, $o_api_data->api_secret_token);
				}
				$this->a_api_data = $parsed_data;
			}
		}
		else{
			$this->return_json(array('code' => 999, 'Not allowed'));
		}
	}
	
/*
	public function get_content($url, $post = false)
	{
		$header[] = 'Content-Type: application/json';
		$header[] = "Accept-Encoding: gzip, deflate";
		$header[] = "Cache-Control: max-age=0";
		$header[] = "Connection: keep-alive";
		$header[] = "Accept-Language: en-US,en;q=0.8,id;q=0.6";
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		// curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_ENCODING, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
	
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");
	
		if ($post)
		{
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		}
	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		$rs = curl_exec($ch);
		curl_close($ch);
	
		if(empty($rs)){
			return false;
		}
		return $rs;
	}
*/
}