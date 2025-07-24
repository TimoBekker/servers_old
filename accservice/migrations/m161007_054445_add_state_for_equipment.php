<?php

use yii\db\Migration;

class m161007_054445_add_state_for_equipment extends Migration
{
    public function up()
    {
        $this->createTable('c_state_equipment', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
        ]);
        $this->insert('c_state_equipment', ['id'=>'1','name'=>'в работе']);
        $this->insert('c_state_equipment', ['id'=>'2','name'=>'выключено / не в работе']);
        $this->insert('c_state_equipment', ['id'=>'3','name'=>'выведено из эксплуатации']);

        $this->addColumn('r_equipment', 'state', $this->integer()->notNull()->defaultValue(1));
        $this->addForeignKey('fk-r_equipment-c_state_equipment', 'r_equipment', 'state', 'c_state_equipment', 'id', 'RESTRICT', 'RESTRICT');

    }

    public function down()
    {
        $this->dropColumn('r_equipment', 'state');
        $this->dropTable('c_state_equipment');
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
