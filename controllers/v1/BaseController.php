<?php

namespace app\controllers\v1;

use Yii;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;

class BaseController extends ActiveController
{
	public $modelClass = 'app\models\Article';

	public function behaviors(){
		if(Yii::$app->request->method == 'GET'){ // CORS跨域预检
			$behaviors = [];
		}
		else{
			$behaviors = [
				'tokenAuth' => [
					'class' => \conquer\oauth2\TokenAuth::className(),
				],
			];
		}
		return ArrayHelper::merge(parent::behaviors(), $behaviors);
	}

	public function paginationData($models, $pagination){
		$pages = array();
		$page_size = $pagination->getPageSize();
		$total_count = $pagination->totalCount;
		$pages['totalPage'] = ceil($total_count/$page_size);
		$pages['page'] = $pagination->getPage();
		return ['pages' => $pages, 'models' => $models];
	}
}
