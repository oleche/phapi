<?php
include_once MY_DOC_ROOT . '/config/DB/Entity.php';

class Scope extends Entity{
  private $scope = [
      'name' => [ 'type' => 'string', 'length' => 45, 'unique' => true, 'pk' => true ],
      'level' => [ 'type' => 'int' ],
      'priority' => [ 'type' => 'int' ]
  ];

  public function __construct(){
    parent::__construct($this->scope, get_class($this));
  }
}

?>
