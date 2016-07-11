<?php
class GenericGet
{
  private $model;
  private $id;
  private $session;
  public $response;

  public function __construct($object, $session, $id = null){
    $this->model = $object;
    $this->response = array();
    $this->id = $id;
    $this->session = $session;
  }

  public function get($checkUser = false){
    $map = $this->model->get_mapping();
    if ($this->validateScope()){
      if ($checkUser){
        $q_list = $this->model->fetch("username = '$this->id'",false,array('username'),false);
      }else{
        $pk = "";

        $query = $this->model->assembly_search($_GET);

    		$q_list = array();

        foreach ($map as $k => $map) {
          if (isset($map['pk']) && $map['pk'] == true){
            $pk = $k;
            break;
          }
        }

        if (is_null($this->id) || $this->id == ""){
          $this->model->set_pagination(true);
          $this->model->set_paging($this->model, $_GET);
    			$q_list = $this->model->fetch($query,false,array($pk),false,$this->model->page);
    		}else{
    			if ($this->model->fetch_id(array($pk=>$this->id),null,true,$query))
    				$q_list[] = $this->model;
    		}
      }

      if ((count($q_list) == 0) || (!$q_list)){
        $this->response['type'] = 'error';
  			$this->response['message'] = 'Cannot retrieve data';
  			$this->response['code'] = 422;
  		}else{
        $this->model->paginate($this->model);

  			$this->response['code'] = 200;
  			$this->response[get_class($this->model)] = array();
  			foreach ($q_list as $k => $q_item) {
  				$this->response[get_class($this->model)][] = $q_item->columns;
  			}
  		}
    }
  }

  public function getPaginationLink(){
    return $this->model->pagination_link;
  }

  private function validateScope(){
    if (!allow::is_allowed($this->session->session_scopes, allow::ANYONE())){
      $this->response = allow::denied($this->session->session_scopes);
      return false;
    }
    return true;
  }
}
?>
