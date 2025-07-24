<?php

use yii\db\Migration;

class m161007_052252_add_model_for_equipment extends Migration
{
    public function up()
    {
        $this->addColumn('r_equipment', 'model', $this->string(255)->notNull()->defaultValue(''));
    }

    public function down()
    {
        $this->dropColumn('r_equipment', 'model');
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
