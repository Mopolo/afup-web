<?php

use Phinx\Seed\AbstractSeed;

class Tarif extends AbstractSeed
{
    public function run()
    {
        $data = [
            [
                'id' => 1,
                'technical_name' => 'AFUP_FORUM_DEUXIEME_JOURNEE',
                'pretty_name' => 'Deuxième journée',
                'public' => true,
                'members_only' => false,
                'default_price' => 150,
                'active' => true,
                'day' => 'two',
                'cfp_submitter_only' => false,
            ],
            [
                'id' => 2,
                'technical_name' => 'AFUP_FORUM_2_JOURNEES',
                'pretty_name' => '2 Jours',
                'public' => true,
                'members_only' => false,
                'default_price' => 250,
                'active' => true,
                'day' => 'one,two',
                'cfp_submitter_only' => false,
            ],
            [
                'id' => 3,
                'technical_name' => 'AFUP_FORUM_2_JOURNEES_AFUP',
                'pretty_name' => '2 Jours AFUP',
                'public' => true,
                'members_only' => true,
                'default_price' => 150,
                'active' => true,
                'day' => 'one,two',
                'cfp_submitter_only' => false,
            ],
        ];

        $table = $this->table('afup_forum_tarif');
        $table->truncate();

        $table
            ->insert($data)
            ->save()
        ;
    }
}