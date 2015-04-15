<?php
namespace bupy7\gallery\manager\migrations;

use yii\db\Schema;
use yii\db\Migration;

class m140930_003227_gallery_manager extends Migration
{
    
    /**
     * @var string Name of table gallery extension.
     */
    public $tableName = '{{%gallery_image}}';

    public function up()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => Schema::TYPE_PK,
                'type' => Schema::TYPE_STRING,
                'owner_id' => Schema::TYPE_STRING . ' NOT NULL',
                'rank' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
                'name' => Schema::TYPE_STRING,
                'description' => Schema::TYPE_TEXT
            ]
        );
    }

    public function down()
    {
        $this->dropTable($this->tableName);
    }
}
