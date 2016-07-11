<?php
include_once MY_DOC_ROOT . "/controller/helpers/allow.php";

class GenericDeleteImage
{
  private $model;
  private $id;
  private $session;
  public $response;
  private $checkUser;

  public function __construct($model, $id, $session){
    $this->model = $model;
    $this->response = array();
    $this->id = $id;
    $this->session = $session;
    $this->checkUser = false;
    $this->path = null;
  }

  public function checkUser(){
    $this->checkUser = true;
  }

  public function setPath($path){
    $this->path = $path;
  }

  public function delete(){
    if ($this->validate_scope()){

      if ($this->model->fetch_id(array('id'=>$this->id))){
        if (is_null($this->path)){
          $this->response['type'] = 'error';
          $this->response['message'] = 'Path not set';
          $this->response['code'] = 422;
          return false;
        }
        $filepath = $this->model->columns[$this->path];
        if ($this->checkUser){
          $username = $this->model->columns['username']['username'];
          if (!$this->validateUser($username)){
            return false;
          }
        }
        if (!$this->model->delete()){
          $this->response['type'] = 'error';
          $this->response['title'] = 'Borrar modelo';
          $this->response['message'] = 'Se ha producido el siguiente error: '.$this->model->err_data;
          $this->response['code'] = 422;
        }else{
          if ($this->remove_asset($filepath)){
            $this->response['message'] = 'Deleted';
            $this->response['code'] = 200;
          }
        }
      }else{
        $this->response['type'] = 'error';
        $this->response['message'] = 'Cannot retrieve data';
        $this->response['code'] = 422;
      }
    }
  }

  //Private Methods

  private function validate_scope(){
    if (!allow::is_allowed($this->session->session_scopes, allow::PROFILE())){
      $this->response = allow::denied($scope);
      return false;
    }
    return true;
  }

  private function remove_asset($url){
    if (isset($url) && (trim($url) != "")){
      $filepath = MY_ASSET_ROOT.'/'.$url;
      if (file_exists($filepath)) {
        if (unlink($filepath)){
          return true;
        }else{
          $this->response['type'] = 'error';
          $this->response['title'] = 'Eliminar Archivo';
          $this->response['message'] = 'El archivo no ha podido ser eliminado';
          $this->response['code'] = 500;
          return false;
        }
      }
    }
    return true;
  }

  private function validateUser($username){
    if (!allow::is_allowed($this->session->session_scopes, allow::ADMINISTRATOR())){
      if ($this->session->username != $username){
        $this->response['type'] = 'error';
        $this->response['title'] = 'Usuario';
        $this->response['message'] = 'No se puede mostrar esta informacion';
  			$this->response['code'] = 401;
        return false;
      }
    }
    return true;
  }

}

?>
