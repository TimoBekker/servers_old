<?php

namespace app\models\registry;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\registry\REquipment;

/**
 * REquipmentSearch represents the model behind the search form about `app\models\registry\REquipment`.
 */
class REquipmentSearch extends REquipment
{
    public $dns_name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', /*'type',*/ 'placement', 'organization', 'count_processors', 'count_cores', 'amount_ram', 'volume_hdd', 'access_kspd', 'access_internet', 'connect_arcsight', 'access_remote'/*, 'parent'*/, 'backuping', 'state'], 'integer'],
            [['name', 'vmware_name', 'description', 'manufacturer', 'serial_number', 'inventory_number', 'date_commission', 'date_decomission', 'icing_internet', 'model'], 'safe'],
            [['type', 'parent'], 'string'],
            // связанные параметры для поиска и сортировки:
            [['ip_address'], 'safe'],
            [['vlan_name', 'claims','soft_installed','information_systems','information_system_contours'], 'integer'],
            [['dns_name', 'response_persons'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = REquipment::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        /*$dataProvider->sort->attributes['ip_address'] = [
            'asc' => ['nn_equipment_vlan.ip_address' => SORT_ASC],
            'desc' => ['nn_equipment_vlan.ip_address' => SORT_DESC],
            'default' => SORT_ASC,
        ];
        $dataProvider->sort->attributes['vlan_name'] = [
            'asc' => ['c_vlan_net.name' => SORT_ASC],
            'desc' => ['c_vlan_net.name' => SORT_DESC],
            'default' => SORT_ASC,
        ];
        $dataProvider->sort->attributes['name'] = [
            'asc' => ['r_equipment.name' => SORT_ASC],
            'desc' => ['r_equipment.name' => SORT_DESC],
        ];*/

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // перед передачей преобразуем реляционные параметры

        /*$typepattern = $this->type;
        if(!is_null($this->type) and $this->type !== '')
            $typepattern = Yii::$app->db->createCommand("SELECT id FROM c_type_equipment WHERE name LIKE :pattern", [':pattern'=>$this->type.'%'])->queryScalar();

        $parentpattern = $this->parent;
        if(!is_null($this->parent) and $this->parent !== '')
            $parentpattern = Yii::$app->db->createCommand("SELECT id FROM r_equipment WHERE name LIKE :pattern", [':pattern'=>$this->parent.'%'])->queryScalar();*/
        // var_dump($this->amount_ram);exit;

        $query->andFilterWhere([
            'r_equipment.id' => $this->id,
            // 'type' => $typepattern,
            'r_equipment.type' => $this->type,
            'r_equipment.state' => $this->state,
            'r_equipment.date_commission' => $this->date_commission,
            'r_equipment.date_decomission' => $this->date_decomission,
            'r_equipment.placement' => $this->placement,
            'r_equipment.organization' => $this->organization,
            'r_equipment.count_processors' => $this->count_processors,
            'r_equipment.count_cores' => $this->count_cores,
            'r_equipment.amount_ram' => $this->amount_ram,
            // 'volume_hdd' => $this->volume_hdd,
            'r_equipment.access_kspd' => $this->access_kspd,
            'r_equipment.access_internet' => $this->access_internet,
            'r_equipment.connect_arcsight' => $this->connect_arcsight,
            'r_equipment.access_remote' => $this->access_remote,
            'r_equipment.parent' => $this->parent,
            // 'parent' => $parentpattern,
            // 'c_vlan_net.name' => $this->vlan_name,
            'r_equipment.backuping' => $this->backuping,
        ]);

        $query->andFilterWhere(['like', 'r_equipment.name', $this->name])
            ->andFilterWhere(['like', 'r_equipment.vmware_name', $this->vmware_name])
            ->andFilterWhere(['like', 'r_equipment.description', $this->description])
            ->andFilterWhere(['like', 'r_equipment.manufacturer', $this->manufacturer])
            ->andFilterWhere(['like', 'r_equipment.serial_number', $this->serial_number])
            ->andFilterWhere(['like', 'r_equipment.inventory_number', $this->inventory_number])
            ->andFilterWhere(['like', 'r_equipment.icing_internet', $this->icing_internet])
            ->andFilterWhere(['like', 'r_equipment.model', $this->model])
        ;

        if ($this->ip_address) {
            $query->joinWith(['nnEquipmentVlans'])
                ->andFilterWhere(['like', 'inet_ntoa(nn_equipment_vlan.ip_address)', $this->ip_address])
            ;
        }

        if ($this->vlan_name) {
            $query->joinWith(['nnEquipmentVlans.vlanNet'])
                ->andFilterWhere(['c_vlan_net.id' => $this->vlan_name])
            ;
        }

        if ($this->dns_name) {
            $query->joinWith(['nnEquipmentVlans.dnsName'])
                ->andFilterWhere(['like', 's_ipdns_table.dns_name', $this->dns_name])
            ;
        }

        if ($this->response_persons) {
            $query->joinWith(['sResponseEquipments.responsePerson'])
                ->andFilterWhere([
                    'or',
                    ['like', 'user.first_name', $this->response_persons],
                    ['like', 'user.last_name', $this->response_persons],
                    ['like', 'user.second_name', $this->response_persons],
                ])
            ;
        }

        if ($this->claims) {
            $query->joinWith(['nnEquipmentClaims.claim0'])
                ->andFilterWhere(['s_claim.id' => $this->claims])
            ;
        }

        if ($this->soft_installed) {
            $query->joinWith(['rSoftwareInstalleds.software0'])
                ->andFilterWhere(['r_software.id' => $this->soft_installed])
            ;
        }

        if ($this->information_systems) {
            $query->joinWith(['nnEquipmentInfosysContours.informationSystemModel'])
                ->andFilterWhere(['r_information_system.id' => $this->information_systems])
            ;
        }

        if ($this->information_system_contours) {
            $query->joinWith(['nnEquipmentInfosysContours.contourModel'])
                ->andFilterWhere(['s_contour_information_system.id' => $this->information_system_contours])
            ;
        }

        if ($this->volume_hdd) {
            $query->joinWith(['cVolumeHdds'])
                ->andFilterWhere(['c_volume_hdd.size' => $this->volume_hdd])
            ;
        }

        $query->distinct();

        // принимать даты будем в формате дд.мм.гггг (настроим так датапикер)
        // если данные пришли валидуем, а потом сконвертируем в гггг-мм-дд и добавим в фильтр
        // подготовим валидатор дат
        /*$validator = new \yii\validators\DateValidator(['format'=>'dd.MM.yyyy']);
        $error='';
        // date_commission
        if(!empty($params['date_commission_from']) &&
                            $validator->validate($params['date_commission_from'], $error)){
            // конвертим в мускул формат
            $date_commission_from = (new \Datetime($params['date_commission_from']))
                ->format('Y-m-d');
            $query->andFilterWhere(
                ['>', 'date_commission', $date_commission_from]
            );
        }
        if(!empty($params['date_commission_to']) &&
                            $validator->validate($params['date_commission_to'], $error)){
            // конвертим в мускул формат
            $date_commission_to = (new \Datetime($params['date_commission_to']))
                ->format('Y-m-d');
                // var_dump($date_commission_to);exit;
            $query->andFilterWhere(
                ['<', 'date_commission', $date_commission_to]
            );
        }
        // date_decomission
        if(!empty($params['date_decomission_from']) &&
                            $validator->validate($params['date_decomission_from'], $error)){
            // конвертим в мускул формат
            $date_decomission_from = (new \Datetime($params['date_decomission_from']))
                ->format('Y-m-d');
            $query->andFilterWhere(
                ['>', 'date_decomission', $date_decomission_from]
            );
        }
        if(!empty($params['date_decomission_to']) &&
                            $validator->validate($params['date_decomission_to'], $error)){
            // конвертим в мускул формат
            $date_decomission_to = (new \Datetime($params['date_decomission_to']))
                ->format('Y-m-d');
                // var_dump($date_decomission_to);exit;
            $query->andFilterWhere(
                ['<', 'date_decomission', $date_decomission_to]
            );
        }*/

        return $dataProvider;
    }
}
