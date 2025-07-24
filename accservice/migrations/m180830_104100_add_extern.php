<?php

use yii\db\Migration;

/**
 * Class m180830_104100_add_extern
 */
class m180830_104100_add_extern extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('extern', [
            'id' => $this->primaryKey(),
            'token' => $this->string()->notNull()->unique(),
            'description' => $this->string()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('extern');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180830_104100_add_extern cannot be reverted.\n";

        return false;
    }
    */
}
