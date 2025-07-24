<?php

namespace app\models\relation;

use Yii;
use app\components\utils\Utils;
use app\models\reference\CVlanNet;
use app\models\reference\SIpdnsTable;
use app\models\registry\REquipment;

/**
 * This is the model class for table "nn_equipment_vlan".
 *
 * @property string $id
 * @property string $equipment
 * @property string $vlan_net
 * @property string $ip_address
 * @property string $mask_subnet
 *
 * @property CVlanNet $vlanNet
 */
class NnEquipmentVlan extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nn_equipment_vlan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['equipment', 'ip_address', 'mask_subnet'], 'safe'],
            [['equipment', 'ip_address', 'mask_subnet'], 'required'],
            [['equipment', 'vlan_net', 'ip_address', 'mask_subnet'], 'integer'],
            // [['ip_address', 'mask_subnet'], 'match', 'pattern' => '/((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)/']

            // добавлено при разработке АПИ
            [['ip_address_raw', 'mask_subnet_raw'], 'ip'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'equipment' => 'Equipment',
            'vlan_net' => 'Vlan Net',
            'ip_address' => 'Ip Address',
            'mask_subnet' => 'Mask Subnet',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVlanNet()
    {
        return $this->hasOne(CVlanNet::className(), ['id' => 'vlan_net']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipment0()
    {
        return $this->hasOne(REquipment::className(), ['id' => 'equipment']);
    }

    public function getIpAddressLookable()
    {
        if ($this->ip_address) {
            return long2ip($this->ip_address);
        }
        return null;
    }

    public function getMaskSubnetLookable()
    {
        if ($this->mask_subnet) {
            return long2ip($this->mask_subnet);
        }
        return null;
    }

    public function getMaskSubnetDecimale()
    {
        return Utils::maskToDec($this->maskSubnetLookable);
    }

    /**
    * Вернуть dns имя для ip
    */
    public function getDnsName()
    {
        // return $this->hasOne(REquipment::className(), ['id' => 'equipment']);
        // $class = SIpdnsTable::className();
        $query = SIpdnsTable::find();
        $query->primaryModel = $this;
        $query->link = ['ip_address' => 'ip_address'];
        $query->multiple = true;
        return $query;
    }

    /*
     * Для использования в АПИ добавлен следующий функционал, исходя из совместимости с имеющимся Легаси кодом
     * */

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['ip_address'], $fields['mask_subnet']);
        $fields[] = 'ip_address_raw';
        $fields[] = 'mask_subnet_raw';
        return $fields;
    }

    public function getIp_address_raw()
    {
        return $this->ipAddressLookable;
    }

    public function setIp_address_raw($value)
    {
        $this->ip_address = ip2long($value);
    }

    public function getMask_subnet_raw()
    {
        return $this->maskSubnetLookable;
    }

    public function setMask_subnet_raw($value)
    {
        $this->mask_subnet = ip2long($value);
    }
}
