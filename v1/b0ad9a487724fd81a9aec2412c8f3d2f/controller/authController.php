<?php
include_once MY_DOC_ROOT . "/config/config.php";
include_once MY_DOC_ROOT . '/model/usertype.php';
include_once MY_DOC_ROOT . '/model/user.php';
include_once MY_DOC_ROOT . '/model/scope.php';
include_once MY_DOC_ROOT . '/model/api/client.php';
include_once MY_DOC_ROOT . '/model/api/clientscope.php';
include_once MY_DOC_ROOT . '/model/api/field.php';
include_once MY_DOC_ROOT . '/model/api/token.php';
include_once MY_DOC_ROOT . '/model/api/userasoc.php';

class AuthController extends GCConfig
{
  const BASIC = 'Basic ';
  const BEARER = 'Bearer ';

  protected $scope;
  protected $user;
  protected $api_client;
  protected $api_client_scope;
  protected $api_token;
  protected $api_user_asoc;

  public $client_id;
	public $username;
  public $email;

  public function __construct() {
		parent::__construct();
    $this->username = '';
		$this->response = array();
    $this->api_client = new ApiClient();
    $this->api_client_scope = new ApiClientScope();
    $this->api_token = new ApiToken();
    $this->api_user_asoc = new ApiUserAsoc();
    $this->scope = new Scope();
    $this->user = new User();
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

	private function validate_login($params=array()){
		if ($this->validate_fields($params, 'login')){
			$result = array();
			$pass = sha1($params['password']);
			if ($this->user->fetch_id(array('username' => $params['username']),null,true," password = '$pass' AND enabled is TRUE ")){
				if ($this->api_user_asoc->fetch_id(array('client_id'=>$this->client_id,'username'=>$this->user->columns['username']))){
					$this->username = trim($this->user->columns['username']);
          return true;
				}else{
					$this->err = 'Usuario no asociado';
					return false;
				}
			}else{
	            $this->err = 'Invalid Credentials';
				return false;
			}
		}else{
			$this->err = $this->response['message'];
			return false;
		}
	}


  private function generate_token(){
		if ($this->locate_valid_token()){
			return $this->api_token->columns['token'];
		}else{
      $timestamp = time();
      $token = $this->encrypt($this->client_id.':'.$timestamp.':'.$this->_scopes.':'.$this->username,$this->app_secret);
      $token = $this->base64_url_encode($token);
      $this->api_token->columns['token'] = $token;
      $this->api_token->columns['created_at'] = date("Y-m-d H:i:s");
      $this->api_token->columns['updated_at'] = date("Y-m-d H:i:s");
      $this->api_token->columns['expires'] = 3600000;
      $this->api_token->columns['enabled'] = true;
      $this->api_token->columns['client_id'] = $this->client_id;
      $this->api_token->columns['scopes'] = $this->_scopes;
      $this->api_token->columns['timestamp'] = $timestamp;
			if (is_int($this->api_token->insert()))
				return $token;
			else {
				$this->err = 'Error saving token: '.$this->api_token->err_data;
				throw new Exception("Error Processing Request", 1);
				return false;
			}
		}
	}

  private function locate_valid_token(){
		$result = $this->api_token->fetch("client_id = '$this->client_id'", false, array('updated_at'), false);
		$last = false;
		foreach ($result as $r) {
			if (count($result) > 0){
				$token = $result[0]->columns['token'];
				$token = $this->decrypt($this->base64_url_decode($token), $this->app_secret);
				$token = explode(':', $token);
				$token[2] = (string)$token[2];
				$token[3] = (string)$token[3];

				if (trim($this->username) == "" || trim($this->username) == trim($token[3])){
					if (count($token) == 4){
						if ((trim($this->_scopes) == trim($token[2])) && (((strtotime($result[0]->columns['updated_at'])*1000)+$result[0]->columns['expires']) > (time()*1000))){
							$this->api_token = $result[0];
							$this->api_token->columns['updated_at'] = date("Y-m-d H:i:s");
							$this->api_token->columns['client_id'] = $result[0]->columns['client_id']['client_id'];
							$this->api_token->update();
							return true;
						}else{
							$result[0]->columns['enabled'] = 0;
							$result[0]->columns['client_id'] = $result[0]->columns['client_id']['client_id'];
							if (!$result[0]->update())
								$this->err = 'Error deleting';
							return false;
						}
					}else{
						$this->err = 'Malformed token';
						return false;
					}
				}else{
					$last = false;
				}
			}else{
				$last = false;
			}
		}
		return $last;

	}

  private function validate_scopes($method){
    $retval = true;
    if ($method == 'GET'){
      return true;
    }

    $scopes_arr = explode(',', $this->_scopes);
    if (count($scopes_arr) <= 0){
      $retval = ($retval && false);
      $this->err = "no scopes selected";
    }

    foreach ($scopes_arr as $value) {
      if ($this->scope->fetch_id(array("name"=>$value))){
        $result = $this->api_client_scope->fetch("id_client = '$this->client_id' AND id_scope = '".$this->scope->columns['name']."'");
        if (count($result) > 0){
          $retval = ($retval && true);
        } else {
          $retval = ($retval && false);
          $this->err = "invalid scope for client";
        }
      }else{
        $this->err = "scope '$value' not found";
        $retval = ($retval && false);
      }
    }
    return $retval;
  }

  private function validate_basic($params = array()){
    $token64 = base64_decode($this->token);
    $result = $this->api_client->fetch("CONCAT(client_id,':',client_secret) = '$token64' AND enabled is true");
    if (count($result) == 1){
      $this->client_id = $result[0]->columns['client_id'];
      $this->email = $result[0]->columns['email'];
      $this->username = $result[0]->columns['user_id']['username'];
      if ($result[0]->columns['asoc'] == 1){
        return $this->validate_login($params);
      }
      return true;
    }else{
      $this->err = 'Token not found';
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

  public function doPOST($token = null, $params = array(), $method = null){
		try{
			if ($this->sanitize_token($token, self::BASIC)){
  		    $this->_scopes = (isset($params['scope']) && $params['scope'] != '')?$params['scope']:'';
      		if ($this->validate_basic($params) && $this->validate_scopes($method)){
  				$this->response['code'] = 200;
  				$this->response['access_token'] = $this->generate_token();
  				$this->response['expires'] = ((strtotime($this->api_token->columns['updated_at'])*1000)+$this->api_token->columns['expires']) - (time()*1000);
  				return true;
  			}else{
  				$this->response['type'] = 'error';
          if (isset($this->response['http_code']) && trim($this->response['http_code']) != "")
            $this->response['code'] = 422;
          else
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

  public function doGET() {}
  public function doPUT() {}
  public function doDELETE() {}


}

?>
