<?php
/**
 * vms data sync iask(chunsheng) data
 * 
 * @author shouqiang <shouqiang@staff.sina.com.cn>
 * @version 2016.02.16
 */

class SyncIaskVplay
{
	
	const APPNAME = 'ivms';

	/**
	 * A class instance variable
	 * @var null
	 */
	public static $_instance = null;

	/**
	 * sync vid => videoId
	 * @var array
	 */
	protected $_vidAndVideoIdMap = array();

	/**
	 * Post data(FYI VMS message queue list 通知春生vplay_update接口的) 
	 * @var array
	 */
	protected $_postDatas = array();

	/**
	 * Post url
	 * @var string
	 */
	protected $_postUrl = 'http://172.16.78.87/update_vplay.php';


	/**
	 * The SyncIaskVplay instance
	 * @return SyncIaskVplay
	 */
	public static function instance()
	{
		if (self::$_instance == null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	protected function __construct()
	{

	}

	/**
	 * set $_vidAndVideoIdMap by vid
	 * @param array $vids 
	 */
	public function setVidAndVideoIdMapByVids($vids = array())
	{
		if (count($vids) < 1) {
			exit(1); 
		}

		$vids = array_unique($vids);

		$url = "http://i.s.video.sina.com.cn/video/getVideoIdByVid";
		foreach($vids as $vid) {
			$params = array(
				'appname' => self::APPNAME,
				'vid' => $vid
			);
			$response = $this->_httpGet($url, $params);
			$response = json_decode($response, true);
			if ($response['code'] == 1) {
				$this->_vidAndVideoIdMap[$vid] = $response['data']['video_id'];
			} else {
				echo "$vid is not found videoId, then $vid sync failed.\n";
			}
		}

		return $this;
	}

	/**
	 * set $_vidAndVideoIdMap by videoIds
	 * @param array $vids 
	 */
	public function setVidAndVideoIdMapByVideoIds($videoIds = array())
	{
		foreach ($videoIds as $videoId) {
			$videoFiles = $this->_vmsApiInvode('video', 'getVideoFilesByVideoId', array('videoId' => $videoId));
			if (count($videoFiles) < 1) {
				echo "video files does not exists! Please check the videoId:$videoId\n";
				exit();
			}
			print_r($videoFiles);
			foreach ($videoFiles as $videoFile) {
				$this->_vidAndVideoIdMap[$videoFile['file_id']] = $videoId;
			}
		}
		print_r($this->_vidAndVideoIdMap);
		return $this;
	}

	/**
	 * The main function
	 * 
	 * @return void
	 */
	public function execute()
	{
		$this->_organizeData();
		foreach($this->_postDatas as $postData) {

			$curl = curl_init();

			curl_setopt($curl, CURLOPT_URL, $this->_postUrl);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData));

			$result = curl_exec($curl);
			curl_close($curl);

			$result = json_decode($result, true);
			if ($result['code'] == 1) {
				echo "Sync data successfully! vid: " . $postData['vid'] . " \n";
			} else {
				echo "Sync data failed! vid: " . $postData['vid'] . " \n";
			}
		}
	}

