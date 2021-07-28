<?php

use App\Alias;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class AliasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csv_dir = base_path().'/database/seeds/csv/';
        $csv = Reader::createFromPath($csv_dir . 'alias.csv', 'r');

        // Remove header row.
        $csv->setHeaderOffset(0);
        foreach ($csv->getRecords() as $record) {
            Alias::create([
                'alias' => $record['alias'],
                'slug' => $record['slug'],
                'gender' => $record['gender'],
                'role_id' => $record['role_id'],
                'user_id' => $record['user_id'],
                'hours' => $record['hours'],
            ]);
        }
    }
}
