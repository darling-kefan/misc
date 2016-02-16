<?php
/**
 * 微博接口认证类
 */
class OAuth2 {
	private $_clientId = '';

	private $_clientSecret = '';

	private $_accessToken = '';

	private static $_expiration = 0;

	private $_uid = 0;

	private $_redirectUri = '';

	private static $_instance = Null;

	public static function instance($clientId, $clientSecret) {
		if(self::$_instance === Null) {
			self::$_instance = new self($clientId, $clientSecret);
		}
	}

	public function __construct($clientId, $clientSecret) {
		$this->_clientId = $clientId;
		$this->_clientSecret = $clientSecret;
	}

	/**
	 * 请求用户授权Token
	 * @param string $redirectUrl 回调地址
	 * @param string $scope 申请scope权限所需参数，可一次申请多个scope权限，用逗号分隔
	 * @param string $state 用于保持请求和回调的状态，在回调时，会在Query Parameter中回传该参数
	 * @param string $display e.g: 	default:默认，适用于web浏览器，
	 *							   	mobile:移动终端的授权页面，适用于支持html5的手机
	 *								wap	wap版授权页面，适用于非智能手机。
	 *								client	客户端版本授权页面，适用于PC桌面应用。
	 *								apponweibo	默认的站内应用授权页，授权后不返回access_token，只刷新站内应用父框架。
	 * @param Boolean forcelogin 是否强制用户重新登录，true：是，false：否。默认false。
	 * @param string language 授权页语言，缺省为中文简体版，en为英文版。英文版测试中，开发者任何意见可反馈至 @微博API
	 */
	public function authorize($redirectUri, $scope = Null, $state = '', $display = 'default', $forcelogin = FALSE, $language = Null) {
		$url = 'https://api.weibo.com/oauth2/authorize';

		$params = array();
		$params['client_id'] = self::$_clientId;
		$params['redirect_uri'] = $redirectUri;

		$params['response_type'] = 'code';
		if(!is_null($scope)) {
			$params['scope'] = $scope;
		}

		if(!empty($state)) {
			$params['state'] = $state;
		}

		$params['display'] = $display;
		$params['forcelogin'] = $forcelogin;

		if(!is_null($language)) {
			$params['language'] = $language;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$response = curl_exec($ch);
		if($error = curl_error($ch)) {
			return $error;
		}
		curl_close($ch);

		return TRUE;
	}

	/**
	 * 获取access_token
	 * @param string $code
	 *
	 * @return string
	 */
	public function getAccessToken($code) {
		if(self::$_expiration > time()) {
			return $this->_accessToken;
		}

		$url = 'https://api.weibo.com/oauth2/access_token';

		$params = array();
		$params['client_id'] = self::$_clientId;
		$params['client_secret'] = self::$_clientSecret;
		$params['grant_type'] = 'authorization_code';
		$params['code'] = $code;
		$params['redirect_uri'] = self::$_redirectUri;
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		$response = curl_exec($ch);
		$response = json_decode($response);

		$this->_accessToken = $response['access_token'];
		self::$_expiration = time() + $response['expire_in'];
		$this->_uid = $response['uid'];

		return $this->_accessToken;
	}

	/**
	 * 获取被授权者的用户ID
	 */
	public function getUid() {
		return $this->_uid;
	}

	public static function getExpiration() {
		return self::$_expiration;
	}
}