<?php
use Migrations\AbstractMigration;

class AddCompletedDateToMissions extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('missions');
        $table->addColumn('completed_date', 'datetime', [
            'default' => null,
            'null' => true,
        ]);
        $table->update();
    }
}
