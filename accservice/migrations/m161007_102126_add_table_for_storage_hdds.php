<?php

/*
    План миграции:
    Создаем таблицу для хранения ПЗУ id, volume_hdd, equipment
    Переводим существующие значения из таблицы r_equipment в таблицу для хранения ПЗУ. Для этого
    копируем значения с указанием id оборудования.
    Для совместимости и возможности разработки пока не удаляем столбец volume_hdd. Возможно он так и
    останется в базе.
*/

use app\models\registry\REquipment;
use yii\db\Migration;

class m161007_102126_add_table_for_storage_hdds extends Migration
{
    public function up()
    {
        // создание таблицы
        $this->createTable('c_volume_hdd', [
            'id' => $this->primaryKey(),
            'is_dynamic' => $this->smallInteger(), // null - не определено, 0 - статический, <> 0 - динамический
            'size' => $this->integer(), // следом переопределим как БИГ ИНТ NULL UNSIGNED 20
            'equipment' => $this->integer(), // надо будет сделать инт 10 unsigned
        ]);
        $this->addCommentOnColumn('c_volume_hdd', 'is_dynamic', 'null - не определено, 0 - статический, <> 0 - динамический');
        // переопределение поля
        Yii::$app->db->createCommand("
ALTER TABLE `c_volume_hdd`
CHANGE COLUMN `size` `size` BIGINT(20) UNSIGNED NOT NULL COMMENT '1 условная единица = 1 Мб. Число. Положительное, до 18446744073709551615' ,
CHANGE COLUMN `equipment` `equipment` INT(10) UNSIGNED NOT NULL;
        ")->execute();
        // внешний ключ
        $this->addForeignKey('fk-c_volume_hdd-r_equipment', 'c_volume_hdd', 'equipment', 'r_equipment', 'id', 'CASCADE', 'RESTRICT');
        // заполнение значений
        Yii::$app->db->createCommand()->batchInsert('c_volume_hdd', ['size', 'equipment'],
            REquipment::find()->asArray()->select(['volume_hdd','id'])->where('volume_hdd IS NOT NULL')->all()
        )->execute();
    }

    public function down()
    {
        $this->dropTable('c_volume_hdd');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
