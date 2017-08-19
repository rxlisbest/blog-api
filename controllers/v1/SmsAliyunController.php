<?php

namespace app\controllers\v1;

use Yii;
use linslin\yii2\curl;
use app\models\SmsAliyun;
use yii\web\HttpException;

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

		if(!isset($get['TemplateCode']) || !isset($get['RecNum'])){
			throw new HttpException(400, "参数错误");
		}

		$sms_config = Yii::$app->params['aliyun']['sms'];
		if(!isset($sms_config['TemplateCode'][$get['TemplateCode']])){
			throw new HttpException(400, "参数错误");
		}
		$template_code = $sms_config['TemplateCode'][$get['TemplateCode']];
		$query = SmsAliyun::find()->andWhere(['>', 'create_time', strtotime(date('Y-m-d'))])->andWhere(['<', 'create_time', time()]);
		// 同一IP每天条数限制
		$m = clone $query;
		$where = ['ip' => Yii::$app->request->userIP];
		$send_count = $m->where($where)->count();
		if($send_count >= 3*$template_code['daily_limit']){
			throw new HttpException(500, "同一IP超出每天限制数量");
		}
		// 同一模板每天条数限制
		$n = clone $query;
		$where = ['RecNum' => $get['RecNum']];
		$send_count = $n->where($where)->andWhere(['TemplateCode' => $template_code['code']])->count();
		if($send_count >= $template_code['daily_limit']){
			throw new HttpException(500, "超出每天限制数量");
		}

		// 上次发短信时间间隔
		$where = ['RecNum' => $get['RecNum']];
		$last_sms = SmsAliyun::find()->where($where)->andWhere(['TemplateCode' => $template_code['code']])->orderBy('create_time DESC')->one();
		if($last_sms && time() - $last_sms->attributes['create_time'] < $sms_config['time_interval']){
			throw new HttpException(500, '时间间隔');
		}

		$param_string = $this->createParamString($get['TemplateCode']);
		$sms_aliyun = new SmsAliyun();
		$sms_aliyun->ParamString = $param_string;
		$sms_aliyun->RecNum = $get['RecNum'];
		$sms_aliyun->TemplateCode = $template_code['code'];
		$sms_aliyun->ip = Yii::$app->request->userIP;
		$sms_aliyun->create_time = (string)time();
		$sms_aliyun->update_time = (string)time();
		$result = $sms_aliyun->save();
		if($result){
//			$curl = new curl\Curl();
//			$response = $curl->setGetParams([
//				'ParamString' => $param_string,
//				'RecNum' => $get['RecNum'],
//				'SignName' => $sms_config['SignName'],
//				'TemplateCode' => $template_code['code']
//			])->setHeaders([
//				'Authorization' => 'APPCODE ' . $sms_config['AppCode'],
//			])->get('http://sms.market.alicloudapi.com/singleSendSms');
//			$result = json_decode($response);
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
}
