<?php
namespace app\models;

use Yii;
use conquer\oauth2\Exception;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "oauth2_user".
 *
 * @property integer $user_id
 * @property string $username
 * @property string $password
 * @property string $salt
 * @property string $roles
 * @property string $scope
 */
class UserWechat extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%user_wechat}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['user_id'], 'integer'],
			[['openid'], 'string'],
			[['create_time', 'update_time'], 'string']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'user_id' => '用户ID',
			'openid' => 'openid',
			'create_time' => '创建时间',
			'update_time' => '编辑时间'
		];
	}
}
