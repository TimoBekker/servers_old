<?php

use yii\db\Migration;

class m161117_060703_add_reserval_copying extends Migration
{
    public function up()
    {
        $this->addColumn('r_equipment', 'backuping', $this->smallInteger(1)->defaultValue(0));
    }

    public function down()
    {
        $this->dropColumn('r_equipment', 'backuping');
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
