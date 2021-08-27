<?php

use Phinx\Seed\AbstractSeed;

class PaymentsSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data = [
            [
                'userId' => 2,
                'namespace' => 'payments',
                'key' => 'price',
                'value' => 's:2:"10";',
                'description' => 'Price of the one piece of happiness'
            ]
        ];
        $this->insert('options', $data);
    }
}
