<?php

namespace app\controllers\v1;

use app\models\ArticleCategory;
use Yii;
use yii\web\HttpException;
use conquer\oauth2\TokenAuth;
use yii\data\ActiveDataProvider;

class ArticleCategoryController extends BaseController
{
	public $modelClass = 'app\models\ArticleCategory';

	public function actions(){
		$actions = parent::actions();
		unset($actions['index'], $actions['create'], $actions['update']);
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
			'query' => ArticleCategory::find()->where($where)->orderBy(['create_time' => SORT_DESC]),
			'pagination' => $pagination,
		]);
	}

	public function actionCreate(){
		$post = Yii::$app->request->post();
		$article_category = new ArticleCategory();
		foreach ($post as $k => $v){
			$article_category->{$k} = $v;
		}

		$token_auth = new TokenAuth();
		$article_category->user_id = $token_auth->getAccessToken()->user_id;
		$article_category->create_time = time();
		$article_category->update_time = time();
		$result = $article_category->save();
		if(!$result){
			throw new HttpException(500, '操作失败');
		}
		return $article_category->attributes;
	}

	public function actionUpdate($id){
		$post = Yii::$app->request->post();

		$token_auth = new TokenAuth();
		$user_id = $token_auth->getAccessToken()->user_id;

		$article_category = ArticleCategory::findOne(['id' => $id, 'user_id' => $user_id]); // 修改需判断身份
		if(!$article_category){
			throw new HttpException(404, "Object not found: ${id}");
		}

		foreach ($post as $k => $v){
			$article_category->{$k} = $v;
		}

		$article_category->update_time = time();
		$result = $article_category->save();
		if(!$result){
			throw new HttpException(500, '操作失败');
		}
		return $article_category->attributes;
	}
}
