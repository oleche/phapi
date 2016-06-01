<?php
include_once MY_DOC_ROOT . '/config/DB/Entity.php';

class ApiFieldType extends Entity{
  private $api_field_type = [
      'id' => [ 'type' => 'int', 'pk' => true ],
      'name' => [ 'type' => 'string', 'length' => 75 ],
      'regex' => [ 'type' => 'string', 'length' => 800 ]
  ];

  public function __construct(){
    parent::__construct($this->api_field_type, get_class($this));
  }
}

?>
