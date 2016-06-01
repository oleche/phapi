<?php
include_once MY_DOC_ROOT . '/config/DB/Entity.php';
include_once MY_DOC_ROOT . '/model/scope.php';
include_once 'client.php';

class ApiClientScope extends Entity{
  private $api_client_scope;

  public function __construct(){
    $this->api_client_scope = [
        'id_scope' => [ 'type' => 'string', 'length' => 32, 'pk' => true, 'foreign' => array('name', new Scope()) ],
        'id_client' => [ 'type' => 'string', 'length' => 32, 'pk' => true, 'foreign' => array('client_id', new ApiClient()) ]
    ];
    parent::__construct($this->api_client_scope, get_class($this));
  }
}

?>
