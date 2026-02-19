<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%short_url_visit_log}}`.
 */
class m260218_000002_create_short_url_visit_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%short_url_visit_log}}', [
            'id' => $this->primaryKey(),
            'short_url_id' => $this->integer()->notNull(),
            'visitor_ip' => $this->string(45)->notNull(),
            'user_agent' => $this->string(1024)->null(),
            'visited_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-short_url_visit_log-short_url_id', '{{%short_url_visit_log}}', 'short_url_id');
        $this->addForeignKey(
            'fk-short_url_visit_log-short_url_id',
            '{{%short_url_visit_log}}',
            'short_url_id',
            '{{%short_url}}',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-short_url_visit_log-short_url_id', '{{%short_url_visit_log}}');
        $this->dropTable('{{%short_url_visit_log}}');
    }
}
