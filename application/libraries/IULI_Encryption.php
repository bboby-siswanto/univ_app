<?php
class IULI_Encryption
{
	// const TIME_DIFF_LIMIT = 300; // 5 menit
    private $keystring = '';

	public static function hash_data(array $json_data, $cid, $s_timelimit = false) {
        $hashstring = ($s_timelimit) ? strrev(time()).'.'.strrev($s_timelimit).'.'. json_encode($json_data) : strrev(time()).'.'. json_encode($json_data);
		return self::double_encrypt($hashstring, $cid);
	}

	public static function parse_data($hased_string, $cid) {
		$parsed_string = self::double_decrypt($hased_string, $cid);
        $parsed_limit = (is_numeric(explode('.', $parsed_string)[1])) ? 3 : 2 ;
		list($timestamp, $data1, $data2) = array_pad(explode('.', $parsed_string, $parsed_limit), 3, null);
        if ($parsed_limit > 2) {
            if (self::ts_diff(strrev($timestamp), strrev($data1)) === true) {
                return json_decode($data2, true);
            }
            return null;
        }
		
		return json_decode($data1, true);
	}

	private static function ts_diff($ts, $limit) {
		return abs($ts - time()) <= $limit;
	}

	private static function double_encrypt($string, $cid) {
		$result = '';
		$result = self::encrypt($string, $cid);
		$result = self::encrypt($result, '');
		return strtr(rtrim(base64_encode($result), '='), '+/', '-_');
	}
	private static function encrypt($string, $key) {
		$result = '';
		$strls = strlen($string);
		$strlk = strlen($key);
		for($i = 0; $i < $strls; $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % $strlk) - 1, 1);
			$char = chr((ord($char) + ord($keychar)) % 128);
			$result .= $char;
		}
		return $result;
	}

	private static function double_decrypt($string, $cid) {
		$result = base64_decode(strtr(str_pad($string, ceil(strlen($string) / 4) * 4, '=', STR_PAD_RIGHT), '-_', '+/'));
		$result = self::decrypt($result, $cid);
		$result = self::decrypt($result, '');
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

}