<?php

use Phinx\Migration\AbstractMigration;

class ProductsReportedPerson extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('grand_products_reported');
        $table->addColumn('user_id', 'integer', array('after' => 'product_id'))
              ->removeColumn('reported_type')
              ->removeColumn('data')
              ->addIndex('user_id')
              ->addIndex('year')
              ->update();
    }
}