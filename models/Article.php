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
class Article extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%article}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['user_id', 'file_id', 'type', 'status'], 'integer'],
			[['content', 'cover_src'], 'string'],
			[['title', 'create_time', 'update_time'], 'string']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'user_id' => '用户ID',
			'file_id' => '文件ID',
			'title' => '标题',
			'cover_src' => '封面链接',
			'content' => '内容',
			'type' => '类型',
			'status' => '状态',
			'create_time' => '创建时间',
			'update_time' => '编辑时间'
		];
	}
}
