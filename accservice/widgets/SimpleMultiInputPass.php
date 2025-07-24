<?php

namespace app\widgets;

use app\components\utils\Utils;
use app\widgets\SimpleMultiInput;
use yii\base\InvalidConfigException;
use yii\base\Widget;

final class SimpleMultiInputPass extends SimpleMultiInput
{
    const TYPE_PASSWORD = 'password';

    /* override */
    public function run()
    {
        $b = is_null($this->model) || is_null($this->attribute) || is_null($this->fieldsNames);
        if ($b) {
            throw new InvalidConfigException("Неверно заданы параметры виджета", 777002);
        }
        // $relationRows = $this->model->{$this->attribute};
        $relationRows = $this->model->getRPasswords()->andWhere(['finale' => 0, 'next' => null])->all();
        array_unshift($relationRows, $this->emptyRelationModel);
        return $this->render('simplemultiinputpass/index',[
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