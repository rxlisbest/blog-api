<?php

namespace app\controllers\v1;

use app\models\Discussion;
use Yii;
use yii\web\HttpException;
use yii\data\ActiveDataProvider;

class DiscussionController extends BaseController
{
	public $modelClass = 'app\models\Discussion';

	public function actions(){
		$actions = parent::actions();
		unset($actions['index'], $actions['create']);
		return $actions;
	}

	public function actionIndex(){
		$get = Yii::$app->request->get();

		if(isset($get['page'])){
			$pagination = [
				'page' => $get['page'] - 1,
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

		if(isset($get['time'])){
			$get['time'] = explode(',', $get['time']);
			if(count(explode(',', $get['time'])) != 2){
				unset($get['time']);
			}
		}

		if(isset($get['user_id'])){
			$where['user_id'] = $get['user_id'];
		}

		if(isset($get['article_id'])){
			$where['article_id'] = $get['article_id'];
		}
		
		$where['status'] = 0;
		$discussion = Discussion::find()->where($where)->orderBy(['time' => SORT_ASC]);
		if(isset($get['time'])){
			$discussion = $discussion->andFilterWhere('between', 'time', $get['time'][0], $get['time'][1]);
		}
		return Yii::createObject([
			'class' => ActiveDataProvider::className(),
			'query' => $discussion,
			'pagination' => $pagination,
		]);
	}

	public function actionCreate(){
		$post = Yii::$app->request->post();
		$discussion = new Discussion();
		foreach ($post as $k => $v){
			$discussion->{$k} = $v;
		}

		$sensitive_words = Yii::$app->params['sensitive_words'];
		$discussion->content = str_replace($sensitive_words, '***', $discussion->content);
		$discussion->user_id = $this->getUserId();
		$discussion->create_time = time();
		$discussion->update_time = time();
		$result = $discussion->save();
		if(!$result){
			throw new HttpException(500, '操作失败');
		}
		return $discussion->attributes;
	}
}
