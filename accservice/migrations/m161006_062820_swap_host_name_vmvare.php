<?php

/*
    План миграции
    Переименовать host_name в vmware_name
    Пробежаться по оборудованию
    Для всего оборудования, где есть name и vmware_name меняем местами их
    Для оборудования, являющегося виртуальным копируем имя name в vmware_name
*/

use yii\db\Migration;

class m161006_062820_swap_host_name_vmvare extends Migration
{
    public function up()
    {
        Yii::$app->db->createCommand("
            ALTER TABLE .`r_equipment`
            CHANGE COLUMN `host_name` `vmware_name` VARCHAR(255) NOT NULL DEFAULT '' COMMENT '' ;
        ")->execute();
        Yii::$app->db->createCommand("
            UPDATE `r_equipment` old
              JOIN `r_equipment` new USING (id)
            SET    new.vmware_name = old.name,
                   new.name = old.vmware_name
            WHERE  old.name!='' and old.vmware_name!='';
        ")->execute();
        Yii::$app->db->createCommand("
            UPDATE `r_equipment`
            SET    vmware_name = name
            WHERE  vmware_name = '' and type = 1;
        ")->execute();
    }

    public function down()
    {
        echo "m161006_062820_swap_host_name_vmvare cannot be reverted.\n";
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
