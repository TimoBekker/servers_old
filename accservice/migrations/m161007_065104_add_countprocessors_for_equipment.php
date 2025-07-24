<?php

use yii\db\Migration;

class m161007_065104_add_countprocessors_for_equipment extends Migration
{
    public function up()
    {
        $this->addColumn('r_equipment', 'count_processors', $this->integer()->notNull()->defaultValue(1));
    }

    public function down()
    {
        $this->dropColumn('r_equipment', 'count_processors');
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
