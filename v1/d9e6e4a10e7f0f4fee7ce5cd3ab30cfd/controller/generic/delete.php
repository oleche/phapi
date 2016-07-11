<?php
include_once MY_DOC_ROOT . "/controller/helpers/allow.php";

class GenericDelete
{
  private $model;
  private $id;
  public $response;

  public function __construct($object, $id){
    $this->model = $object;
    $this->response = array();
    $this->id = $id;
  }

  public function delete($scope){
    $map = $this->model->get_mapping();
    if ($this->validate_scope($scope)){
      $pk = "";

      foreach ($map as $k => $map) {
        if (isset($map['pk']) && $map['pk'] == true){
          $pk = $k;
          break;
        }
      }

      if ($this->model->fetch_id(array($pk=>$this->id))){
  	    if (!$this->model->delete()){
          $this->response['type'] = 'error';
          $this->response['title'] = 'Borrar modelo';
          $this->response['message'] = 'Se ha producido el siguiente error: '.$this->model->err_data;
    			$this->response['code'] = 422;
        }else{
          $this->response['message'] = 'Deleted';
          $this->response['code'] = 200;

        }
      }
    }
  }

  //Private Methods

  private function validate_scope($scope){
    if (!allow::is_allowed($scope, allow::ADMINISTRATOR())){
      $this->response = allow::denied($scope);
      return false;
    }
    return true;
  }

}

?>
