<?php
	include 'DB/DBManager.php';
	include 'DB/DataBase.class.php';

	include MY_DOC_ROOT . '/model/api/form.php';

	abstract class GCConfig{
		protected $ipp;
		protected $fbappid;
		protected $fbsecret;

		protected $request;

		private $connection;

    //controller vars
    public $pagination_link = "";
    public $page;
    public $per_page;

		//response information
		public $err;
		public $response;

		//internalDB
		private $api_form;

    public function __construct(){

			$config = parse_ini_file("config.ini");

			$this->app_secret = $config['app_secret'];

			$this->fbappid = $config['fbapp'];
			$this->fbsecret = $config['fbsecret'];
	    $this->ipp = $config['ipp'];

			$this->api_form = new ApiForm();
		}

		public function setRequest($request){
			$this->request = $request;
		}

		public abstract function doPOST();
		public abstract function doGET();
		public abstract function doPUT();
		public abstract function doDELETE();

		//Private Methods
		private function filter_gets($ignored = array()){
			$query = "";
			$count = 0;
			foreach ($_GET as $key => $value) {
				if (!in_array($key, $ignored)){
					$query .= $key.'='.$value;
				}
				if ($count > 0){
					$query .= "&";
				}
				$count++;
			}

			return $query;
		}

		//Protected methods
    protected function paginate($class){
        if ($class->pages > 1){
            $this->pagination_link = "Link: ";
		if (($this->page+1) > 1){
			$this->pagination_link .= "<".$_SERVER['SCRIPT_URI']."?page=".$this->page;
			$rest = $this->filter_gets(array('page','request'));
			if ($rest != "")
				$this->pagination_link .= "&$rest";
			$this->pagination_link .= '>; rel="prev",';
		}
		if (($this->page+1) < $class->pages){
			$this->pagination_link .= "<".$_SERVER['SCRIPT_URI']."?page=".($this->page+2);
			$rest = $this->filter_gets(array('page','request'));
			if ($rest != "")
				$this->pagination_link .= "&$rest";
			$this->pagination_link .= '>; rel="next",';
		}
		if (($this->page+1) <= $class->pages){
			$this->pagination_link .= "<".$_SERVER['SCRIPT_URI']."?page=1";
			$rest = $this->filter_gets(array('page','request'));
			if ($rest != "")
				$this->pagination_link .= "&$rest";
			$this->pagination_link .= '>; rel="first",';
		}
		if (($this->page+1) >= 1){
			$this->pagination_link .= "<".$_SERVER['SCRIPT_URI']."?page=".$class->pages;
			$rest = $this->filter_gets(array('page','request'));
			if ($rest != "")
				$this->pagination_link .= "&$rest";
			$this->pagination_link .= '>; rel="last",';
		}
    	}
    }

    protected function set_pagination(&$class, $params){
      $this->per_page = (isset($params['per_page']) && trim($params['per_page']) != '')?$params['per_page']:$this->ipp;
    	$this->page = (isset($params['page']) && trim($params['page']) != '')?$params['page']:1;
			if ($this->page <= 0)
				$this->page = 0;
			else {
				$this->page -= 1;
			}
			$class->set_ipp($this->per_page);
    }

		protected function validate_fields($fields, $endpoint){
			$available = array();
			$rvalue = true;
			$this->response['message'] = array();
			if (is_array($fields)){
				foreach ($fields as $k => $field) {
					$available[] = $k;
					if (trim($field) == ''){
						$rvalue = false;
						$message = array();
						$message['field'] = $k;
						$message['message'] = 'Is empty';
						$this->response['message'][] = $message;
						$this->response['code'] = 2;
						$this->response['http_code'] = 422;
					}
				}
			}
			$q_list = $this->api_form->fetch(" endpoint LIKE '$endpoint' ");
			if (count($q_list) > 0){
	            $i = 0;
				foreach ($q_list as $q_item) {
					$i++;
					if (in_array($q_item->columns['field'], $available)){
						//regex validation
						if ( !preg_match( $q_item->columns['id_type']['regex'], $fields[$q_item->columns['field']] ) ) {
							$rvalue = false;
							$message = array();
							$message['field'] = $q_item->columns['field'];
							$message['message'] = 'Do not match validation type: '.$q_item->columns['id_type']['name'];
							$message['format'] = $q_item->columns['id_type']['regex'];
							$this->response['message'][] = $message;
							$this->response['code'] = 2;
							$this->response['http_code'] = 422;
						}
					}else{
						if ($q_item->columns['required']){
							$rvalue = false;
							$message = array();
							$message['field'] = $q_item->columns['field'];
							$message['message'] = 'Is required';
							$this->response['message'][] = $message;
							$this->response['code'] = 2;
							$this->response['http_code'] = 422;
						}
					}
				}
			}else{
				$this->response['request'] = $_POST;
				$this->response['message'] = 'Fields definition error';
				$this->response['code'] = 2;
				$this->response['http_code'] = 500;
				return false;
			}
			return $rvalue;
		}
	}

?>
