<?php

namespace app\controllers\v1;

use app\models\ArticleCategory;
use Yii;
use app\models\Article;
use app\models\File;
use yii\data\Pagination;
use yii\web\HttpException;
use conquer\oauth2\TokenAuth;
use yii\data\ActiveDataProvider;

class ArticleController extends BaseController
{
	public $modelClass = 'app\models\Article';

	public function actions(){
		$actions = parent::actions();
		unset($actions['index'], $actions['view'], $actions['create'], $actions['update'], $actions['delete']);
		return $actions;
	}

	public function actionIndex(){
		$get = Yii::$app->request->get();
		if(isset($get['page'])){
			$page = $get['page'];
		}
		else{
			$page = 0;
		}

		$where = [];
		if(isset($get['user_id'])){
			$where['a.user_id'] = $get['user_id'];
		}
		if(isset($get['type'])){
			$where['ac.type'] = $get['type'];
		}
		if(isset($get['category_id'])){
			$where['a.category_id'] = $get['category_id'];
		}
		$where['a.status'] = 0;
		return Yii::createObject([
			'class' => ActiveDataProvider::className(),
			'query' => Article::find()->select('a.*')->from('article AS a')->leftJoin("article_category AS ac", 'a.category_id = ac.id')->where($where)->orderBy(['create_time' => SORT_DESC]),
			'pagination' => [
				'page' => $page,
				'pageSize' => 1,
			],
		]);
	}

	public function actionView($id){
		$article = Article::findOne($id);
		if(!$article){
			throw new HttpException(404, "Object not found: ${id}");
		}

		$article_category = ArticleCategory::findOne($article->category_id);
		if($article_category->type == 1 && $article->cover_src == ''){
			$file = File::findOne($article->attributes['file_id']);
			if($file){
				$video = '';
				if($file->attributes['transcode_id']){
					if($file->attributes['is_transcoded']){
						$video = $file->attributes['domain'] . $file->attributes['transcode_name'];
					}
				}
				else{
					$video = $file->attributes['domain'] . $file->attributes['save_name'];
				}
				if($video){
					$cover_src = $video . '?vframe/jpg/offset/5';
					$article->cover_src =  $cover_src;
					$article->save();
				}
			}
		}
		return $article;
	}

	public function actionCreate(){
		$post = Yii::$app->request->post();
		$article = new Article();
		foreach ($post as $k => $v){
			$article->{$k} = $v;
		}

		$token_auth = new TokenAuth();
		$article->user_id = $token_auth->getAccessToken()->user_id;
		$article->create_time = time();
		$article->update_time = time();
		$result = $article->save();
		if(!$result){
			throw new HttpException(500, '操作失败');
		}
		return $article->attributes;
	}

	public function actionUpdate($id){
		$post = Yii::$app->request->post();

		$token_auth = new TokenAuth();
		$user_id = $token_auth->getAccessToken()->user_id;

		$article = Article::findOne(['id' => $id, 'user_id' => $user_id]); // 修改需判断身份
		if(!$article){
			throw new HttpException(404, "Object not found: ${id}");
		}

		foreach ($post as $k => $v){
			$article->{$k} = $v;
		}

		$article->update_time = time();
		$result = $article->save();
		if(!$result){
			throw new HttpException(500, '操作失败');
		}
		return $article->attributes;
	}

	public function actionDelete($id){
		$token_auth = new TokenAuth();
		$user_id = $token_auth->getAccessToken()->user_id;

		$article = Article::findOne(['id' => $id, 'user_id' => $user_id]); // 修改需判断身份
		if(!$article){
			throw new HttpException(404, "Object not found: ${id}");
		}

		$result = $article->delete();
		if(!$result){
			throw new HttpException(500, '操作失败');
		}
		return $article->attributes;
	}
}
