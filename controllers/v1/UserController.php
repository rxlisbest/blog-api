<?php

namespace app\controllers\v1;

use app\models\User;
use conquer\oauth2\TokenAuth;

class UserController extends BaseController{
	public $modelClass = 'app\models\User';

	public function actions(){
		$actions = parent::actions();
		return $actions;
	}

	public function actionFetch(){
		$token_auth = new TokenAuth();
		$user_id = $token_auth->getAccessToken()->user_id;
		$user = User::findOne($user_id);
		unset($user->password);
		return $user;
	}
}
