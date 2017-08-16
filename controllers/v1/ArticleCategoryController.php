<?php

namespace app\controllers\v1;

use app\models\ArticleCategory;
use Yii;
use app\models\Article;
use app\models\File;
use yii\data\Pagination;
use yii\web\HttpException;
use conquer\oauth2\TokenAuth;

class ArticleCategoryController extends BaseController
{
	public $modelClass = 'app\models\ArticleCategory';

	public function actions(){
		$actions = parent::actions();
		unset($actions['create'], $actions['update']);
		return $actions;
	}

	public function actionCreate(){
		$post = Yii::$app->request->post();
		$article_category = new ArticleCategory();
		foreach ($post as $k => $v){
			$article_category->{$k} = $v;
		}

		$token_auth = new TokenAuth();
		$article_category->user_id = $token_auth->getAccessToken()->user_id;
		$article_category->create_time = date('Y-m-d H:i:s');
		$article_category->update_time = date('Y-m-d H:i:s');
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

		$article_category->update_time = date('Y-m-d H:i:s');
		$result = $article_category->save();
		if(!$result){
			throw new HttpException(500, '操作失败');
		}
		return $article_category->attributes;
	}
}
