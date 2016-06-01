<?php
include_once MY_DOC_ROOT . '/config/DB/Entity.php';

class UserType extends Entity{
  private $user_type = [
      'id' => [ 'type' => 'int', 'unique' => true, 'pk' => true ],
      'name' => [ 'type' => 'string', 'length' => 32, 'unique' => true ],
      'priority' => [ 'type' => 'int', 'unique' => true ]
  ];

  public function __construct(){
    parent::__construct($this->user_type, get_class($this));
  }
}

?>
