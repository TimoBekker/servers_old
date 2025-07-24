<?php

namespace app\models\reference;

use Yii;
use app\models\User;
use app\models\registry\REquipment;
use app\models\registry\RSoftware;

/**
 * This is the model class for table "s_organization".
 *
 * @property string $id
 * @property string $name
 * @property string $address
 * @property string $phone
 * @property string $site
 * @property string $parent
 *
 * @property REquipment[] $rEquipments
 * @property RSoftware[] $rSoftwares
 * @property SContractor[] $sContractors
 * @property SOrganization $parent0
 * @property SOrganization[] $sOrganizations
 * @property User[] $users
 */
class SOrganization extends \yii\db\ActiveRecord
{
    public static $tableLabelName = "Организации";
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 's_organization';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', /*'address', 'phone', 'site'*/], 'required'],
            [['parent'], 'integer'],
            [['name', 'phone'], 'string', 'max' => 255],
            [['address'], 'string', 'max' => 512],
            [['site'], 'url']
        ];
    }

    /**
     * @property array $_attributeRenders
     */
    private $_attributeRenders = [
        'name' => [
            'textInput',
            '255',
        ],
        'address' => [
            'textInput',
            '512',
        ],
        'phone' => [
            'textInput',
            '128',
        ],
        'site' => [
            'textInput',
            '255',
        ],
        'parent' => [
            'dropDownList',
            // второй параметр-массив будет добавлен при инициализации - public function init()
        ]
    ];

    public function getAttributeRender($attrName, $disableid = null){
        // if только в этой модели. Исключаем возможность выбрать организацию руководящую самой собой (актуально при редактировании записи)
        if( $attrName === 'parent' && !is_null($disableid) ){

            // инициализируем $_attributeRenders parent:
            $db = Yii::$app->db;
            $arrList = $db->createCommand('SELECT `id`, `name` FROM `s_organization`')->queryAll();
            $ddlist = ['' => 'Нет'];
            foreach ($arrList as $key => $value) {
                $ddlist[$value["id"]] = $value["name"];
            }
            $this->_attributeRenders['parent'][] = $ddlist;
            // -----------------------------------------
            $temp = $this->_attributeRenders[$attrName];
            unset($temp[1][$disableid]);
            return $temp;
        }
        return isset($this->_attributeRenders[$attrName]) ? $this->_attributeRenders[$attrName] : false;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'address' => 'Адрес',
            'phone' => 'Телефон',
            'site' => 'Сайт',
            'parent' => 'Руководящая организация',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getREquipments()
    {
        return $this->hasMany(REquipment::className(), ['organization' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRSoftwares()
    {
        return $this->hasMany(RSoftware::className(), ['developer' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSContractors()
    {
        return $this->hasMany(SContractor::className(), ['organization' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent0()
    {
        return $this->hasOne(SOrganization::className(), ['id' => 'parent']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSOrganizations()
    {
        return $this->hasMany(SOrganization::className(), ['parent' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['organization' => 'id']);
    }

    /*
        Функция удаления модели с проверкой возможных связей
    */
    public function deleteWithCheckConstraint(){
        if ( $this->rEquipments
                || $this->rSoftwares
                    || $this->sContractors
                        || $this->sOrganizations
                            || $this->users )
            return false;
        $this->delete();
        return true;
    }
}
