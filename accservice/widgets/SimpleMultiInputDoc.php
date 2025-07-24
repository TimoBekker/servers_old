<?php

namespace app\widgets;

use app\components\utils\Utils;
use app\widgets\SimpleMultiInput;
use yii\base\InvalidConfigException;
use yii\base\Widget;

final class SimpleMultiInputDoc extends SimpleMultiInput
{
    const TYPE_HIDDEN = 'hidden';

    /* override */
    public function run()
    {
        $b = is_null($this->model) || is_null($this->attribute) || is_null($this->fieldsNames);
        if ($b) {
            throw new InvalidConfigException("Неверно заданы параметры виджета", 777002);
        }
        $relationRows = $this->model->{$this->attribute};
        array_unshift($relationRows, $this->emptyRelationModel);
        return $this->render('simplemultiinputdoc/index',[
            'model' => $this->model,
            'relationModelName' => Utils::basename($this->emptyRelationModel::className()),
            'relationRows' => $relationRows,
            'attribute' => $this->attribute,
            'fieldsNames' => $this->fieldsNames,
            'buttonAdd' => $this->buttonAdd,
            'buttonRemove' => $this->buttonRemove,
            'selectsData' => $this->selectsData,
            'selectsOptions' => $this->selectsOptions,
            'addBtnAddClass' => $this->addBtnAddClass,
            'rmvBtnAddClass' => $this->rmvBtnAddClass,
        ]);
    }
}