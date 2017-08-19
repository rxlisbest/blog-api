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
class SmsAliyun extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%sms_aliyun}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['ParamString', 'RecNum', 'TemplateCode', 'ip'], 'string'],
			[['create_time', 'update_time'], 'string']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'ParamString' => 'ParamString',
			'RecNum' => 'RecNum',
			'TemplateCode' => 'TemplateCode',
			'ip' => 'IP',
			'create_time' => '创建时间',
			'update_time' => '编辑时间'
		];
	}
}
