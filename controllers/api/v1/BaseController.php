<?php

namespace app\controllers\api\v1;

use Yii;
use yii\web\Response;
use yii\rest\ActiveController;

class BaseController extends ActiveController
{
	public $modelClass = 'app\models\Article';

	public function behaviors(){
		return [
			/**
			 * Performs authorization by token
			 */
			'tokenAuth' => [
				'class' => \conquer\oauth2\TokenAuth::className(),
			],
		];
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
