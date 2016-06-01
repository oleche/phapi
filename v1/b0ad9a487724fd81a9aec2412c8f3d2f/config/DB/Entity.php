<?php
include_once 'DBManager.php';
include_once 'DataBase.class.php';

class Entity extends DBManager{
  protected $ipp;
  public $table;

  public $connection;
  private $mapping;

  public function __construct($map, $table_name, $configfile = "/var/www/html/cv/v1/b0ad9a487724fd81a9aec2412c8f3d2f/config/config.ini"){
    $config = parse_ini_file($configfile);

    $this->ipp = $config['ipp'];

    $this->connection = Database::getInstance($configfile);

    $col = array();//'id', 'name', 'format', 'max_size', 'max_dimensions', 'mime', 'type');
    $key = array();//'id');
    $foreign = null;//'user_id' => array('idusuario', $this->user));

    //Explore the mapping for columns, keys and foreign keys
    $this->mapping = $map;

    if (!is_null($this->mapping)){
			foreach ($this->mapping as $k => $map) {
				$col[] = $k;
        if (is_array($map))
          if (isset($map['pk']) && $map['pk']){
            $key[] = $k;
          }
          if (isset($map['foreign']) && is_array($map['foreign'])){
            if (is_null($foreign))
              $foreign = array();
            $foreign[$k] = $map['foreign'];
          }
			}
		}
    //end of exploration
    $table = $this->from_camel_case($table_name);

    parent::__construct($this->connection, $table, $col, $key, $foreign);


    $a = get_class_vars(get_class($this));
  }

  private function from_camel_case($input) {
    preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
    $ret = $matches[0];
    foreach ($ret as &$match) {
      $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
    }
    return implode('_', $ret);
  }
}

?>