	/**
	 * organize Data
	 *
	 * FIY: /apps/bala/QMessage/Provider/Vplay.php
	 * 	
	 * @return array
	 */
	protected function _organizeData()
	{
		foreach ($this->_vidAndVideoIdMap as $vid => $videoId) {
			// get Video Information
			$video = $this->_vmsApiInvode('video', 'getVideoByVideoId', array('videoId' => $videoId));
			if (count($video) != 1) {
				echo "videoId: $videoId is not existed!\n";
				exit();
			}
			$video = $video[0];
			
			// get userId, userName
			$userName = '';
			$userId = 0;
			if($video['account_id'] > 0) {
				$userId = $video['account_id'];
				$account = $this->_vmsApiInvode('account', 'getAccountByAccountId', array('accountId' => $video['account_id']));
				$userName = isset($account[0]) ? $account[0]['name'] : '';	
			}else {
				/**
				 * @todo pm continue
				 */
				$userId = $video['user_id'];
				$user = Weibo_Client::factory('User')->getUserByUserId($video['user_id'])->getObject('Model_Weibo_User')->current();
				$userName = $user ? $user->getUserName() : '';
			}

			// get media information
			$mediaTag = $this->_vmsApiInvode('video', 'generateMediaTag', array('videoId' => $videoId));
			$mediaTag = $mediaTag ? $mediaTag[0] : '';
			
			$channel = $this->_vmsApiInvode('channel', 'getChannelByChannelId', array('channelId' => $video['channel_id']));
			if($channel) {
				$channel = $channel[0];
				$channel = explode(',', $channel['path']);
			}else {
				$channel = array();
			}
			$channelId1 = isset($channel[0]) ? $channel[0] : 0;
			$channelId2 = isset($channel[1]) ? $channel[1] : 0;
			$channelId3 = isset($channel[2]) ? $channel[2] : 0;
			$channelId4 = isset($channel[3]) ? $channel[3] : 0;		

			$filterType = 0;
			if($video['media_id'] > 0) {
				$media = $this->_vmsApiInvode('media', 'getMediaByMediaId', array('mediaId' => $video['media_id']));
				$filterType = $media ? $media[0]['filter_ip'] : 0;
			}

			$message = array(
				'action' => 'update',
				'vid' => $vid,
				'user_id' => $userId,
				'user_name' => $userName,
				'media_tag' => $mediaTag,
				'channel_id1' => $channelId1,
				'channel_id2' => $channelId2,
				'channel_id3' => $channelId3,
				'channel_id4' => $channelId4,
				'filter_type' => $filterType,
				'name' => $video['title'],
				'user_ip' => $video['ip'],
				'approve_status' => $video['approve_status'],
				'description' => $video['description'],
				'status' => $video['status']
			);

			$this->_postDatas[] = $message;
		}
		
	}

	/**
	 * vms bala interface
	 * @param  string $className 
	 * @param  string $method    
	 * @param  array  $arguments 
	 * @return string           
	 */
	protected function _vmsApiInvode($className = '', $method = '', $arguments = array())
	{
		if (!$className || !$method || !$arguments) {
			echo "The parameters error, \$className = $className, \$method = $method, \$arguments = $arguments" . \n;
			exit();
		}

		$secureKey = 'adskeasla';

		$input = array(
			'className' => $className,
			'method' => $method,
			'arguments' => $arguments
		);
		$input = json_encode($input);

		$token = md5($secureKey . $input);
		$headers = array(
			"token: $token"
		);

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, 'http://api.vms.video.sina.com.cn/business/index');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_POST, 1);
    	curl_setopt($curl, CURLOPT_POSTFIELDS, $input);

    	$result = curl_exec($curl);
    	curl_close($curl);

    	return json_decode($result, true);
	}

	/**
	 * HTTP Get Request
	 * @param  string $url    
	 * @param  array  $params 
	 * @return string         
	 */
	protected function _httpGet($url, $params)
	{
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $url . "?" . http_build_query($params));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($curl);
		curl_close($curl);

		return $result;
	}


	/**
	 * HTTP Post Request
	 * @param  string $url    
	 * @param  array  $params 
	 * @return string         
	 */
	protected function _httpPost($url, $params)
	{
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));

		$result = curl_exec($curl);
		curl_close($curl);

		return $result;
	}

	public function __destruct()
	{

	}

}



// invoking class by parameters vids
/**
$vids = array(139790366, 139790368);
SyncIaskVplay::instance()
	->setVidAndVideoIdMapByVids($vids)
	->execute();
**/

// invoking class by parameters videoIds
$videoIds = array(250484380);
SyncIaskVplay::instance()
	->setVidAndVideoIdMapByVideoIds($videoIds)
	->execute();