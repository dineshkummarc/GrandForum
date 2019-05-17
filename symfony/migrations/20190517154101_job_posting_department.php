<?php

use Phinx\Migration\AbstractMigration;

class JobPostingDepartment extends AbstractMigration
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
        $table = $this->table('grand_job_postings', array('id' => 'id'));
        $table->addColumn('project_id', 'integer', array('after' => 'user_id'))
              ->addIndex('project_id')
              ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {

    }
}
