<?php

namespace app\controllers\v1;

use Yii;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class QiniuController extends BaseController
{
	public $modelClass = 'app\models\File';

	public function actions(){
		$actions = parent::actions();
		unset($actions['index'], $actions['view'], $actions['create'], $actions['update']);
		return $actions;
	}

	public function actionToken(){
		$accessKey = Yii::$app->params['qiniu']['accessKey'];
		$secretKey = Yii::$app->params['qiniu']['secretKey'];
		$auth = new Auth($accessKey, $secretKey);

		$bucket = Yii::$app->params['qiniu']['bucket'];
		// 生成上传Token
		$uptoken = $auth->uploadToken($bucket);
		$domain = Yii::$app->params['qiniu']['domain'];
		return ['uptoken' => $uptoken, 'domain' => $domain];
	}
}
