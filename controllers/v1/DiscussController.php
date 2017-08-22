<?php

namespace app\controllers\v1;

use app\models\Discuss;
use Yii;
use yii\web\HttpException;
use conquer\oauth2\TokenAuth;

class DiscussController extends BaseController
{
	public $modelClass = 'app\models\Discuss';

	public function actions(){
		$actions = parent::actions();
		unset($actions['create']);
		return $actions;
	}

	public function actionCreate(){
		$post = Yii::$app->request->post();
		$discuss = new Discuss();
		foreach ($post as $k => $v){
			$discuss->{$k} = $v;
		}

		$discuss->user_id = $this->getUserId();
		$discuss->create_time = time();
		$discuss->update_time = time();
		$result = $discuss->save();
		if(!$result){
			throw new HttpException(500, '操作失败');
		}
		return $discuss->attributes;
	}
}
