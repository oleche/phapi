<?php
include_once MY_DOC_ROOT . '/controller/baseController.php';
include_once MY_DOC_ROOT . '/model/usertype.php';
include_once MY_DOC_ROOT . '/model/user.php';
include_once MY_DOC_ROOT . '/model/profile/personal.php';
include_once MY_DOC_ROOT . '/model/profile/license.php';
include_once MY_DOC_ROOT . '/model/profile/language.php';
include_once MY_DOC_ROOT . '/controller/user/userCreate.php';
include_once MY_DOC_ROOT . '/controller/user/userGet.php';
include_once MY_DOC_ROOT . '/controller/user/userPUT.php';
include_once MY_DOC_ROOT . '/controller/user/uStatus.php';

include_once MY_DOC_ROOT . '/controller/generic/put.php';
include_once MY_DOC_ROOT . '/controller/generic/create.php';
include_once MY_DOC_ROOT . '/controller/generic/get.php';
include_once MY_DOC_ROOT . '/controller/personal/personalGet.php';

include_once MY_DOC_ROOT . '/controller/helpers/allow.php';

class UserController extends BaseController implements ApiMethods
{
  public function __construct() {
		parent::__construct();
	}

  //USUALLY TO CREATE
  public function doPOST($args = array(), $verb = null) {
    if (!$this->validation_fail)
    {
      if (!allow::is_allowed($this->session->session_scopes, allow::PROFILE())){
        $this->response = allow::denied($this->session->session_scopes);
        return false;
      }
      if ($this->validate_fields($_POST, 'v1/user', 'POST')){
        $user_create = new UserCreate();
        $user_create->createUser($this->session->session_scopes);
        $this->response = $user_create->response;
      }
    }
  }

  //USUALLY TO READ INFORMATION
  public function doGET($args = array(), $verb = null) {
    if (!$this->validation_fail)
    {
      $user_get = new UserGet($this->session, $verb);
      $user_get->getUser();
      $this->response = $user_get->response;
      $this->pagination_link = $user_get->getPaginationLink();
    }
  }

  //TEND TO HAVE MULTIPLE METHODS
  public function doPUT($args = array(), $verb = null, $file = null) {
    if (!$this->validation_fail)
    {
      if (!allow::is_allowed($this->session->session_scopes, allow::PROFILE())){
        $this->response = allow::denied($this->session->session_scopes);
        return false;
      }
      if ($this->validate_fields($_POST, 'v1/user/:id', 'PUT')){
        $user_put = new UserPut($this->session, $verb);
        $user_put->putUser();
        $this->response = $user_put->response;
      }
    }
  }

  //DELETES ONE SINGLE ENTRY
  public function doDELETE($args = array(), $verb = null) {
    if (!$this->validation_fail)
    {
      $this->response['code'] = 200;
      $this->response['msg'] = "OK";
    }
  }
}

?>
