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
			[['nickName', 'avatarUrl', 'country', 'province', 'city', 'language'], 'string'],
			[['create_time', 'update_time'], 'integer']
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
			'nickName' => 'nickName',
			'avatarUrl' => 'avatarUrl',
			'country' => 'country',
			'province' => 'province',
			'city' => 'city',
			'language' => 'language',
			'create_time' => '创建时间',
			'update_time' => '编辑时间'
		];
	}
}
