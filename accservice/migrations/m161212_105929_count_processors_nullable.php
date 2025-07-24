<?php

use yii\db\Migration;

class m161212_105929_count_processors_nullable extends Migration
{
    public function up()
    {
        Yii::$app->db->createCommand("
            ALTER TABLE `accservice_schema`.`r_equipment`
            CHANGE COLUMN `count_processors` `count_processors` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT '' ;
        ")->execute();
    }

    public function down()
    {
        echo "m161212_105929_count_processors_nullable cannot be reverted.\n";

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
