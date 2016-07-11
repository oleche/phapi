<?php
include_once MY_DOC_ROOT . '/config/DB/Entity.php';
include_once 'usertype.php';

class User extends Entity{
  private $user;

  public function __construct(){
    $this->user = [
        'username' => [ 'type' => 'string', 'length' => 70, 'unique' => true, 'pk' => true ],
        'name' => [ 'type' => 'string', 'length' => 45, 'unique' => true ],
        'lastname' => [ 'type' => 'string', 'length' => 45, 'unique' => true ],
        'email' => [ 'type' => 'string', 'length' => 70, 'unique' => true ],
        'fbid' => [ 'type' => 'string', 'length' => 100, 'unique' => true ],
        'googleid' => [ 'type' => 'string', 'length' => 100, 'unique' => true ],
        'avatar' => [ 'type' => 'text', 'nullable' => true ],
        'phone' => [ 'type' => 'string', 'length' => 32 ],
        'password' => [ 'type' => 'string', 'length' => 32 ],
        'enabled' => [ 'type' => 'boolean'],
        'created_at' => [ 'type' => 'datetime' ],
        'updated_at' => [ 'type' => 'datetime' ],
        'type' => [ 'type' => 'int', 'foreign' => array('id', new UserType())]
    ];
    parent::__construct($this->user, get_class($this));
  }
}

?>
