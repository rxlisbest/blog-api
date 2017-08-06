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
		unset($actions['index']);
		return $actions;
	}

	public function actionIndex(){
		$get = Yii::$app->request->get();
		$query = Article::find();

		// 得到文章的总数（但是还没有从数据库取数据）
		$count = $query->count();
		// 使用总数来创建一个分页对象
		$pagination = new Pagination(['totalCount' => $count]);
		// 使用分页对象来填充 limit 子句并取得文章数据
		$articles = $query->orderBy([ 'create_time' => SORT_DESC])->offset($pagination->offset)->limit($pagination->limit)->all();
		return $this->paginationData($articles, $pagination);
	}
}
