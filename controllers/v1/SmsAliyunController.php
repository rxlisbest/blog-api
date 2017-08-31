<?php

namespace app\controllers\v1;

use Yii;
//use linslin\yii2\curl;
use app\models\SmsAliyun;
use yii\web\HttpException;
use yii\captcha\CaptchaAction;

class SmsAliyunController extends BaseController
{
	public $modelClass = 'app\models\SmsAliyun';

	public function actions(){
		$actions = parent::actions();
		unset($actions['index'], $actions['view'], $actions['create'], $actions['update']);
		return $actions;
	}

	public function actionSend(){
		$get = Yii::$app->request->get();
//		// 图形验证码
//		if(!isset($get['captcha']) || !trim($get['captcha'])){
//			throw new HttpException(400, "验证码不能为空");
//		}
//		$captcha = new CaptchaAction(1, $this);
//		$result = $captcha->validate($get['captcha'], true);
//		if(!$result){
//			throw new HttpException(400, "验证码不正确");
//		}

		if(!isset($get['TemplateCode']) || !isset($get['RecNum'])){
			throw new HttpException(400, "参数错误");
		}

		$sms_config = Yii::$app->params['aliyun']['sms'];
		if(!isset($sms_config['TemplateCode'][$get['TemplateCode']])){
			throw new HttpException(400, "参数错误");
		}

		if(!preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $get['RecNum'])){
			throw new HttpException(400, "手机号格式不正确");
		}

		$template_code = $sms_config['TemplateCode'][$get['TemplateCode']];
		$query = SmsAliyun::find()->where(['>', 'create_time', strtotime(date('Y-m-d'))])->andWhere(['<', 'create_time', time()]);
		// 同一IP每天条数限制
		$x = clone $query;
		$where = ['ip' => Yii::$app->request->userIP];
		$send_count = $x->andWhere($where)->count();
		if($send_count >= 3*$template_code['daily_limit']){
			throw new HttpException(500, "同一IP超出每天限制数量");
		}
		// 同一模板每天条数限制
		$y = clone $query;
		$where = ['RecNum' => $get['RecNum']];
		$send_count = $y->andWhere($where)->andWhere(['TemplateCode' => $template_code['code']])->count();
		if($send_count >= $template_code['daily_limit']){
			throw new HttpException(500, "超出每天限制数量");
		}

		// 每天条数总数限制
		$z = clone $query;
		$send_count = $z->count();
		if($send_count >= $sms_config['daily_limit']){
			throw new HttpException(500, "超出每天限制总数量");
		}

		// 上次发短信时间间隔
		$where = ['RecNum' => $get['RecNum']];
		$last_sms = SmsAliyun::find()->where($where)->andWhere(['TemplateCode' => $template_code['code']])->orderBy('create_time DESC')->one();
		if($last_sms && time() - $last_sms->attributes['create_time'] < $sms_config['time_interval']){
			throw new HttpException(500, '发送时间间隔不能短于' .$sms_config['time_interval'].'s');
		}

		$param_string = $this->createParamString($get['TemplateCode']);
		$sms_aliyun = new SmsAliyun();
		$sms_aliyun->ParamString = $param_string;
		$sms_aliyun->RecNum = $get['RecNum'];
		$sms_aliyun->TemplateCode = $template_code['code'];
		$sms_aliyun->ip = Yii::$app->request->userIP;
		$sms_aliyun->create_time = time();
		$sms_aliyun->update_time = time();
		$result = $sms_aliyun->save();
		if($result){
			$curl = new curl\Curl();
			$response = $curl->setGetParams([
				'ParamString' => $param_string,
				'RecNum' => $get['RecNum'],
				'SignName' => $sms_config['SignName'],
				'TemplateCode' => $template_code['code']
			])->setHeaders([
				'Authorization' => 'APPCODE ' . $sms_config['AppCode'],
			])->get('http://sms.market.alicloudapi.com/singleSendSms');
			$result = json_decode($response);
			return $result;
		}
		else{
			throw new HttpException(500, "操作失败");
		}
	}

	private function createParamString($TemplateCode){
		$param = [];
		switch ($TemplateCode){
			case 'register':
				$param['code'] = sprintf('%06d', rand(0, 999999));
				break;
			default:
				// do nothing
		}
		return json_encode($param);
	}

	public function actionCaptcha($random){
		// 生成验证码
		$captcha = new CaptchaAction(1, $this);
		$captcha->minLength = 4;
		$captcha->maxLength = 4;
		$captcha->width = 80;
		$captcha->height = 45;
		$captcha->getVerifyCode(true);
		@ header("Content-Type:image/png"); // 创建一个图层
		echo $captcha->run();exit;
	}
}
