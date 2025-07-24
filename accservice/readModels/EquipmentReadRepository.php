<?php
namespace app\readModels;

use app\models\registry\REquipment;

/**
 * Created by PhpStorm.
 * User: custom
 * Date: 24.08.18
 * Time: 13:19
 */

class EquipmentReadRepository
{
    public $equipment;
    /**
     * EquipmentReadRepository constructor.
     */
    public function __construct(REquipment $equipment)
    {
        $this->equipment = $equipment;
    }

    public function getInformationSystems(){
        return $this->equipment->getNnEquipmentInfosysContours()->with(["equipmentModel", "informationSystemModel", "contourModel"])->orderBy("contour")->all();
    }
}