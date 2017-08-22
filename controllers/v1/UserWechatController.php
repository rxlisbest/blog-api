<?php

namespace app\controllers\v1;

use Yii;
use app\models\UserWechat;
use app\models\SmsAliyun;
use app\models\User;
use linslin\yii2\curl;
use yii\rest\ActiveController;
use yii\web\HttpException;

class UserWechatController extends ActiveController
{
	public $modelClass = 'app\models\UserWechat';

	public function actions(){
		$actions = parent::actions();
		unset($actions['index'], $actions['view'], $actions['create'], $actions['update']);
		return $actions;
	}

	public function actionLogin(){
		$post = Yii::$app->request->post();

		$AppID = Yii::$app->params['wechat']['AppID'];
		$AppSecret = Yii::$app->params['wechat']['AppSecret'];

		$curl = new curl\Curl();
		$response = $curl->setGetParams([
					'appid' => $AppID,
					'secret' => $AppSecret,
					'js_code' => $post['code'],
					'grant_type' => 'authorization_code'
				])->get('https://api.weixin.qq.com/sns/jscode2session');
		$result = json_decode($response);
		if(isset($result->errcode)){
			throw new HttpException(500, $result->errmsg);
		}
		else{
			$user_wechat = UserWechat::findOne(['openid' => $result->openid]);
			if(!$user_wechat){
				$user_wechat = new UserWechat();
				$user_wechat->openid = $result->openid;
				$user_wechat->user_id = 0;
				$user_wechat->create_time = time();
				$user_wechat->update_time = time();
			}
			else{
				$user_wechat->update_time = time();
			}
			$user_wechat->nickName = $post['nickName'];
			$user_wechat->avatarUrl = $post['avatarUrl'];
			$user_wechat->country = $post['country'];
			$user_wechat->province = $post['province'];
			$user_wechat->city = $post['city'];
			$user_wechat->language = $post['language'];
			$user_wechat->save();

			$result->user_id = $user_wechat->user_id;
			return $result;
		}
	}

	public function actionRegister(){
		$post = Yii::$app->request->post();
		// 参数验证
		if(!isset($post['cellphone']) || !$post['cellphone']){
			throw new HttpException(400, "手机号不能为空");
		}
		if(!isset($post['code']) || !$post['code']){
			throw new HttpException(400, "验证码不能为空");
		}
		if(!isset($post['openid']) || !$post['openid']){
			throw new HttpException(400, "openid不能为空");
		}

		if(!preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $post['cellphone'])){
			throw new HttpException(400, "手机号格式不正确");
		}

		// 手机号验证
		$user = User::findOne(['cellphone' => $post['cellphone']]);
		if($user){
			// 微信号验证
			$user_wechat = UserWechat::findOne(['user_id' => $user->user_id]);
			if($user_wechat){
				throw new HttpException(400, "手机号已绑定");
			}
		}

		// 验证码验证
		$sms_config = Yii::$app->params['aliyun']['sms'];
		$where = ['RecNum' => $post['cellphone'], 'TemplateCode' => $sms_config['TemplateCode']['register']['code']];
		$sms = SmsAliyun::find()->where($where)->orderBy('create_time DESC')->one();
		if(!$sms){
			throw new HttpException(400, "手机号未发送验证码");
		}
		$param = json_decode($sms->ParamString);
		if($param->code != $post['code']){
			throw new HttpException(400, "验证码输入不正确");
		}
		if(time() - $sms->create_time > $sms_config['expire']){
			throw new HttpException(400, "验证码已过期，请重新发送");
		}
		// 微信号验证
		$user_wechat = UserWechat::findOne(['openid' => $post['openid']]);
		if($user_wechat && $user_wechat->user_id > 0){
			throw new HttpException(400, "微信号已绑定");
		}

		if(!$user){
			$user = new User();
			$user->salt = $this->createSalt();
			$user->cellphone = $post['cellphone'];
			$user->username = '';
			$user->client_id = 'blog';
		}
		else{
			$user->cellphone = $post['cellphone'];
		}
		$user->save();

		if(!$user_wechat){
			$user_wechat = new UserWechat();
			$user_wechat->openid = $post['openid'];
			$user_wechat->user_id = $user->user_id;
			$user_wechat->create_time = time();
			$user_wechat->update_time = time();
		}
		else{
			$user_wechat->user_id = $user->user_id;
			$user_wechat->update_time = time();
		}

		$result = $user_wechat->save();
		if($result){
			return $user_wechat;
		}
		else{
			throw new HttpException(500, "操作失败");
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
