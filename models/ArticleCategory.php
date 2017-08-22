<?php
namespace app\models;

use Yii;
use conquer\oauth2\Exception;
use yii\helpers\VarDumper;

class ArticleCategory extends \yii\db\ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return '{{%article_category}}';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['user_id', 'parent_id', 'type', 'status'], 'integer'],
			[['title'], 'string'],
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
			'parent_id' => '上级ID',
			'title' => '标题',
			'type' => '类型',
			'status' => '状态',
			'create_time' => '创建时间',
			'update_time' => '编辑时间'
		];
	}
}
