<?php

use yii\db\Migration;

class m161229_103641_ram_and_hdd_mul_1024 extends Migration
{
    public function up()
    {
        Yii::$app->db->createCommand("
            UPDATE `r_equipment` SET `amount_ram` = `amount_ram` * 1.024 WHERE (`amount_ram` % 1000) = 0;
        ")->execute();
        Yii::$app->db->createCommand("
            UPDATE `c_volume_hdd` SET `size` = `size` * 1.024 WHERE (`size` % 1000) = 0;
        ")->execute();
    }

    public function down()
    {
        echo "m161229_103641_ram_and_hdd_mul_1024 cannot be reverted.\n";

        return false;
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
