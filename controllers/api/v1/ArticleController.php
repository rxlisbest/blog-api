<?php

namespace app\controllers\api\v1;

use Yii;
use app\models\Article;
use yii\data\Pagination;

class ArticleController extends BaseController
{
	public $modelClass = 'app\models\Article';

	public function actions(){
		$actions = parent::actions();
		unset($actions['index'], $actions['create']);
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
		$query = Article::find();

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

		$token_auth = new \conquer\oauth2\TokenAuth();
		$article->user_id = $token_auth->getAccessToken()->user_id;
		$article->create_time = date('Y-m-d H:i:s');
		$article->update_time = date('Y-m-d H:i:s');
		$result = $article->save();
		if(!$result){
			throw new HttpException(500, '操作失败');
		}
		return $article->attributes;
	}
}
