<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "system_status".
 *
 * @property int $id
 * @property int|null $is_disabled
 * @property string|null $disabled_at
 * @property string|null $disabled_by
 */
class SystemStatus extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'system_status';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['disabled_at', 'disabled_by'], 'default', 'value' => null],
            [['is_disabled'], 'default', 'value' => 0],
            [['is_disabled'], 'integer'],
            [['disabled_at'], 'safe'],
            [['disabled_by'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'is_disabled' => Yii::t('app', 'Is Disabled'),
            'disabled_at' => Yii::t('app', 'Disabled At'),
            'disabled_by' => Yii::t('app', 'Disabled By'),
        ];
    }

}
