<?php

namespace app\controllers\v1;

use Yii;
use yii\web\HttpException;
use yii\captcha\CaptchaAction;

class CaptchController extends BaseController
{
	public $modelClass = 'app\models\SmsAliyun';

	public function actions(){
		$actions = parent::actions();
		unset($actions['index'], $actions['view'], $actions['create'], $actions['update']);
		return $actions;
	}
}
