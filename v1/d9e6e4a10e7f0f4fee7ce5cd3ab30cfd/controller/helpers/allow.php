<?php

class allow {
	private static $PROFILE = array('profile-owner', 'administrator', 'site-visitor');
	private static $PUBLISH = array('partner', 'administrator', 'profile-viewer', 'site-manager');
  private static $COMMUNICATE = array('administrator', 'profile-viewer', 'site-manager', 'profile-owner', 'partner');
	private static $SEARCH = array('administrator', 'profile-viewer', 'site-manager', 'partner');
	private static $REMOVE = array('administrator', 'site-manager');
	private static $VALIDATE = array('administrator', 'moderator', 'site-manager');
	private static $MODERATE = array('administrator', 'moderator');
	private static $REPORT = array('administrator', 'profile-viewer', 'partner', 'site-manager', 'moderator');
	private static $MANAGER = array('administrator', 'site-manager');
	private static $ANYONE = array('administrator', 'site-manager', 'profile-owner', 'site-visitor', 'profile-viewer', 'partner', 'moderator');
  private static $ADMINISTRATOR = array('administrator');

	public static function MANAGER(){
  	return self::$MANAGER;
	}

  public static function ADMINISTRATOR(){
  	return self::$ADMINISTRATOR;
	}

	public static function PUBLISH(){
		return self::$PUBLISH;
	}

	public static function COMMUNICATE(){
		return self::$COMMUNICATE;
	}

	public static function PROFILE(){
		return self::$PROFILE;
	}

	public static function SEARCH(){
		return self::$SEARCH;
	}

	public static function REMOVE(){
		return self::$REMOVE;
	}

	public static function VALIDATE(){
		return self::$VALIDATE;
	}

	public static function MODERATE(){
		return self::$MODERATE;
	}

	public static function REPORT(){
		return self::$REPORT;
	}

	public static function ANYONE(){
		return self::$ANYONE;
	}

	public static function is_allowed($scopes, $allow){
		$set = explode(',', $scopes);
		$r_values = true;
		foreach ($set as $value) {
			$value = trim($value);
			$r_values = ($r_values && in_array($value, $allow));
		}
		return $r_values;
	}

	public static function denied($scopes){
		$response = array();
		$response['code'] = 401;
		$response['type'] = 'error';
		$response['message'] = 'Cannot allow action under scopes: '.$scopes;
		return $response;
	}
}

?>
