<?php

include_once MY_DOC_ROOT . '/model/api/token.php';

class SessionHelper {
  const BASIC = 'Basic ';
  const BEARER = 'Bearer ';

  protected $app_secret;
  protected $token;
  protected $api_token;
  public $username;
  public $session_scopes;
  public $session_token;
  public $err;
  public $response;


  public function __construct($app_secret){
    $this->app_secret = $app_secret;
    $this->api_token = new ApiToken();
    $this->response = array();
		$this->username = '';
  }

  private function sanitize_token($token, $type){
    $this->token = str_replace($type, '', $token);
    return (strpos($token,$type) !== false);
  }

  function base64_url_encode($input) {
	 return strtr(base64_encode($input), '+/', '-_');
	}

	function base64_url_decode($input) {
	 return base64_decode(strtr($input, '-_', '+/'));
	}

  /**
   * Returns an encrypted & utf8-encoded
   */
  function encrypt($pure_string, $encryption_key) {
      $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
      $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
      $encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, $encryption_key, utf8_encode($pure_string), MCRYPT_MODE_ECB, $iv);
      return $encrypted_string;
  }

  /**
   * Returns decrypted original string
   */
  function decrypt($encrypted_string, $encryption_key) {
      $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
      $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
      $decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, $encryption_key, $encrypted_string, MCRYPT_MODE_ECB, $iv);
      return $decrypted_string;
  }


  private function validate_token($token){
    $this->session_token = $token;
    $result = $this->api_token->fetch("token = '$token' AND enabled is TRUE", false, array('updated_at'), false);
    if (count($result) == 1){
      $token = $this->decrypt($this->base64_url_decode($this->token), $this->app_secret);
      $token = explode(':', $token);

      if (count($token) == 4){
        if (((strtotime($result[0]->columns['updated_at'])*1000)+$result[0]->columns['expires']) > (time()*1000)){
          $this->session_scopes = $token[2];
          $this->username = trim($token[3]);

          return true;
        }else{
          $result[0]->columns['enabled'] = 0;
          $result[0]->columns['client_id'] = $result[0]->columns['client_id']['client_id'];
          $result[0]->update();
          $this->err = 'Expired token';
          return false;
        }
      }else{
        $this->err = 'Malformed token';
        return false;
      }
    }else{
      $this->err = 'Invalid token';
      return false;
    }
  }

  public function validate_bearer_token($token){
    try{
      if ($this->sanitize_token($token, self::BEARER)){
        if ($this->validate_token($this->token)){
          return true;
        }else{
          $this->response['type'] = 'error';
          $this->response['code'] = 401;
          $this->response['message'] = $this->err;
          return false;
        }
      }else{
        $this->response['type'] = 'error';
        $this->response['code'] = 401;
        $this->response['message'] = 'Malformed token';
        return false;
      }
    }catch(Exception $e){
      $this->response['type'] = 'error';
      $this->response['code'] = 401;
      $this->response['message'] = $this->err;
      return false;
    }
	}
}

?>
