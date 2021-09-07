<?php

use Phinx\Migration\AbstractMigration;

class PaymentsPermissions extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $data = [
            [
                'roleId' => 2,
                'module' => 'payments',
                'privilege' => 'Management'
            ],
            [
                'roleId' => 2,
                'module' => 'payments',
                'privilege' => 'Info'
            ],
            [
                'roleId' => 2,
                'module' => 'payments',
                'privilege' => 'Pay'
            ],
            [
                'roleId' => 3,
                'module' => 'payments',
                'privilege' => 'Info'
            ],
            [
                'roleId' => 3,
                'module' => 'payments',
                'privilege' => 'Pay'
            ],
        ];

        $privileges = $this->table('acl_privileges');
        $privileges->insert($data)
            ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('DELETE FROM acl_privileges WHERE module = "payments"');
    }
}
