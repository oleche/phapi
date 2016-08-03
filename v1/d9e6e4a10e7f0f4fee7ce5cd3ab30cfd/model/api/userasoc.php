<?php
include_once MY_DOC_ROOT . '/config/DB/Entity.php';
include_once MY_DOC_ROOT . '/model/user.php';
include_once 'client.php';

class ApiUserAsoc extends Entity{
  private $api_user_asoc;

  public function __construct(){
    $this->api_user_asoc = [
      'username' => [ 'type' => 'string', 'length' => 70, 'pk' => true, 'foreign' => array('username', new User()) ],
      'client_id' => [ 'type' => 'string', 'length' => 32, 'pk' => true, 'foreign' => array('client_id', new ApiClient()) ]
    ];
    parent::__construct($this->api_user_asoc, get_class($this));
  }
}

?>
