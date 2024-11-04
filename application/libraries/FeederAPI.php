<?php
class FeederAPI
{
	private $token;
	private $apiURI;
	
	public function __construct($a_config = false)
	{
		$this->feederU = '';
		$this->feederP = '';
		
		if($a_config['mode'] == 'production'){
			$this->apiURI = '';
		}
		else{
			$this->apiURI = '';
		}
		
		$this->init();
	}
	
	private function init()
	{
		$tokenData = array(
			'username' => $this->feederU,
			'password' => $this->feederP
		);
		$tokenResult = $this->post('GetToken', $tokenData);
		$this->token = $tokenResult->data->token;
	}
	
	public function post($function, $args = false)
	{
		if($function == 'GetToken'){
			$postBody = array(
				'act' => 'GetToken'
			);
			
			$postBody = array_merge($postBody, $args);
		}
		else{
			$postBody = array(
				'act' => $function,
				'token' => $this->token
			);
			if($args){
				foreach($args as $key => $value){
					$postBody[$key] = $value;
				}
			}
		}
		
		$ch = curl_init();
		// curl_setopt_array($ch, [
		// 	CURLOPT_URL => $this->apiURI,
		// 	CURLOPT_RETURNTRANSFER => true,
		// 	CURLOPT_ENCODING => '',
		// 	// CURLOPT_MAXREDIRS => 10,
		// 	// CURLOPT_TIMEOUT => 0,
		// 	// CURLOPT_FOLLOWLOCATION => true,
		// 	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		// 	CURLOPT_CUSTOMREQUEST => 'POST',
		// 	CURLOPT_POSTFIELDS => json_encode($postBody)
		// ]);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json'
		));
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postBody));
        curl_setopt($ch, CURLOPT_URL, $this->apiURI);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = json_decode(curl_exec($ch));
		curl_close($ch);
		$result->post_data = $postBody;
		
		return $result;
	}
}