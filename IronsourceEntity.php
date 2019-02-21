<?php

namespace common\models\offerwall\ironsource;

use common\models\User;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "ow_ironsource".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $rewards
 * @property string $event_id
 * @property string $item_name
 * @property string $created_at
 * @property string $lang
 * @property string $status
 */
class IronsourceEntity extends ActiveRecord
{
    const STATUS_NEW = 0;
    const STATUS_PROCESSED = 1;

    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return 'ow_ironsource';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['user_id', 'rewards', 'event_id', 'lang'], 'required'],
            [['user_id', 'rewards', 'status'], 'integer'],
            [['created_at'], 'safe'],
            [['event_id'], 'string', 'max' => 32],
            [['lang'], 'string', 'max' => 2],
            [['item_name'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'rewards' => 'Rewards',
            'event_id' => 'Event ID',
            'created_at' => 'Created At',
            'lang' => 'Lang',
        ];
    }

    public function switchStatus(int $newStatus): bool
    {
        $this->status = $newStatus;
        return $this->save();
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

}