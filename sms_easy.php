<?php

/**
 *  php lib for send SMS using service https://easysms.4simple.org
 */


/**
 * Main Client API class version 1.0.0
*/
class SMS_Easy
{

    private $credentials = false;
    private $API_URL = "https://api.4simple.org/";
    private $retries = 5;

    /**
     * Lib version.
     */
    public static $VERSION = "1.0.0";

    /**
     *  API Client object constructor.
     * 
     * @param int $user_id Your account User ID (located in https://easysms.4simple.org/user/panel/)
     * @param string $auth_token Your account Authentication Token (located in https://easysms.4simple.org/user/panel/)
     * @param int $retries Amount of attempts to send the SMS if server is busy.
    */
    public function SMS_Easy($user_id, $auth_token, $retries=5)
    {
        $this->credentials = [
            "user_id" => $user_id,
            "auth_token" => $auth_token
        ];
        $this->retries = $retries;
    }

    /**
     * Send SMS using this function.
     *
     *@param string $to recipient phone number. Remember add international country code prefix.
     *@param string $body sms text message to send.
     *@return: A array with the server response.
     * When all was fine returned array should be similar to:
     *  ['success'=>'ok', 'pid'=> 123}
     * The 'pid' var can be used to track sms in the system.
     * When operation fails returned array should be similar to:
     *  ['error'=> 'error description']
    */
    public function send_sms($to, $body)
    {
        $data = $this->credentials + ['to' => $to, 'body' => $body];
        return $this->send_payload('sms', $data);
    }

    /*
     * Get the delivered SMS status.
     * 
     *@param int $pid pid var returned while you send SMS
     *@return An array with the server response.
     *Examples of servers responses
     *
     *['status'=> 'queued']
     *  The status key is returned with the value set as the sms current status. Possible status values are:
     *    queued, when the sms is in the processing queue waiting to be delivered.
     *    success-delivered, when the sms was delivered successful.
     *    failed, when the sms delivery fails.
     *
     *['error'=> 'error description']
     *  The error key is returned if you submit incorrect credentials or use an invalid processing id pid.
     *  Some of the possible error details returned are:
     *    Login error, when incorrect credentials are provided.
     *    Pid error, when incorrect processing id pid is provided. 
    */
    public function get_sms_status($pid)
    {
        $data = $this->credentials + ['pid' => $pid];
        return $this->send_payload('status', $data);
    }
    
    /**
     * Get your current account balance.
     * 
     * @return Accout balance or a array with the error code response like: ['error'=>'Login error']
    */
    public function get_balance()
    {
        $result = $this->send_payload('balance', $this->credentials);
        if( isset($result['balance']) ){
            return $result['balance'];
        }
        return $result;
    }
    
    /**
     * Private function don't use it directly.
    */
    private function send_payload($cmd, $data)
    {
        $url = $this->API_URL . $cmd;
        for ($i = 0; $i < $this->retries; $i++){
            try{
                
                $defaults = array(
                    CURLOPT_POST => 1,
                    CURLOPT_HEADER => 0,
                    CURLOPT_URL => $url,
                    CURLOPT_FRESH_CONNECT => 1,
                    CURLOPT_RETURNTRANSFER => 1,
                    CURLOPT_FORBID_REUSE => 1,
                    CURLOPT_TIMEOUT => 7,
                    CURLOPT_POSTFIELDS => http_build_query($data)
                ); 

                $ch = curl_init();
                curl_setopt_array($ch, $defaults);
                if( ! $result = curl_exec($ch))
                {
                    curl_close($ch);
                    continue;
                }
                curl_close($ch);
                return json_decode($result, true);
            } catch (Exception $e) {
                error_log('Apptent #' . strval($i+1) .
                    ' failed due to: ' . $e->getMessage() . "\n");
            }
        }
        throw new Exception('Server is  busy, try some minutes latter.');
    }
}

?>
