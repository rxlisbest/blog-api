<?php

namespace app\controllers\v1;

use Yii;
use app\models\UserWechat;
use app\models\User;
use linslin\yii2\curl;
use yii\rest\ActiveController;
use yii\web\HttpException;
use yii\captcha\CaptchaAction;

class WechatController extends ActiveController
{
	public $modelClass = 'app\models\UserWechat';

	public function actions(){
		$actions = parent::actions();
		unset($actions['index'], $actions['view'], $actions['create'], $actions['update']);
		return $actions;
	}

	public function actionLogin(){
		$a = new CaptchaAction(1, $this);
//		@ header("Content-Type:image/png"); //创建一个图层
		echo $a->run();exit;
		exit;
		$result = $a->validate('rosafcx', true);
		var_dump($result);
		exit;
		$get = Yii::$app->request->get();

		$AppID = Yii::$app->params['wechat']['AppID'];
		$AppSecret = Yii::$app->params['wechat']['AppSecret'];

		$curl = new curl\Curl();
		$response = $curl->setGetParams([
					'appid' => $AppID,
					'secret' => $AppSecret,
					'js_code' => $get['code'],
					'grant_type' => 'authorization_code'
				])->get('https://api.weixin.qq.com/sns/jscode2session');
		$result = json_decode($response);
		if(isset($result->errcode)){
			throw new HttpException(500, $result->errmsg);
		}
		else{
			$user_wechat = UserWechat::findOne(['openid' => $result->openid]);
			if(!$user_wechat){
				$user = new User();
				$user->salt = $this->createSalt();
				$user->username = '';
				$user->client_id = 'blog';
				$user->save();

				$user_wechat = new UserWechat();
				$user_wechat->openid = $result->openid;
				$user_wechat->user_id = $user->attributes['user_id'];
				$user_wechat->create_time = date('Y-m-d H:i:s');
				$user_wechat->update_time = date('Y-m-d H:i:s');
				$user_wechat->save();
			}
			$result->user_id = $user_wechat->user_id;
			return $result;
		}
	}

	public function createSalt($length = 6){
		// 密码字符集，可任意添加你需要的字符
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$salt = '';
		for ( $i = 0; $i < $length; $i++ ){
			$salt .= $chars[ mt_rand(0, strlen($chars) - 1) ];
		}
		return $salt;
	}
}
