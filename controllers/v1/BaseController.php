<?php

namespace app\controllers\v1;

use Yii;
use app\models\UserWechat;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use conquer\oauth2\TokenAuth;

class BaseController extends ActiveController
{
	public $modelClass = 'app\models\Article';

	public function behaviors(){
		if(Yii::$app->request->method == 'GET'){ // GET方法取消身份验证
			$behaviors = [];
		}
		else{
			$behaviors = $this->getBehaviors();
		}
		return ArrayHelper::merge(parent::behaviors(), $behaviors);
	}

	public function paginationData($models, $pagination){
		$pages = array();
		$page_size = $pagination->getPageSize();
		$total_count = $pagination->totalCount;
		$pages['totalPage'] = ceil($total_count/$page_size);
		$pages['page'] = $pagination->getPage();
		return ['pages' => $pages, 'models' => $models];
	}

	public function getBehaviors(){
		// 增加微信登录判断
		$headers = Yii::$app->request->headers;
		$authHeader = $headers->get('Authorization');
		if (preg_match("/^Openid\\s+(.*?)$/", $authHeader, $matches)) { // 微信登录
			$openid = $matches[1];
			$user_wechat = UserWechat::findOne(['openid' => $openid]);
			if($user_wechat && $user_wechat->user_id > 0){
				return [];
			}
		}
		return $behaviors = [
			'tokenAuth' => [
				'class' => \conquer\oauth2\TokenAuth::className(),
			],
		];
	}

	public function getUserId(){
		// 增加微信登录判断
		$headers = Yii::$app->request->headers;
		$authHeader = $headers->get('Authorization');
		$user_id = 0;
		if (preg_match("/^Openid\\s+(.*?)$/", $authHeader, $matches)) { // 微信登录
			$openid = $matches[1];
			$user_wechat = UserWechat::findOne(['openid' => $openid]);
			if($user_wechat && $user_wechat->user_id > 0){
				return $user_wechat->user_id;
			}
			else{
				throw new HttpException(401, 'The openid provided is invalid.');
			}
		}
		else{
			$token_auth = new TokenAuth();
			return $token_auth->getAccessToken()->user_id;
		}
	}
}
