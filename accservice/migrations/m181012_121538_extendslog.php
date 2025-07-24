<?php

use yii\db\Migration;

/**
 * Class m181012_121538_extendslog
 */
class m181012_121538_extendslog extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('r_log', 'object_before', $this->text()->null());
        $this->addColumn('r_log', 'object_after', $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('r_log', 'object_before');
        $this->dropColumn('r_log', 'object_after');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181012_121538_extendslog cannot be reverted.\n";

        return false;
    }
    */
}
