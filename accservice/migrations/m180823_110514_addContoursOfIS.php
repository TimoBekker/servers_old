<?php

use yii\db\Migration;

/**
 * Class m180823_110514_addContoursOfIS
 */
class m180823_110514_addContoursOfIS extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // справочник контуров ис
        $this->createTable('s_contour_information_system', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
        ]);
        // добавляем таблицу связи
        $this->createTable('nn_equipment_infosys_contour', [
            'equipment' => $this->integer(10)->notNull()->unsigned(),
            'information_system' => $this->integer(10)->notNull()->unsigned(),
            'contour' => $this->integer()->notNull(),
        ]);
        // добавляем составной первичный ключ
        $this->addPrimaryKey("pk-nn_eq_in_co", 'nn_equipment_infosys_contour', ["equipment", "information_system", "contour"]);
        // add foreign key
        $this->addForeignKey(
            'fk-nn_eq_in_co-r_equipment',
            'nn_equipment_infosys_contour',
            'equipment',
            'r_equipment',
            'id',
            'CASCADE',
            'RESTRICT'
        );
        // add foreign key
        $this->addForeignKey(
            'fk-nn_eq_in_co-r_information_system',
            'nn_equipment_infosys_contour',
            'information_system',
            'r_information_system',
            'id',
            'CASCADE',
            'RESTRICT'
        );
        // add foreign key
        $this->addForeignKey(
            'fk-nn_eq_in_co-s_contour_information_system',
            'nn_equipment_infosys_contour',
            'contour',
            's_contour_information_system',
            'id',
            'CASCADE',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180823_110514_addContoursOfIS cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180823_110514_addContoursOfIS cannot be reverted.\n";

        return false;
    }
    */
}
