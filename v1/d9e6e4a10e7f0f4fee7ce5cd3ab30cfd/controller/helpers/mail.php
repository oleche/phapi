<?php
include_once MY_DOC_ROOT . "/lib/beanstalkd/Client.php";

use Beanstalk\Client;

class mailHelper{
  private $beanstalkHost;
  private $beanstalkTube;
  private $data;
  public function __construct($type, $recipient){
    $config = parse_ini_file(MY_DOC_ROOT."/config/config.ini");
    $this->beanstalkHost = $config['beanstalk_host'];
    $this->beanstalkTube = $config['beanstalk_tube'];
    $this->data = array();
    $this->data['message_type'] = $type;
    $this->data['receipient'] = $recipient;
  }

  public function addMessageParamerter($parameter,$value){
    $this->data[$parameter]=$value;
  }

  public function publishMessage(){
    $message = json_encode($this->data,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    $beanstalkParams = [
			'host' => $this->beanstalkHost
		];

    try {
      $beanstalk = new Client($beanstalkParams);

      $beanstalk->connect();
      $beanstalk->useTube($this->beanstalkTube);
      $beanstalk->put(
          1, // Give the job a priority of 23.
          0,  // Do not wait to put job into the ready queue.
          3000, // Give the job 1 minute to run.
          $message // The job's body.
      );
      $beanstalk->disconnect();
    } catch (Exception $e) {
      return false;
    }

    return true;
  }
}

?>
