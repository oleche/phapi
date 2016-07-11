<?php


require_once 'API.class.php';
include_once 'controller/authController.php';
include_once 'controller/genericController.php';


class PHAPI extends API
{
  protected $user;
  protected $action;

  public function __construct($request, $origin) {
    parent::__construct($request);

    switch($this->endpoint){
      case 'auth':
        $this->action = new AuthController();
        $this->action->setRequest($request);
        break;
      default:
        $this->action = new GenericController($this->endpoint);
        $this->action->setRequest($request);
        break;
    }

  //Bearer token validation, maybe
  }

  // /v1/auth POST
  protected function auth(){
    switch ($this->method) {
     case 'POST':
       $this->action->doPost($_SERVER['HTTP_Authorization'], $_POST, $this->method);
       $this->response_code = $this->action->response['code'];
       return $this->action->response;
       break;
     default:
       $this->response_code = 405;
       return "Invalid action method";
       break;
    }
  }

  protected function user(){
  }

  //
  // /v1/model/:id - GET, DELETE, PUT
  // /v1/model/ - GET, POST
  protected function model(){
    $this->action->setModel(new SiteModel());
    switch ($this->method) {
      case 'POST':
        $this->action->setFormEndpoint('v1/languages');
        break;
      case 'PUT':
        $this->action->setFormEndpoint('v1/languages/:id');
        break;
      default:
    }
    return $this->doRegulaCall();
  }

  protected function welcome() {
    if ($this->method == 'GET') {
      return "WELCOME TO PHAPI";
    } else {
      return "Invalid Method";
    }
  }
}

?>
