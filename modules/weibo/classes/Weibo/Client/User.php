<?php
/**
 * 微博用户接口类
 * @author shaoyu3@staff.sina.com.cn
 */
class Weibo_Client_User extends Weibo_Client {
	/**
	 * 通过用户ID获取用户信息
	 * @param integer $userId
	 * @param string $accessToken OAuth2验证模式下需传该值
	 *
	 * @return array
	 */
	public function getUserByUserId($userId, $accessToken = Null) {
		$url =  "http://i2.api.weibo.com/2/users/show.json";

		$params = array();

		if(Weibo_Client::$_isOAuth2) {
			if(is_null($accessToken)) {
				throw new Weibo_Exception('accessToken不能为空');
			}
			$params['access_token'] = $accessToken;
		}else {
			$params['source'] = Weibo_Client::$_source;
		}

		$params['uid'] = $userId;

		$uri = $url . '?' . http_build_query($params);
		$this->_result = $this->curl($uri);
		return $this;
	}

	/**
	 * 根据用户名获取用户信息
	 * @param string $userName
	 * @param string $accessToken OAuth2验证模式下需传该值	
	 *
	 * @return array
	 */
	public function getUserByUserName($userName, $accessToken = Null) {
		$url =  "http://i2.api.weibo.com/2/users/show.json";

		$params = array();

		if(Weibo_Client::$_isOAuth2) {
			if(is_null($accessToken)) {
				throw new Weibo_Exception('accessToken不能为空');
			}
			$params['access_token'] = $accessToken;
		}else {
			$params['source'] = Weibo_Client::$_source;
		}

		$params['screen_name'] = $userName;

		$uri = $url . '?' . http_build_query($params);

		$this->_result = $this->curl($uri);

		return $this;
	}

	/**
	 * 通过个性化域名获取用户资料以及用户最新的一条微博
	 * @param $string $domain
	 * @param string $accessToken OAuth2验证模式下需传该值
	 * 
	 * @return array
	 */
	public function getUserByDomain($domain, $accessToken = Null) {
		$url = 'https://api.weibo.com/2/users/domain_show.json';

		$params = array();

		if(Weibo_Client::$_isOAuth2) {
			if(is_null($accessToken)) {
				throw new Weibo_Exception('accessToken不能为空');
			}
			$params['access_token'] = $accessToken;
		}else {
			$params['source'] = Weibo_Client::$_source;
		}

		$params['domain'] = $domain;

		$uri = $url . '?' . http_build_query($params);

		$this->_result = $this->curl($uri);

		return $this;
	}
}
