<?php

namespace app\controllers\v1;

use Yii;
use app\models\File;
use yii\web\HttpException;

class StatisticController extends BaseController
{
	public $modelClass = 'app\models\File';

	public function actions(){
		$actions = parent::actions();
		unset($actions['index'], $actions['view'], $actions['create'], $actions['update']);
		return $actions;
	}

	public function actionIndex(){
		$get = Yii::$app->request->get();
		if(isset($get['type'])){
			if($get['type'] == 'month_storage'){
				return $this->monthStorage();
			}
			else if($get['type'] == 'week_storage'){
				return $this->weekStorage();
			}
		}
	}

	public function monthStorage(){
		$token_auth = new \conquer\oauth2\TokenAuth();
		$user_id = $token_auth->getAccessToken()->user_id;
		$get = Yii::$app->request->get();
		if(isset($get['date'])){

		}
		else{
			$date = date('Y-m-d');
		}
		$today = date('Y-m-d');
		$first = date('Y-m-01', strtotime($date)); // 当月第一天
		$last = date('Y-m-d', strtotime(date('Y-m-01', strtotime($date." +1 months"))." -1 days")); // 当月最后一天
		$data = [];
		for($i = 0; $i < 31; $i++){
			$key = date('Y-m-d', strtotime($first." +${i} days"));
			if($key > $last ){
				break;
			}
			if($key <= $today){
				$data[$key] = 0;
			}
			else{
				$data[$key] = '';
			}
		}
		$query = File::find();
		$result = $query->select(["create_time", 'SUM(size) AS size', 'user_id'])->where(['user_id' => $user_id])->andWhere(['>=','date_format(create_time, \'%Y-%m-%d\')', $first])->andWhere(['<=','date_format(create_time, \'%Y-%m-%d\')', $last])->groupBy(["date_format(create_time, '%Y-%m-%d')", 'user_id'])->all();
		foreach ($result as $k => $v){
			$key = date('Y-m-d', strtotime($v->attributes['create_time']));
			$data[$key] = $v->attributes['size'];
		}

		$labels = [];
		$series = [];
		foreach ($data as $k => $v){
			if($v !== ''){
				$series[] = $v;
			}
			$labels[] = $k;
		}
		return ['labels' => $labels, 'series' => $series];
	}

	public function weekStorage(){
		$token_auth = new \conquer\oauth2\TokenAuth();
		$user_id = $token_auth->getAccessToken()->user_id;
		$get = Yii::$app->request->get();
		if(isset($get['date'])){

		}
		else{
			$date = date('Y-m-d');
		}
		$today = date('Y-m-d');
		$f = 1;
		$w = date('w',strtotime($date));
		$first = date('Y-m-d', strtotime("$date -".($w ? $w - $f : 6).' days')); // 当周第一天
		$last = date('Y-m-d', strtotime("$first +6 days")); // 当周最后一天
		$data = [];
		for($i = 0; $i < 7; $i++){
			$key = date('Y-m-d', strtotime($first." +${i} days"));
			if($key > $last ){
				break;
			}
			if($key <= $today){
				$data[$key] = 0;
			}
			else{
				$data[$key] = '';
			}
		}
		$query = File::find();
		$result = $query->select(["create_time", 'SUM(size) AS size', 'user_id'])->where(['user_id' => $user_id])->andWhere(['>=','date_format(create_time, \'%Y-%m-%d\')', $first])->andWhere(['<=','date_format(create_time, \'%Y-%m-%d\')', $last])->groupBy(["date_format(create_time, '%Y-%m-%d')", 'user_id'])->all();
//		var_dump($query->createCommand()->getRawSql());exit;
		foreach ($result as $k => $v){
			$key = date('Y-m-d', strtotime($v->attributes['create_time']));
			if($v->attributes['size'] > 0){
				$data[$key] = $v->attributes['size'];
			}
		}

		$labels = [];
		$series = [];
		foreach ($data as $k => $v){
			if($v !== ''){
				$series[] = $v;
			}
			$labels[] = $k;
		}
		return ['labels' => $labels, 'series' => $series];
	}
}
