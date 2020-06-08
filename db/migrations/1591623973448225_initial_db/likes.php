<?php

use Phalcon\Db\Column;
use Phalcon\Db\Index;
use Phalcon\Db\Reference;
use Phalcon\Migrations\Mvc\Model\Migration;

/**
 * Class LikesMigration_1591623973448225
 */
class LikesMigration_1591623973448225 extends Migration
{
    /**
     * Define the table structure
     *
     * @return void
     */
    public function morph()
    {
        $this->morphTable('likes', [
                'columns' => [
                    new Column(
                        'postId',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'size' => 11,
                            'first' => true
                        ]
                    ),
                    new Column(
                        'userId',
                        [
                            'type' => Column::TYPE_INTEGER,
                            'notNull' => true,
                            'size' => 11,
                            'after' => 'postId'
                        ]
                    ),
                    new Column(
                        'created',
                        [
                            'type' => Column::TYPE_TIMESTAMP,
                            'default' => "CURRENT_TIMESTAMP",
                            'notNull' => true,
                            'after' => 'userId'
                        ]
                    ),
                    new Column(
                        'deleted',
                        [
                            'type' => Column::TYPE_TINYINTEGER,
                            'default' => "0",
                            'notNull' => true,
                            'size' => 1,
                            'after' => 'created'
                        ]
                    )
                ],
                'indexes' => [
                    new Index('PRIMARY', ['postId', 'userId'], 'PRIMARY'),
                    new Index('user', ['userId'], '')
                ],
                'references' => [
                    new Reference(
                        'post',
                        [
                            'referencedSchema' => 'mnmlsm',
                            'referencedTable' => 'posts',
                            'columns' => ['postId'],
                            'referencedColumns' => ['postId'],
                            'onUpdate' => 'NO ACTION',
                            'onDelete' => 'NO ACTION'
                        ]
                    ),
                    new Reference(
                        'user',
                        [
                            'referencedSchema' => 'mnmlsm',
                            'referencedTable' => 'users',
                            'columns' => ['userId'],
                            'referencedColumns' => ['userId'],
                            'onUpdate' => 'NO ACTION',
                            'onDelete' => 'NO ACTION'
                        ]
                    )
                ],
                'options' => [
                    'table_type' => 'BASE TABLE',
                    'auto_increment' => '',
                    'engine' => 'InnoDB',
                    'table_collation' => 'utf8_general_ci'
                ],
            ]
        );
    }

    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {

    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {

    }

}
