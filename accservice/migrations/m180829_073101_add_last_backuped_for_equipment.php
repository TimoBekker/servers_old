<?php

use yii\db\Migration;

/**
 * Class m180829_073101_add_last_backuped_for_equipment
 */
class m180829_073101_add_last_backuped_for_equipment extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('r_equipment', 'last_backuped', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('r_equipment', 'last_backuped');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180829_073101_add_last_backuped_for_equipment cannot be reverted.\n";

        return false;
    }
    */
}
