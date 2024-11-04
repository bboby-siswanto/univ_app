<?php
class IULI_Lms
{
    private $host = '';
    private $token = '';

    private function execute_post($param) {
        $a_param_data = [
            'wstoken' => $this->token,
            'moodlewsrestformat' => 'json'
        ];
        if (!empty($param)) {
            $post_body = array_merge($a_param_data, $param);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
            curl_setopt($ch, CURLOPT_URL, $this->host);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = json_decode(curl_exec($ch));
            curl_close($ch);

            $result_ch = [
                'result' => $result,
                'post_data' => $post_body
            ];
            return $result_ch;
        }
        else {
            return false;
        }
    }

    function execute($s_function, $param = false) {
        if (!$param) {
            $param = [
                'wsfunction' => $s_function
            ];
        }
        else {
            $param['wsfunction'] = $s_function;
        }

        $result = $this->execute_post($param);
        return $result;
    }
}
