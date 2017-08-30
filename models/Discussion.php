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
class Discussion extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%discussion}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['user_id', 'article_id', 'type', 'status', 'time'], 'integer'],
			[['content'], 'string'],
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
			'content' => '讨论正文',
			'article_id' => '文章ID',
			'status' => '状态',
			'time' => '视频播放时间',
			'create_time' => '创建时间',
			'update_time' => '编辑时间'
		];
	}
}
