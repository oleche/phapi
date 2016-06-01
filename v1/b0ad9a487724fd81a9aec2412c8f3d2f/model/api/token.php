<?php
include_once MY_DOC_ROOT . '/config/DB/Entity.php';
include_once 'client.php';

class ApiToken extends Entity{
  private $api_token;

  public function __construct(){
    $this->api_token = [
        'id' => [ 'type' => 'int', 'pk' => true ],
        'token' => [ 'type' => 'string', 'length' => 128 ],
        'created_at' => [ 'type' => 'datetime' ],
        'expires' => [ 'type' => 'int' ],
        'enabled' => [ 'type' => 'boolean' ],
        'client_id' => [ 'type' => 'string', 'length' => 32, 'pk' => true, 'foreign' => array('client_id', new ApiClient()) ],
        'updated_at' => [ 'type' => 'datetime' ],
        'scopes' => [ 'type' => 'string', 'length' => 250 ],
        'timestamp' => [ 'type' => 'string', 'length' => 128 ],
    ];
    parent::__construct($this->api_token, get_class($this));
  }
}

?>
