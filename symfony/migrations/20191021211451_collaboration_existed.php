<?php

use Phinx\Migration\AbstractMigration;

class CollaborationExisted extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('grand_collaborations');
        $table->addColumn('existed', 'string', array('after' => 'funding', 'limit' => '16'))
              ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}
