<?php
include_once MY_DOC_ROOT . '/config/DB/Entity.php';
include_once MY_DOC_ROOT . '/model/user.php';

class ApiClient extends Entity{
  private $api_client;

  public function __construct(){
    $this->api_client = [
        'client_id' => [ 'type' => 'string', 'length' => 32, 'pk' => true ],
        'client_secret' => [ 'type' => 'string', 'length' => 32 ],
        'email' => [ 'type' => 'string' ],
        'user_id' => [ 'type' => 'string', 'length' => 15, 'foreign' => array('username', new User()) ],
        'created_at' => [ 'type' => 'datetime' ],
        'updated_at' => [ 'type' => 'datetime' ],
        'enabled' => [ 'type' => 'boolean' ],
        'asoc' => [ 'type' => 'boolean' ]
    ];
    parent::__construct($this->api_client, get_class($this));
  }
}

?>
