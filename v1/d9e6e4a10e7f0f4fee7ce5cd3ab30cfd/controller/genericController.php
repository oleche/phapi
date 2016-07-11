<?php
include_once MY_DOC_ROOT . '/controller/baseController.php';
include_once MY_DOC_ROOT . '/controller/generic/create.php';
include_once MY_DOC_ROOT . '/controller/generic/get.php';
include_once MY_DOC_ROOT . '/controller/generic/put.php';
include_once MY_DOC_ROOT . '/controller/generic/delete.php';

class GenericController extends BaseController implements ApiMethods
{
  private $model;
  private $form_endpoint;

  public function __construct() {
		parent::__construct();
	}

  //USUALLY TO CREATE
  public function doPOST($args = array(), $verb = null) {
    if (!$this->validation_fail)
    {
      if (!allow::is_allowed($this->session->session_scopes, allow::ADMINISTRATOR())){
        $this->response = allow::denied($this->session->session_scopes);
        return false;
      }
      if ($this->validate_fields($_POST, $this->form_endpoint, 'POST')){
        $create = new GenericCreate($this->model);
        $create->create($this->session->session_scopes);
        $this->response = $create->response;
      }
    }
  }

  //USUALLY TO READ INFORMATION
  public function doGET($args = array(), $verb = null) {
    if (!$this->validation_fail)
    {
      $ident = null;
      if ((count($args) > 0) && (is_numeric($args[0]))){
        $ident = $args[0];
      }else{
        $ident = $verb;
      }
      $get = new GenericGet($this->model, $this->session, $ident);
      $get->get();
      $this->response = $get->response;
      $this->pagination_link = $get->getPaginationLink();
    }
  }

  //TEND TO HAVE MULTIPLE METHODS
  public function doPUT($args = array(), $verb = null, $file = null) {
    if (!$this->validation_fail)
    {
      if (!allow::is_allowed($this->session->session_scopes, allow::ADMINISTRATOR())){
        $this->response = allow::denied($this->session->session_scopes);
        return false;
      }
      if ($this->validate_fields($_POST, $this->form_endpoint, 'PUT')){
        $put = new GenericPut($this->model, $this->session, $verb);
        $put->put();
        $this->response = $put->response;
      }
    }
  }

  //DELETES ONE SINGLE ENTRY
  public function doDELETE($args = array(), $verb = null) {
    if (!$this->validation_fail)
    {
      $ident = null;
      if ((count($args) > 0) && (is_numeric($args[0]))){
        $ident = $args[0];
      }else{
        $ident = $verb;
      }

      $delete = new GenericDelete($this->model, $ident);
      $delete->delete($this->session->session_scopes);
      $this->response = $delete->response;
    }
  }

  public function setModel($model){
    $this->model = $model;
  }

  public function setFormEndpoint($endpoint){
    $this->form_endpoint = $endpoint;
  }

  private function validateArgsAndVerb($args, $verb){

  }

}

?>
