<?php

namespace app\controllers\v1;

use Yii;
use yii\web\Response;
use yii\rest\ActiveController;

class AuthController extends ActiveController
{
	public $modelClass = 'app\models\User';

	public function actions()
	{
		return [
			/**
			 * Returns an access token.
			 */
			'create' => [
				'class' => \conquer\oauth2\TokenAction::classname(),
			],
			'options' => [
				'class' => \conquer\oauth2\TokenAction::classname(),
			],
		];
	}
}
