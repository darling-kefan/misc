<?php
abstract class Weibo_Client {

	const RETURN_OBJECT = 1;
	
	const RETURN_ARRAY = 2;

	const RETURN_JSON = 3;

	protected static $_source = Null;

	protected static $_isOAuth2 = FALSE;

	protected static $_instance = Null;

	protected static $_initial = FALSE;

	protected $_result = Null;

	protected $_asObject = Null;

	public static function factory($name) {
		if(!self::$_initial) {
			self::$_source = Kohana::$config->load('weibo.AppKey');
			
			self::$_isOAuth2 = Kohana::$config->load('weibo.isOAuth2');
			
			if(!self::$_isOAuth2) {
				if(is_null(self::$_source)) {
					throw new Weibo_Exception('非OAuth2验证状态下必须设置source值');
				}
			}
			self::$_initial = TRUE;
		}

		$class = 'Weibo_Client_' . ucfirst(strtolower($name));

		if(!class_exists($class)) {
			throw new Weibo_Exception($class . " 类不存在");
		}

		return new $class();
	}

	public function getObject($className) {
		$this->_asObject = $className;
		return Weibo_Client_Result::factory(Weibo_Client::RETURN_OBJECT, $this->_result, $this->_asObject);
	}

	public function getArray() {
		return Weibo_Client_Result::factory(Weibo_Client::RETURN_ARRAY, $this->_result);
	}

	public function getJson() {
		return Weibo_Client_Result::factory(Weibo_Client::RETURN_JSON, $this->_result);
	}

	protected function curl($uri, $params = Null) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $uri);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		if(!is_null($params)) {
			if(!is_array($params)) {
				throw new Weibo_Exception("params参数必须传入数组");
			}
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		}

		$response = curl_exec($ch);
		return $response;	
	}
}