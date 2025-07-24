<?php

use yii\db\Migration;

class m160920_105524_add_hostname_field_from_equipment extends Migration
{
    public function up()
    {
        $this->addColumn('r_equipment', 'host_name', $this->string(255)->notNull()->defaultValue(''));
    }

    public function down()
    {
        $this->dropColumn('r_equipment', 'host_name');
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
