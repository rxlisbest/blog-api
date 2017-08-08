<?php

namespace app\controllers\v1;

use Yii;
use app\models\Article;
use yii\data\Pagination;
use yii\web\HttpException;
use conquer\oauth2\TokenAuth;

class ArticleController extends BaseController
{
	public $modelClass = 'app\models\Article';

	public function actions(){
		$actions = parent::actions();
		unset($actions['index'], $actions['create'], $actions['update'], $actions['delete']);
		return $actions;
	}

	public function actionIndex(){
		$get = Yii::$app->request->get();
		if(isset($get['page']) && $get['page']){
			$page = $get['page'];
		}
		else{
			$page = 0;
		}
		$where = [];
		if(isset($get['user_id'])){
			$where['user_id'] = $get['user_id'];
		}
		$query = Article::find();
		$query->where($where);
		// 得到文章的总数（但是还没有从数据库取数据）
		$count = $query->count();
		// 使用总数来创建一个分页对象
		$pagination = new Pagination(['totalCount' => $count, 'page' => $page]);

		// 使用分页对象来填充 limit 子句并取得文章数据
		$articles = $query->orderBy(['create_time' => SORT_DESC])->offset($pagination->offset)->limit($pagination->limit)->all();
		return $this->paginationData($articles, $pagination);
	}

	public function actionCreate(){
		$post = Yii::$app->request->post();
		$article = new Article();
		foreach ($post as $k => $v){
			$article->{$k} = $v;
		}

		$token_auth = new TokenAuth();
		$article->user_id = $token_auth->getAccessToken()->user_id;
		$article->create_time = date('Y-m-d H:i:s');
		$article->update_time = date('Y-m-d H:i:s');
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

		$article->update_time = date('Y-m-d H:i:s');
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
