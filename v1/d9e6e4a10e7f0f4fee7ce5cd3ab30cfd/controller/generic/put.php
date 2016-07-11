<?php
class GenericPut{
  private $model;
  private $session;
  private $id;
  public $response;
  private $validScope;
  private $username;

  public function __construct($model, $session, $id = null){
    $this->model = $model;
    $this->response = array();
    $this->session = $session;
    $this->id = $id;
    $this->validScope = allow::ADMINISTRATOR();
    $this->username = null;
  }

  public function setUsername($username){
    $this->username = $username;
  }

  public function put($checkUser = false){
    if ($this->validateScope($this->session->session_scopes)){
      $theMap = $this->model->get_mapping();
      $pk = "";
      foreach ($theMap as $k => $map) {
        if (isset($map['pk']) && $map['pk'] == true){
          $pk = $k;
          break;
        }
      }
      if ($checkUser){
        if (is_null($this->username)){
          $q_list = $this->model->fetch("username = '$this->id'",false,array('username'),false);
        }else{
          if ($this->model->fetch_id(array($pk=>$this->id),null,true,"username LIKE '$this->username'"))
    				$q_list[] = $this->model;
        }
      }else{
        if ($this->model->fetch_id(array($pk=>$this->id),null,true,""))
  				$q_list[] = $this->model;
      }

      if (count($q_list) > 0){
        if ($this->doUpdate($q_list[0], $theMap)){
          $this->response['code'] = 200;
    			$this->response['message'] = 'OK';
          $this->response['title'] = 'Modelo Actualizado';
        }
      }else{
        $this->response['type'] = 'error';
        $this->response['message'] = 'Cannot retrieve data';
        $this->response['code'] = 422;
      }
    }
  }

  public function setValidScope($scope){
    $this->validScope = $scope;
  }

  private function doUpdate($model, $theMap){
    $this->username = $model->columns['username']['username'];
    if ($checkUser){
      if (!$this->validateUser()){
        return false;
      }
    }

    foreach ($theMap as $k => $map) {
      $currentValue = $model->columns[$k];
      if (isset($map['foreign'])){
        $currentValue = $model->columns[$k][$map['foreign'][0]];
      }
      if (isset($map['postable']) && $map['postable'] == true){
        $model->columns[$k] = (isset($_POST[$k]))?$_POST[$k]:$currentValue;
      }else{
        $model->columns[$k] = $currentValue;
      }
    }
    if (isset($model->columns['updated_at'])){
      $model->columns['updated_at'] = date("Y-m-d H:i:s");
    }
    if (!$model->update()){
      $this->response['type'] = 'error';
      $this->response['title'] = 'Actualizar Modelo';
      $this->response['message'] = 'No se puede actualizar';
      $this->response['code'] = 422;
      return false;
    }
    return true;
  }

  private function validateUser(){
    if (!allow::is_allowed($this->session->session_scopes, allow::ADMINISTRATOR())){
      if ($this->session->username != $this->username){
        $this->response['type'] = 'error';
        $this->response['title'] = 'Usuario';
        $this->response['message'] = 'No se puede mostrar esta informacion';
  			$this->response['code'] = 401;
        return false;
      }
    }
    return true;
  }

  private function validateScope($scope){
    if (!allow::is_allowed($scope, $this->validScope)){
      $this->response = allow::denied($scope);
      return false;
    }
    return true;
  }
}

?>
