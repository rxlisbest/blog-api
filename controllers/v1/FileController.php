<?php

namespace app\controllers\v1;

use Yii;
use app\models\File;
use yii\web\HttpException;
use Qiniu\Auth;
use Qiniu\Processing\PersistentFop;

class FileController extends BaseController
{
	public $modelClass = 'app\models\File';

	public function actions(){
		$actions = parent::actions();
		unset($actions['view'], $actions['create'], $actions['update'], $actions['delete']);
		return $actions;
	}

	public function actionView($id){
		$file = File::findOne($id);
		if(!$file){
			throw new HttpException(404, "Object not found: ${id}");
		}
		if($file->attributes['transcode_id'] && $file->attributes['is_transcoded'] != 1){
			$persistentId = $file->attributes['transcode_id'];
			$status = PersistentFop::status($persistentId);
			foreach ($status ?: [] as $k => $v){
				foreach ($v['items'] ?: [] as $kk => $vv){
					if(isset($vv['code']) && $vv['code'] == 0){
						$file->is_transcoded = 1;
						$file->transcode_name = $vv['key'];
						$result = $file->save();
					}
				}
			}
		}
		return $file;
	}

	public function actionCreate(){
		$post = Yii::$app->request->post();
		$file = new File();
		foreach ($post as $k => $v){
			$file->{$k} = $v;
		}
		if(isset($post['type']) && isset($post['save_name'])){
			$arr = explode("/", $post['type']);
			if($arr[0] == 'video' && $arr[1] != 'mp4'){
				$accessKey = Yii::$app->params['qiniu']['accessKey'];
				$secretKey = Yii::$app->params['qiniu']['secretKey'];
				$auth = new Auth($accessKey, $secretKey);

				//要转码的文件所在的空间和文件名
				$bucket = Yii::$app->params['qiniu']['bucket'];
				$key = $post['save_name'];
				//转码是使用的队列名称
				$pipeline = Yii::$app->params['qiniu']['persistentPipeline'];
				$pfop = new PersistentFop($auth, $bucket, $pipeline);
				//要进行转码的转码操作
				$fops = Yii::$app->params['qiniu']['persistentOps'];
				list($id, $err) = $pfop->execute($key, $fops);
				if ($err == null) {
					$file->transcode_id = $id;
					$file->transcode_type = Yii::$app->params['qiniu']['transcodeType'];
				}
			}
		}

		$token_auth = new \conquer\oauth2\TokenAuth();
		$file->user_id = $token_auth->getAccessToken()->user_id;
		$file->create_time = time();
		$file->update_time = time();
		$result = $file->save();
		if(!$result){
			throw new HttpException(500, '操作失败');
		}
		return ['id' => $file->attributes['id']];
	}
}
