<?php
class Weibo_Client_Result {

	public static function factory($type, $result, $asObject = Null) {
		if($type == Weibo_Client::RETURN_JSON) {
			return $result;
		}

		if($type == Weibo_Client::RETURN_ARRAY) {
			return json_decode($result, TRUE);
		}

		if($type == Weibo_Client::RETURN_OBJECT) {
			return new Weibo_Client_Result_Object(array(json_decode($result, TRUE)), $asObject);
		}
	}
}