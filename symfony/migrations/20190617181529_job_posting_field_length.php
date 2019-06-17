<?php

use Phinx\Migration\AbstractMigration;

class JobPostingFieldLength extends AbstractMigration
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
        $table = $this->table("grand_job_postings");
        $table->changeColumn('rank', 'string', array('limit' => 32))
              ->update();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}
