<?php
class LibAPI
{
	const TIME_DIFF_LIMIT = 300; // 5 menit

	public function get_data($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_VERBOSE, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_ENCODING, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);

		$rs = curl_exec($ch);
		curl_close($ch);
	
		if(empty($rs)){
			return false;
		}
		return json_decode($rs);
	}

	public function post_data($url, $post_data)
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
	
		if($post_data){
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		}
	
		$rs = curl_exec($ch);
		curl_close($ch);
	
		if(empty($rs)){
			return false;
		}
		return json_decode($rs);
	}

	public static function hash_data(array $json_data, $cid, $secret) {
		return self::double_encrypt(strrev(time()) . '.' . json_encode($json_data), $cid, $secret);
	}

	public static function parse_data($hased_string, $cid, $secret) {
		$parsed_string = self::double_decrypt($hased_string, $cid, $secret);
		list($timestamp, $data) = array_pad(explode('.', $parsed_string, 2), 2, null);
		// var_dump($hased_string, $parsed_string, $timestamp, $data);
		if (self::ts_diff(strrev($timestamp)) === true) {
			return json_decode($data, true);
		}
		return null;
	}

	private static function ts_diff($ts) {
		return abs($ts - time()) <= self::TIME_DIFF_LIMIT;
	}

	private static function double_encrypt($string, $cid, $secret) {
		$result = '';
		$result = self::encrypt($string, $cid);
		// var_dump($result);
		$result = self::encrypt($result, $secret);
		// var_dump($result);
		return strtr(rtrim(base64_encode($result), '='), '+/', '-_');
	}

	private static function encrypt($string, $key) {
		$result = '';
		$strls = strlen($string);
		$strlk = strlen($key);
		for($i = 0; $i < $strls; $i++) {
			$char = substr($string, $i, 1);
			// echo "Test1: $char\n";
			$keychar = substr($key, ($i % $strlk) - 1, 1);
			// echo "Test2: $keychar\n";
			$char = chr((ord($char) + ord($keychar)) % 128);
			// echo "Test3: $char\n";
			// break;
			$result .= $char;
			// echo "Test4: $result\n\n";
		}
		return $result;
	}

	private static function double_decrypt($string, $cid, $secret) {
		$result = base64_decode(strtr(str_pad($string, ceil(strlen($string) / 4) * 4, '=', STR_PAD_RIGHT), '-_', '+/'));
		// var_dump($result);
		$result = self::decrypt($result, $cid);
		// var_dump($result);
		$result = self::decrypt($result, $secret);
		// var_dump($result);
		return $result;
	}

	private static function decrypt($string, $key) {
		$result = '';
		$strls = strlen($string);
		$strlk = strlen($key);
		for($i = 0; $i < $strls; $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % $strlk) - 1, 1);
			$char = chr(((ord($char) - ord($keychar)) + 256) % 128);
			$result .= $char;
		}
		return $result;
	}

	public function prepare_data($s_table_name, $a_condition)
	{
		$this->ci =& get_instance();
        $this->ci->load->model('Api_core_model','Acm');
		$return_data = array();
		$mbo_table_data = $this->ci->Acm->get_prepare_data($s_table_name, $a_condition);
		if ($mbo_table_data) {
			$mbo_table_data->portal_sync = '0';
			$return_data = array(
				'table_name' => $s_table_name,
				'data' => $mbo_table_data,
				'clause' => $a_condition
			);
		}

		return $return_data;
	}

}