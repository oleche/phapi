<?php


require_once 'API.class.php';
include_once 'controller/authController.php';


class CVAPI extends API
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
        break;
    }

  //Bearer token validation, maybe
  }

  // /v1/country - GET, POST
  // /v1/country/:id - GET, PUT, DELETE
  // /v1/country/:id/state - GET, POST
  protected function country(){
  }

  // /v1/state/:id - GET, POST, DELETE
  // /v1/state/:id/zone - GET, POST
  protected function state(){
  }

  // /v1/zone/:id - GET, POST, DELETE
  protected function zone(){
  }

  //
  // /v1/ethnic - GET, POST
  // /v1/ethnic/:id - GET, PUT, DELETE
  protected function ethnic(){
  }

  //
  // /v1/religion - GET, POST
  // /v1/religion/:id - GET, PUT, DELETE
  protected function religion(){
  }

  //
  // /v1/socialmedia - GET, POST
  // /v1/socialmedia/:id - GET, PUT, DELETE
  protected function socialmedia(){
  }

  //
  // /v1/language - GET, POST
  // /v1/language/:id - GET, PUT, DELETE
  protected function language(){
  }

  //
  // /v1/academic - GET, POST -> QUERY POR USER (q[user]=:id)
  // /v1/academic/:id - GET, PUT, DELETE
  protected function academic(){
  }

  //
  // /v1/certificationtype - GET, POST
  // /v1/certificationtype/:id - GET, PUT, DELETE
  protected function certificationtype(){
  }

  //
  // /v1/institution - GET, POST
  // /v1/institution/:id - GET, PUT, DELETE
  protected function institution(){
  }

  //
  // /v1/title - GET, POST
  // /v1/title/:id - GET, PUT, DELETE
  protected function title(){
  }

  //
  // /v1/partner - GET, POST
  // /v1/partner/:id - GET, PUT, DELETE
  protected function partner(){
  }

  //
  // /v1/company - GET, POST
  // /v1/company/:id - GET, PUT, DELETE
  protected function company(){
  }

  //
  // /v1/profession - GET, POST
  // /v1/profession/:id - GET, PUT, DELETE
  protected function profession(){
  }

  //
  // /v1/professionarea - GET, POST
  // /v1/professionarea/:id - GET, PUT, DELETE
  protected function professionarea(){
  }

  //
  // /v1/achievementtype - GET, POST
  // /v1/achievementtype/:id - GET, PUT, DELETE
  protected function achievementtype(){
  }

  //
  // /v1/reference - GET, POST
  // /v1/reference/:id - GET, PUT, DELETE
  protected function reference(){
  }

  //
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

  //
  // /v1/user - GET, POST
  // /v1/user/:id - GET, PUT
  // /v1/user/:id/status - GET
  // /v1/user/:id/personal - GET, PUT -> Personal ser crea al crear el usuario
  // /v1/user/:id/social - GET, POST
  // /v1/user/:id/language - GET, POST
  // /v1/user/:id/academic - GET, POST
  // /v1/user/:id/skill - GET, POST
  // /v1/user/:id/professional - GET, PUT
  // /v1/user/:id/professionalarea - GET, POST
  // /v1/user/:id/experience - GET, POST
  // /v1/user/:id/achievement - GET, POST
  // /v1/user/:id/reference - GET, POST
  // /v1/user/:id/message - GET, POST
  //
  // /v1/user/personal/:id/upload PUT -> Debe haber un tipo_asset field
  // /v1/user/language/:id/upload PUT -> Debe haber un tipo_asset field
  // /v1/user/academic/:id/upload PUT -> Debe haber un tipo_asset field
  //
  // /v1/user/social/:id - GET, PUT, DELETE
  //
  // /v1/user/language/:id - GET, PUT, DELETE
  //
  // /v1/user/academic/:id - GET, PUT, DELETE
  //
  // /v1/user/skill/:id - GET, DELETE
  //
  // /v1/user/professionalarea/:id - GET, PUT, DELETE
  //
  // /v1/user/experience/:id - GET, PUT, DELETE
  //
  // /v1/user/achievement/:id - GET, PUT, DELETE
  //
  // /v1/user/reference/:id - GET, PUT, DELETE
  //
  // /v1/user/message/:id - GET, PUT, DELETE
  // /v1/user/message/:id/action - GET, POST -> GET muestra los disponibles, en post se ejecuta la accion. Los mensajes manejan un state machine
  protected function user(){
  }

  //
  // /v1/asset/:id - GET, DELETE
  // /v1/asset/ - GET -> QUERY POR USER (q[user]=:id)
  protected function asset(){
  }


  /**
   * /v1/module
   */
  /*protected function module(){
    if ($this->session->validate_bearer_token($_SERVER['HTTP_Authorization'])){
      switch ($this->method) {
        case 'GET':
          if (allow::is_allowed($this->session->session_scopes, allow::PUBLISH())){
            switch($this->verb){
              case 'actions':
                $this->action->actions();
                $this->response_code = $this->action->response['http_code'];
                return $this->action->response;
                break;
              case 'form':
                $this->action->form();
                $this->response_code = $this->action->response['http_code'];
                return $this->action->response;
                break;
              default:
                $id = null;
                if (isset($this->verb) && (trim($this->verb) != '')){
                  $id = $this->verb;
                }
                if (isset($_GET['show']) && (!allow::is_allowed($this->session->session_scopes, allow::MODERATE()))){
                  $this->response_code = '401';
                  return allow::denied($this->session->session_scopes);
                }
                if (!isset($this->args[0])){
                  $this->args[0]="";
                }
                switch($this->args[0]){
                  case 'actions':
                    $this->action = new ACTION('action');
                    $this->action->show_actions($id, $_GET, $this->session->username);
                    $this->response_code = $this->action->response['http_code'];
                    return $this->action->response;
                    break;
                  default:
                    $this->action->show($id, $_GET, $this->session->username);
                    $this->response_code = $this->action->response['http_code'];
                    return $this->action->response;
                  break;
                }
                break;
            }
          }else{
            $this->response_code = '401';
            return allow::denied($this->session->session_scopes);
          }
          break;
        case 'POST':
          if (allow::is_allowed($this->session->session_scopes, allow::PUBLISH())){
            $this->action->create($_POST, $this->session->username);
            $this->response_code = $this->action->response['http_code'];
            return $this->action->response;
          }else{
            $this->response_code = '401';
            return allow::denied($this->session->session_scopes);
          }
          break;
        case 'PUT':
          if (isset($this->verb) && isset($this->args[0]) && (trim($this->args[0]) != '')){
            switch($this->args[0]){
              case 'register-action':
                if (!allow::is_allowed($this->session->session_scopes, allow::PUBLISH())){
                  $this->response_code = '401';
                  return allow::denied($this->session->session_scopes);
                }
                parse_str($this->file,$post_vars);
                $this->action = new ACTION('action');
                $this->action->create($this->verb, $post_vars, $this->session->username);
                $this->response_code = $this->action->response['http_code'];
                return $this->action->response;
                break;
              case 'execute-action':
                if (!allow::is_allowed($this->session->session_scopes, allow::PUBLISH())){
                  $this->response_code = '401';
                  return allow::denied($this->session->session_scopes);
                }
                parse_str($this->file,$post_vars);
                $this->action = new ACTION('action');
                $this->action->execute($this->verb, $post_vars, $this->session->username);
                $this->response_code = $this->action->response['http_code'];
                return $this->action->response;
                break;
              case 'request-status':
                if (!allow::is_allowed($this->session->session_scopes, allow::PUBLISH())){
                  $this->response_code = '401';
                  return allow::denied($this->session->session_scopes);
                }
                parse_str($this->file,$post_vars);
                $this->action = new ACTION('action');
                $this->action->status($this->verb, $post_vars, $this->session->username);
                $this->response_code = $this->action->response['http_code'];
                return $this->action->response;
                break;
              case 'disable':
                if (!allow::is_allowed($this->session->session_scopes, allow::PUBLISH())){
                  $this->response_code = '401';
                  return allow::denied($this->session->session_scopes);
                }
                $this->action->disable($this->args[0], $_GET);
                $this->response_code = $this->action->response['http_code'];
                return $this->action->response;
                break;
              case 'enable':
                if (!allow::is_allowed($this->session->session_scopes, allow::PUBLISH())){
                  $this->response_code = '401';
                  return allow::denied($this->session->session_scopes);
                }
                $this->action->enable($this->args[0], $_GET);
                $this->response_code = $this->action->response['http_code'];
                return $this->action->response;
                break;
              default:
                $this->response_code = '404';
                return $this::return_message('Acción Invalida, metodo no encontrado: '.$this->args[1],'error');
                break;
            }
          }else{
            $this->response_code = '401';
            return $this::return_message('URL Invalido','error');
          }
          break;
        case 'DELETE':
          if (isset($this->verb) && (trim($this->verb) != '') && (isset($this->args[0]))){
            switch($this->args[0]){
              case 'actions':
                if (!allow::is_allowed($this->session->session_scopes, allow::PUBLISH())){
                  $this->response_code = '401';
                  return allow::denied($this->session->session_scopes);
                }
                parse_str($this->file,$post_vars);
                $this->action = new ACTION('action');
                $this->action->delete($this->verb, $post_vars, $this->session->session_token, $_GET);
                $this->response_code = $this->action->response['http_code'];
                return $this->action->response;
                break;
              default:
                $this->response_code = '401';
                return $this::return_message('Método Invalido','error');
                break;
            }
          }else{
            if (isset($this->verb) && (trim($this->verb) != '')){
              if (!allow::is_allowed($this->session->session_scopes, allow::PUBLISH())){
                $this->response_code = '401';
                return allow::denied($this->session->session_scopes);
              }
              $this->action->delete($this->verb, $this->session->username);
              $this->response_code = $this->action->response['http_code'];
              return $this->action->response;
            }else{
              $this->response_code = '401';
              return $this::return_message('URL Invalido','error');
            }
          }
          break;
        default:
          $this->response_code = '401';
          return $this::return_message('Método Invalido','error');
          break;
      }
    }else{
      if ($this->method == 'OPTIONS'){
        //$this->response_code = '200';
        //return "OK";
        exit(0);
      }else{
        $this->response_code = $this->session->response['code'];
        return $this->session->response;
      }
    }
  }

  /**
  * /v1/user
  */
  /*protected function userold(){
    if ($this->session->validate_bearer_token($_SERVER['HTTP_Authorization'])){
      switch ($this->method) {
        case 'GET':
          if (allow::is_allowed($this->session->session_scopes, allow::PUBLISH())){
            switch($this->verb){
              case 'form':
                $this->action->form();
                $this->response_code = $this->action->response['http_code'];
                return $this->action->response;
                break;
              default:
                $id = null;
                if (isset($this->verb) && (trim($this->verb) != '')){
                  $id = trim($this->verb);
                  if (($id != $this->session->username) && (!allow::is_allowed($this->session->session_scopes, allow::MODERATE()))){
                    $this->response_code = '401';
                    return allow::denied($this->session->session_scopes);
                  }
                }else{
                  if (!allow::is_allowed($this->session->session_scopes, allow::ADMINISTRATOR())){
                    $this->response_code = '401';
                    return allow::denied($this->session->session_scopes);
                  }
                }
                if (isset($_GET['show']) && (!allow::is_allowed($this->session->session_scopes, allow::MODERATE()))){
                  $this->response_code = '401';
                  return allow::denied($this->session->session_scopes);
                }
                if (!isset($this->args[0])){
                  $this->args[0]="";
                }
                switch($this->args[0]){
                  case 'api': //user/:id/api
                    $this->action->show_api($id);
                    $this->response_code = $this->action->response['http_code'];
                    return $this->action->response;
                    break;
                  default: //user/:id
                    $this->action->show($id, $_GET);
                    $this->add_header($this->action->pagination_link);
                    $this->response_code = $this->action->response['http_code'];
                    return $this->action->response;
                    break;
                }
                break;
            }
          }else{
            $this->response_code = '401';
            return allow::denied($this->session->session_scopes);
          }
          break;
        case 'POST':
          if (allow::is_allowed($this->session->session_scopes, allow::MODERATE())){
            $this->action->create($_POST, $this->session->session_token);
            $this->response_code = $this->action->response['http_code'];
            return $this->action->response;
          }else{
            $this->response_code = '401';
            return allow::denied($this->session->session_scopes);
          }
          break;
        case 'PUT':
          if (isset($this->verb) && isset($this->args[0]) && (trim($this->args[0]) != '')){
            switch($this->args[0]){
              case 'upload':
                if (($this->verb != $this->session->username) && !allow::is_allowed($this->session->session_scopes, allow::ADMINISTRATOR())){
                  $this->response_code = '401';
                  return allow::denied($this->session->session_scopes);
                }
                $this->action->upload($this->verb, $this->file);
                $this->response_code = $this->action->response['http_code'];
                return $this->action->response;
                break;
              case 'enable':
                if (!allow::is_allowed($this->session->session_scopes, allow::MODERATE())){
                  $this->response_code = '401';
                  return allow::denied($this->session->session_scopes);
                }
                $this->action->enable($this->verb);
                $this->response_code = $this->action->response['http_code'];
                return $this->action->response;
                break;
              case 'update':
                if (!allow::is_allowed($this->session->session_scopes, allow::PUBLISH())){
                  $this->response_code = '401';
                  return allow::denied($this->session->session_scopes);
                }
                parse_str($this->file,$post_vars);
                $this->action->update($this->verb, $post_vars, $this->session->username, $_GET);
                $this->response_code = $this->action->response['http_code'];
                return $this->action->response;
                break;
              case 'disable':
                if (!allow::is_allowed($this->session->session_scopes, allow::VALIDATE())){
                  $this->response_code = '401';
                  return allow::denied($this->session->session_scopes);
                }
                $this->action->disable($this->verb);
                $this->response_code = $this->action->response['http_code'];
                return $this->action->response;
                break;
              default:
                $this->response_code = '404';
                return $this::return_message('Acción Invalida, metodo no encontrado: '.$this->args[1],'error');
                break;
            }
          }else{
            $this->response_code = '401';
            return $this::return_message('URL Invalido','error');
          }
          break;
        case 'DELETE':
          if (isset($this->verb) && (trim($this->verb) != '')){
            $id = $this->verb;
            if (!allow::is_allowed($this->session->session_scopes, allow::ADMINISTRATOR())){
              $this->response_code = '401';
              return allow::denied($this->session->session_scopes);
            }
            $this->action->delete($id);
            $this->response_code = $this->action->response['http_code'];
            return $this->action->response;
          }else{
            $this->response_code = '401';
            return $this::return_message('URL Invalido','error');
          }
          break;
        case 'OPTIONS':
          $this->response_code = '200';
          return "OK";
          break;
        default:
          $this->response_code = '401';
          return $this::return_message('Método Invalido','error');
          break;
      }
    }else{
      $this->response_code = $this->session->response['code'];
      return $this->session->response;
    }
  }

  protected function action_type(){
    switch ($this->method) {
      case 'GET':
        $this->action = new ACTION('action');
        $this->action->show_action_type();
        $this->response_code = $this->action->response['http_code'];
        return $this->action->response;
        break;
      case 'POST':
      default:
        $this->response_code = 405;
        return "Invalid action method";
      break;
    }
  }

  protected function module_type(){
    switch ($this->method) {
      case 'GET':
        $this->action = new ACTION('action');
        $this->action->show_module_type($_GET);
        $this->response_code = $this->action->response['http_code'];
        return $this->action->response;
        break;
      case 'POST':
      default:
        $this->response_code = 405;
        return "Invalid action method";
      break;
    }
  }

  protected function action_response(){
    switch ($this->method) {
      case 'GET':
        $this->action = new ACTION('action');
        $this->action->show_action_response($_GET);
        $this->response_code = $this->action->response['http_code'];
        return $this->action->response;
        break;
      case 'POST':
      default:
        $this->response_code = 405;
        return "Invalid action method";
      break;
    }
  }

  protected function session(){
    switch ($this->method) {
     case 'POST':
    	 $this->session->validate_basic_token($_SERVER['HTTP_Authorization'], $_POST, $this->method);
    	 $this->response_code = $this->session->response['code'];
       return $this->session->response;
    	 break;
     default:
    	 $this->response_code = 405;
    	 return "Invalid action method";
    	 break;
    }
  }
  */

  protected function welcome() {
    if ($this->method == 'GET') {
      return "WELCOME TO CVJUNGLE API - BIENVENIDO AL API DE CVJUNGLE";
    } else {
      return "Invalid Method";
    }
  }
}

?>
