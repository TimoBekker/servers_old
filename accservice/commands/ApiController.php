<?php

namespace app\commands;

use app\api\models\Extern;
use yii\console\Controller;
use yii\helpers\Json;

class ApiController extends Controller
{
    public function actionAddtoken($description)
    {
        if (!$description) {
            echo "php yii api/addtoken description (description is required!)", PHP_EOL;
            return;
        }
        $extSystem = new Extern(['description' => $description]);
        $extSystem->regenerateToken();
        if ($extSystem->save()) {
            echo "your token: {$extSystem->token}", PHP_EOL;
        }
    }
    public function actionShowtokens()
    {
        $extSystems = Extern::find()->all();
        foreach ($extSystems as $item) {
            echo "{$item->id} {$item->token} description: {$item->description}", PHP_EOL;
        }
    }
    public function actionRemovetoken(int $id)
    {
        $extSystem = Extern::findOne($id);
        if ($extSystem) {
            $extSystem->delete();
        }
    }
    public function actionRebuildtoken(int $id)
    {
        $extSystem = Extern::findOne($id);
        if ($extSystem) {
            $extSystem->regenerateToken();
            if ($extSystem->save()) {
                echo "your token: {$extSystem->token}", PHP_EOL;
            }
        }
    }
}