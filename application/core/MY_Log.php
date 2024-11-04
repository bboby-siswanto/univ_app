<?php
class MY_Log extends CI_Log {
    var $key_tel='';
    var $id_tel='';
    var $tkn_tel='';

    public function write_log($level, $msg)
	{
		if ($this->_enabled === FALSE)
		{
			return FALSE;
		}

		$level = strtoupper($level);

		if (( ! isset($this->_levels[$level]) OR ($this->_levels[$level] > $this->_threshold))
			&& ! isset($this->_threshold_array[$this->_levels[$level]]))
		{
			return FALSE;
		}

		$logpath = $this->_log_path.date('Y').'/'.date('m').'/';
		$filepath = $logpath.'log-'.date('d_F_Y').'.'.$this->_file_ext;
		$message = '';

		if ( ! file_exists($filepath))
		{
			$newfile = TRUE;

			if(!file_exists($logpath)){
				mkdir($logpath, 0777, TRUE);
			}
			// Only add protection to php files
			if ($this->_file_ext === 'php')
			{
				$message .= "<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>\n\n";
			}
		}

		if ( ! $fp = @fopen($filepath, 'ab'))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);

		// Instantiating DateTime with microseconds appended to initial date is needed for proper support of this format
		if (strpos($this->_date_fmt, 'u') !== FALSE)
		{
			$microtime_full = microtime(TRUE);
			$microtime_short = sprintf("%06d", ($microtime_full - floor($microtime_full)) * 1000000);
			$date = new DateTime(date('Y-m-d H:i:s.'.$microtime_short, $microtime_full));
			$date = $date->format($this->_date_fmt);
		}
		else
		{
			$date = date($this->_date_fmt);
		}

        $find = strpos($msg, '404 Page Not Found');
		$message .= $this->_format_line($level, $date, $msg);

		// $stringfile = fread($fp, filesize($filepath));
        // $a_stored_file = explode("\n", $stringfile);
        // $s_lastfile_index = (!empty($a_stored_file[count($a_stored_file) - 1])) ? count($a_stored_file) - 1 : count($a_stored_file) - 2;
        // $laststring = $a_stored_file[$s_lastfile_index];
        // $a_laststring = explode('-->', $a_stored_file[$s_lastfile_index]);
        // $s_lastmessage = trim($a_laststring[count($a_laststring) - 1]);
		// if ($laststring != $message) {
			for ($written = 0, $length = self::strlen($message); $written < $length; $written += $result)
			{
				if (($result = fwrite($fp, self::substr($message, $written))) === FALSE)
				{
					break;
				}
			}
		// }

		flock($fp, LOCK_UN);
		fclose($fp);

		if (isset($newfile) && $newfile === TRUE)
		{
			chmod($filepath, $this->_file_permissions);
		}

        if ($find === false) {
			// if ($laststring != $message) {
			// 	$this->send_notification($message);
			// }
        }

		return is_int($result);
	}

    private function send_notification($s_message = false)
    {
        $s_key = $this->key_tel;
        $s_id = $this->id_tel;
        $s_token = $this->tkn_tel;
        
        if ($s_message) {
            $s_message = str_replace(APPPATH, '', $s_message);
            $s_message = 'LIVE:'.$s_message;

            $uri = "https://api.telegram.org/bot$s_key:$s_token/sendMessage?parse_mode=markdown&chat_id=$s_id";
            $uri .= "&text=".urlencode($s_message);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $uri);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $rs = curl_exec($ch);
            curl_close($ch);
        }
    }
}