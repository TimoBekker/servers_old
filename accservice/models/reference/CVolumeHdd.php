<?php

namespace app\models\reference;

use Yii;
use app\components\utils\Utils;
use app\models\registry\REquipment;

/**
 * This is the model class for table "c_volume_hdd".
 *
 * @property integer $id
 * @property integer $is_dynamic
 * @property string $size
 * @property integer $equipment
 *
 * @property REquipment $equipment0
 */
class CVolumeHdd extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'c_volume_hdd';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        /* новые требования
        ПЗУ храняться в мегабайтай т.е. единица хранения = 1мб
        Примечание к volume_hdd, т.к. объемы большие, то в базу данных мы задаем тип
        UNSIGNED BIGINT, который может сохранить число 18446744073709551615;
        в PHP нет квантификатора UNSIGNED, поэтому максимальное положительное число на 64 битной
        системы мы можем запихнуть 9223372036854775807, что логично вдвое меньше.
        Для поля ввода, всвязи с ТЗ мы ограничим ввод как 18446744073709551616 / 1024 - 1
        todo: сделать проверку ввода в множественном поле
        */
        return [
            [['size', 'equipment'], 'required'],
            [['is_dynamic', 'equipment'], 'integer'],
            // [['size'], 'double', 'max' => gmp_div('18446744073709551616', '1024') - 1, 'on' => ['for_the_form']],
            [['size'], 'string', 'on' => ['default']],
            [['size'], 'filter', 'filter' => function($value) {
                if (!is_numeric($value)) {
                    return '0';
                }
                if (gmp_cmp($value, '18446744073709551615') === 1) {
                    return '18446744073709551615';
                }
                if (gmp_cmp($value, '0') === -1) {
                    return '0';
                }
                return $value;
            }, 'on' => ['default']],
            [['equipment'], 'exist', 'skipOnError' => true, 'targetClass' => REquipment::className(), 'targetAttribute' => ['equipment' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'is_dynamic' => 'Тип',
            'size' => 'Объем',
            'equipment' => 'Equipment',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipment0()
    {
        return $this->hasOne(REquipment::className(), ['id' => 'equipment']);
    }

    // всегда возвращаем true, строку не int и не float не трогаем
    // примечание, в size будет передаваться 18446744073709551616 / 1024 - 1, а такое число у нас
    // есть, то мы можем свободно вызывать функцию Utils::getRealNumType()
    // p.s. gmp_mul не может умножать десятичные числа, поэтому мы делаем сумму умножений целой
    // и дробной части
    public function servicePrepareSize()
    {
        $this->size = (string)floatval($this->size);
        if (Utils::getRealNumType($this->size) === 'int') {
            $this->size = (string)gmp_mul($this->size, '1024');
        } elseif (Utils::getRealNumType($this->size) === 'float') {
            $intval = intval($this->size);
            $ost = $this->size - $intval;
            $fp = gmp_mul((string)$intval, '1000');
            $sp = round($ost * 1000);
            $this->size = (string)gmp_add($fp, (string)$sp);
        }
        return true;
    }
}
