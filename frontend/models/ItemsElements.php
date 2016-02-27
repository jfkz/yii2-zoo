<?php

namespace worstinme\zoo\frontend\models;

use Yii;

/**
 * This is the model class for table "{{%zoo_items_elements}}".
 *
 * @property integer $id
 * @property integer $item_id
 * @property integer $element_id
 * @property string $value_text
 * @property integer $value_int
 * @property string $value_string
 * @property double $value_float
 */
class ItemsElements extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%zoo_items_elements}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id', 'element_id'], 'required'],
            [['item_id', 'element_id', 'value_int'], 'integer'],
            [['value_text'], 'string'],
            [['value_float'], 'number'],
            [['value_string'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_id' => 'Item ID',
            'element_id' => 'Element ID',
            'value_text' => 'Value Text',
            'value_int' => 'Value Int',
            'value_string' => 'Value String',
            'value_float' => 'Value Float',
        ];
    }
}