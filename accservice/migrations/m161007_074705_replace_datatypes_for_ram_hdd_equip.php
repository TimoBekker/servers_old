<?php

/*
    План миграции
    Переопределяем тип хранения ОЗУ в сторону увеличения
    Переопределяем тип (времменного) хранения ПЗУ в сторону увеличения
    Переводим ОЗУ в Мегабайт на единицу
    Временно переводим в том же столбце ПЗУ в Мегабайт на единицу.
*/

use yii\db\Migration;

class m161007_074705_replace_datatypes_for_ram_hdd_equip extends Migration
{
    public function up()
    {
        Yii::$app->db->createCommand("
ALTER TABLE `r_equipment`
CHANGE COLUMN `amount_ram` `amount_ram` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT '1 условная единица = 1 Мб. Число. Положительное, до 4294967295' ,
CHANGE COLUMN `volume_hdd` `volume_hdd` BIGINT(20) UNSIGNED NULL DEFAULT NULL COMMENT '1 условная единица = 1 Мб. Число. Положительное, до 18446744073709551615' ;
        ")->execute();
        Yii::$app->db->createCommand("
            UPDATE `r_equipment` SET `amount_ram` = `amount_ram` * 100 WHERE id > 0;
        ")->execute();
        Yii::$app->db->createCommand("
            UPDATE `r_equipment` SET `volume_hdd` = `volume_hdd` * 5000 WHERE id > 0;
        ")->execute();
    }

    public function down()
    {
        Yii::$app->db->createCommand("
            UPDATE `r_equipment` SET `amount_ram` = `amount_ram` / 100 WHERE id > 0;
        ")->execute();
        Yii::$app->db->createCommand("
            UPDATE `r_equipment` SET `volume_hdd` = `volume_hdd` / 5000 WHERE id > 0;
        ")->execute();
        Yii::$app->db->createCommand("
ALTER TABLE `accservice_schema`.`r_equipment`
CHANGE COLUMN `amount_ram` `amount_ram` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL COMMENT '1 условная единица = 100 Мб. Число. Положительное, до 16777215' ,
CHANGE COLUMN `volume_hdd` `volume_hdd` MEDIUMINT(8) UNSIGNED NULL DEFAULT NULL COMMENT '1 условная единица = 5 Гб. Число. Положительное, до 16777215' ;
        ")->execute();
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
