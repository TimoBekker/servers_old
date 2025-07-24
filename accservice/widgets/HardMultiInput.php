<?php

namespace app\widgets;

use app\components\utils\Utils;
use app\widgets\SimpleMultiInput;
use yii\base\InvalidConfigException;
use yii\base\Widget;

final class HardMultiInput extends SimpleMultiInput
{
    const TYPE_HARD_SELECT = 'hardselect';
    const TYPE_DATE = 'date';

    public $subtIds; // массив вычитаемых айдишников
    public $countNeedRows; // количество строк
    public $softLayout;

    /* override */
    public function run()
    {
        $b = is_null($this->model) || is_null($this->attribute) || is_null($this->fieldsNames);
        if ($b) {
            throw new InvalidConfigException("Неверно заданы параметры виджета", 777002);
        }
        $relationRows = $this->model->{$this->attribute};
        array_unshift($relationRows, $this->emptyRelationModel);
        $view = 'hardmultiinput/index';
        if ($this->softLayout) {
            $view = 'hardmultiinput/soft';
        }
        return $this->render($view, [
            'model' => $this->model,
            'relationModelName' => Utils::basename($this->emptyRelationModel::className()),
            'relationRows' => $relationRows,
            'attribute' => $this->attribute,
            'fieldsNames' => $this->fieldsNames,
            'buttonAdd' => $this->buttonAdd,
            'buttonRemove' => $this->buttonRemove,
            'selectsData' => $this->selectsData,
            'selectsOptions' => $this->selectsOptions,
            'gridRealization' => $this->gridRealization,
            'addBtnAddClass' => $this->addBtnAddClass,
            'rmvBtnAddClass' => $this->rmvBtnAddClass,
        ]);
    }
}