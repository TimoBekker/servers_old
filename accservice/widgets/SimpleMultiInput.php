<?php

namespace app\widgets;

use app\components\utils\Utils;
use yii\base\InvalidConfigException;
use yii\base\Widget;

class SimpleMultiInput extends Widget
{
    const TYPE_INPUT = 'input';
    const TYPE_SELECT = 'select';

    public $model; // сюда придет модель
    /**
     * $fieldsNames
     В аттрибуте ожидаем либо строку в формате "поле.тип" Тип:(input,select)
     либо массив таких строк. По ним будем строить поля ввода.
     **/
    public $fieldsNames;
    public $emptyRelationModel;
    public $selectsData; // 'поле' => [массив данных для поля]
    public $selectsOptions = []; // 'поле' => [массив данных для поля]
    public $attribute; // сюда придет аттрибут.
    public $buttonAdd = 'Добавить';
    public $buttonRemove = 'Удалить';
    public $addBtnAddClass = 'add-btn';
    public $rmvBtnAddClass = 'rmv-btn';

    public $gridRealization = [];

    public $hddsLayout;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $b = is_null($this->model) || is_null($this->attribute) || is_null($this->fieldsNames);
        if ($b) {
            throw new InvalidConfigException("Неверно заданы параметры виджета", 777002);
        }
        $relationRows = $this->model->{$this->attribute};
        array_unshift($relationRows, $this->emptyRelationModel);
        $view = 'simplemultiinput/index';
        if ($this->hddsLayout) {
            $view = 'simplemultiinput/hdds';
        }
        return $this->render($view,[
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

    public static function genInputUID() {
        $inputId = uniqid("", true);
        return str_replace(".", "-", $inputId);
    }

    public static function genGrid(array $grid, $proportional = 0) {
        if ($proportional) {
            return floor(10 / $proportional);
        }
        static $ptr = 0;
        $countGrid = count($grid);
        $rIndex = $countGrid - 1;
        $res = $grid[$ptr];
        if ($ptr == $rIndex) {
            $ptr = 0;
        } else {
            $ptr ++;
        }
        return $res;
    }
}