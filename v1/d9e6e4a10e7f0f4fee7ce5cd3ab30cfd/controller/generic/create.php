<?php
include_once MY_DOC_ROOT . "/controller/helpers/allow.php";

class GenericCreate
{
  private $model;
  public $response;
  private $validScope;
  private $uniqueness;

  public function __construct($object, $uniqueness = null){
    $this->model = $object;
    $this->response = array();
    $this->validScope = allow::ADMINISTRATOR();
    $this->uniqueness = null;
    if (!is_null($uniqueness))
      $this->uniqueness = $uniqueness;
  }

  public function create($scope, $username = null){
    if ($this->validateScope($scope)){
      if (!is_null($this->uniqueness) && !$this->validateUniqueness($username)){
        return false;
      }

      $this->response = array();
      $theMap = $this->model->get_mapping();
      foreach ($theMap as $k => $map) {
        if (isset($map['postable']) && $map['postable'] == true){
          $this->model->columns[$k] = $_POST[$k];
        }
      }

      if (isset($username) && !is_null($username)){
        $this->model->columns['username'] = $username;
      }

      if (isset($theMap['updated_at'])){
        $this->model->columns['updated_at'] = date("Y-m-d H:i:s");
      }

      if (isset($theMap['created_at'])){
        $this->model->columns['created_at'] = date("Y-m-d H:i:s");
      }

	    $id = $this->model->insert();
      if (is_int($id)){
        $this->response[get_class($this->model)] = $this->model->columns;
        $this->response['code'] = 200;
      }else{
        $this->response['type'] = 'error';
        $this->response['title'] = 'Crear modelo';
        $this->response['message'] = 'Se ha producido el siguiente error: '.$this->model->err_data;
  			$this->response['code'] = 422;
      }
    }
  }

  public function setValidScope($scope){
    $this->validScope = $scope;
  }

  //Private Methods
  private function validateScope($scope){
    if (!allow::is_allowed($scope, $this->validScope)){
      $this->response = allow::denied($scope);
      return false;
    }
    return true;
  }

  private function validateUniqueness($username = null){
    $count = 0;
    $sql = "";
    foreach ($this->uniqueness as $value) {
      if ($count > 0){
        $sql .= " AND ";
      }
      $sql .= $value."=".((($this->model->GetType($_POST[$value]) == 'boolean'
      || $this->model->GetType($_POST[$value]) == 'float'
      || $this->model->GetType($_POST[$value]) == 'integer'
      || $this->model->GetType($_POST[$value]) == 'numeric'
      || $this->model->GetType($_POST[$value]) == 'NULL'))?'':"'")
        .(($this->model->GetType($_POST[$value]) == 'NULL')?'NULL':$_POST[$value])
      .((($this->model->GetType($_POST[$value]) == 'boolean'
      || $this->model->GetType($_POST[$value]) == 'float'
      || $this->model->GetType($_POST[$value]) == 'integer'
      || $this->model->GetType($_POST[$value]) == 'numeric'
      || $this->model->GetType($_POST[$value]) == 'NULL'))?'':"'");
    }

    if (isset($username) && !is_null($username)){
      if (trim($sql) != ""){
        $sql .= " AND username = '$username'";
      }
    }

    $q_list = $this->model->fetch($sql,false,null,false);
    if ((count($q_list) == 0) || (!$q_list)){
      return true;
    }
    $this->response['type'] = 'error';
    $this->response['title'] = 'Crear modelo';
    $this->response['message'] = 'El item no es unico.';
    $this->response['code'] = 422;

    return false;
  }

}

?>
