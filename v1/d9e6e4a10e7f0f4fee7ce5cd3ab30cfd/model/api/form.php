<?php
include_once MY_DOC_ROOT . '/config/DB/Entity.php';
include 'field.php';

class ApiForm extends Entity{

  private $api_form;

  public function __construct(){
    $this->api_form = [
       'id' => [ 'type' => 'int', 'pk' => true ],
       'endpoint' => [ 'type' => 'string', 'length' => 50 ],
       'method' => [ 'type' => 'string', 'length' => 10 ],
       'field' => [ 'type' => 'string', 'length' => 75 ],
       'id_type' => [ 'type' => 'int', 'foreign' => array('id', new ApiFieldType())],
       'sample' => [ 'type' => 'string', 'length' => 350 ],
       'internal' => [ 'type' => 'boolean' ],
       'required' => [ 'type' => 'boolean' ],
       'scopes' => [ 'type' => 'string', 'length' => 500 ]
   ];
    parent::__construct($this->api_form, get_class($this));
  }
}

?>
