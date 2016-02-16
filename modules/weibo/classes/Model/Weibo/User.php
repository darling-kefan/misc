<?php
class Model_Weibo_User {
	/**用户UID*/
	private $_id = 0;
	/**用户昵称*/
	private $_screenName = '';
	/**友好显示名称*/
	private $_name = '';
	/**用户所在地*/
	private $_location = '';
	/**用户个人描述*/
	private $_description = '';
	/**用户博客地址*/
	private $_url = '';
	/**用户的微博统一URL地址*/
	private $_profileUrl = '';
	/**用户的个性化域名*/
	private $_domain = '';
	/**用户的微号*/
	private $_weihao = '';
	/**性别，m：男、f：女、n：未知*/
	private $_gender = '';

	/**粉丝数*/
	private $_followersCount = 0;
	/**关注数*/
	private $_friendsCount = 0;
	/**用户的互粉数*/
	private $_biFollowersCount = 0;

	/**微博数*/
	private $_statuesCount = 0;
	/**收藏数*/
	private $_favouritesCount = 0;

	/**用户创建（注册）时间 时间戳*/
	private $_createAt = 0;

	/*是否允许所有人给我发私信，true：是，false：否*/
	private $_allowAllActMsg = FALSE;
	/**是否允许标识用户的地理位置，true：是，false：否*/
	private $_geoEnabled = TRUE;
	/**是否是微博认证用户，即加V用户，true：是，false：否*/
	private $_verified = FALSE;
	/**认证原因*/
	private $verfiedReason = '';

	/**用户的最近一条微博信息字段*/
	private $_status = Null;

	/**是否允许所有人对我的微博进行评论，true：是，false：否*/
	private $_allowAllComment = TRUE;

	/**用户头像地址（中图），50×50像素*/
	private $_profileImageUrl = '';
	/**用户头像地址（大图），180×180像素*/
	private $_avatarLarge = '';
	/**用户头像地址（高清），高清头像原图*/
	private $_avatarHd = '';

	/**该用户是否关注当前登录用户，true：是，false：否*/
	private $_followMe = FALSE;

	/**用户的在线状态，0：不在线、1：在线*/
	private $_onlineStatus = 0;

	public function __construct(array $data) {
		$this->_id = isset($data['id']) ? (int) $data['id'] : 0;
		$this->_screenName = isset($data['screen_name']) ? (string) $data['screen_name'] : '';
		$this->_name = isset($data['name']) ? (string) $data['name'] : '';
		$this->_location = isset($data['location']) ? (string) $data['location'] : '';
		$this->_description = isset($data['description']) ? (string) $data['description'] : '';
		$this->_url = isset($data['url']) ? (string) $data['url'] : '';
		$this->_profileUrl = isset($data['profile_url']) ? (string) $data['profile_url'] : '';
		$this->_domain = isset($data['domain']) ? (string) $data['domain'] : '';
		$this->_weihao = isset($data['weihao']) ? (string) $data['weihao'] : '';
		$this->_gender = isset($data['gender']) ? (string) $data['gender'] : 'n';

		$this->_followersCount = isset($data['followers_count']) ? (int) $data['followers_count'] : 0;
		$this->_friendsCount = isset($data['friends_count']) ? (int) $data['friends_count'] : 0;
		$this->_biFollowersCount = isset($data['bi_followers_count']) ? (int) $data['bi_followers_count'] : 0;
		$this->_statusCount = isset($data['status_count']) ? (int) $data['status_count'] : 0;
		$this->_favouritesCount = isset($data['favourites_count']) ? (int) $data['favourites_count'] : 0;
		$this->_createAt = isset($data['create_at']) ? strtotime($data['create_at']) : 0;
		$this->_allowAllActMsg = isset($data['allow_all_act_msg']) ? (bool) $data['allow_all_act_msg'] : TRUE;
		$this->_geoEnabled = isset($data['geo_enabled']) ? (bool) $data['geo_enabled'] : TRUE;
		$this->_verified = isset($data['verified']) ? (bool) $data['verified'] : FALSE;
		$this->_verifiedReason = isset($data['verifiedReason']) ? (string) $data['verifiedReason'] : '';

		$this->_profileImageUrl = isset($data['profile_image_url']) ? (string) $data['profile_image_url'] : '';
		$this->_avatarLarge = isset($data['avatar_large']) ? (string) $data['avatar_large'] : '';
		$this->_avatarHd = isset($data['avatar_hd']) ? (string) $data['avatar_hd'] : '';
	}

	public function getUserId() {
		return $this->_id;
	}

	public function getUserName() {
		return $this->_screenName;
	}

	public function getName() {
		return $this->_name;
	}

	public function getLocation() {
		return $this->_location;
	}

	public function getDescription() {
		return $this->_description;
	}

	public function getUrl() {
		return $this->_url;
	}

	public function getProfileUrl() {
		return 'http://weibo.com/'. $this->_profileUrl;
	}

	public function getDomain() {
		return $this->_domain;
	}

	public function getWeihao() {
		return $this->_getWeihao;
	}

	public function getGender() {
		return $this->_gender;
	}

	public function getFollowersCount() {
		return $this->_followersCount;
	}

	public function getCreateAt() {
		return $this->_createAt;
	}

	public function getStatusCount() {
		return $this->_statusCount;
	}

	public function getProfileImageUrl() {
		return $this->_profileImageUrl;
	}

	public function getAvatarHd() {
		return $this->_avatarHd;
	}

	public function getAvatarLarge() {
		return $this->_avatarLarge;
	}

}