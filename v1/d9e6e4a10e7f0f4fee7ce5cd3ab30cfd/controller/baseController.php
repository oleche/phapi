<?php

include_once MY_DOC_ROOT . "/config/config.php";
include_once "helpers/allow.php";
include_once "sessionHelper.php";

class BaseController extends GCConfig
{
  protected $session;
  protected $validation_fail;

  public $response;
  public $pagination_link = "";

  public function __construct(){
    parent::__construct();
    $this->response = array();
    $this->session = new SessionHelper($this->app_secret);
    if (!$this->session->validate_bearer_token($_SERVER['HTTP_Authorization'])){
      $this->validation_fail = true;
      $this->response = $this->session->response;
    }
  }

}

?>
