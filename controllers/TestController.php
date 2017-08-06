<?php

namespace app\controllers;

use Yii;
use yii\web\Response;
use yii\rest\ActiveController;

class TestController extends ActiveController
{
	public $modelClass = 'app\models\User';
}
