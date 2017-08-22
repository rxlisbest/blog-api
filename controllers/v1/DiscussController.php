<?php

namespace app\controllers\v1;

use app\models\Discuss;
use Yii;
use yii\web\HttpException;
use yii\data\ActiveDataProvider;

class DiscussController extends BaseController
{
	public $modelClass = 'app\models\Discuss';

	public function actions(){
		$actions = parent::actions();
		unset($actions['create']);
		return $actions;
	}

	public function actionIndex(){
		$get = Yii::$app->request->get();

		if(isset($get['page'])){
			$pagination = [
				'page' => $get['page'],
				'pageSize' => 10,
			];
		}
		else{
			$pagination = false;
		}

		$where = [];
		if(isset($get['type'])){
			$where['type'] = $get['type'];
		}

		if(isset($get['user_id'])){
			$where['user_id'] = $get['user_id'];
		}
		$where['status'] = 0;

		return Yii::createObject([
			'class' => ActiveDataProvider::className(),
			'query' => Discuss::find()->where($where)->orderBy(['create_time' => SORT_DESC]),
			'pagination' => $pagination,
		]);
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
