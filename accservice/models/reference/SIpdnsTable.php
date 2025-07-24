<?php

namespace app\models\reference;

use Yii;

/**
 * This is the model class for table "s_ipdns_table".
 *
 * @property string $id
 * @property string $dns_name
 * @property string $ip_address
 */
class SIpdnsTable extends \yii\db\ActiveRecord
{
    public static $tableLabelName = "Соответствие DNS-имён IP-адресам";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 's_ipdns_table';
    }
    /**
     * @property array $_attributeRenders
     */
    private $_attributeRenders = [
        'dns_name' => [
            'textInput',
            '128',
        ],
        'ip_address' => [
            'textInput',
            '15',
        ]
    ];

    public function getAttributeRender($attrName){
        return isset($this->_attributeRenders[$attrName]) ? $this->_attributeRenders[$attrName] : false;
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dns_name', 'ip_address'], 'required'],
            [['ip_address'], 'string', 'max' => 15],
            [['dns_name'], 'string', 'max' => 255],
            [['dns_name', 'ip_address'], 'unique', 'targetAttribute' => ['dns_name', 'ip_address'],
                'message' => 'Комбинация DNS-имени и IP-адреса должна быть уникальна']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dns_name' => 'DNS-имя',
            'ip_address' => 'IP-адрес',
        ];
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // для совместимости с предыдущим плохим кодом пришлось здесь оставить post
            $postDepricatedData = Yii::$app->request->post('SIpdnsTable')['ip_address'];
            if ( $postDepricatedData ) {
                $this->ip_address = $postDepricatedData;
            }
            if(false === $temp = ip2long( $this->ip_address )){
                return false;
            }
            else{
                $this->ip_address = sprintf("%u", $temp);
            }
            return true;
        } else {
            return false;
        }
    }

    /*
        Функция удаления модели с проверкой возможных связей
    */
    public function deleteWithCheckConstraint(){
        // В данной модели это просто обертка для совместимости
        $this->delete();
        return true;
    }
}