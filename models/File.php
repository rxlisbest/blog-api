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
class File extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%file}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['user_id', 'size', 'is_transcoded'], 'integer'],
			[['hash', 'name', 'type', 'domain', 'save_name', 'transcode_id', 'transcode_type', 'transcode_name'], 'string'],
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
			'hash' => 'HASH',
			'name' => '文件名称',
			'type' => '文件类型',
			'size' => '文件大小',
			'domain' => '文件域名',
			'save_name' => '保存文件名称',
			'transcode_id' => '转码ID',
			'transcode_type' => '转码类型',
			'transcode_name' => '转码文件名称',
			'is_transcoded' => '是否转码',
			'create_time' => '创建时间',
			'update_time' => '编辑时间'
		];
	}
}
