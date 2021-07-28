<?php

use App\AvatarDefault;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class AvatarDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csv_dir = base_path().'/database/seeds/csv/';
        $csv = Reader::createFromPath($csv_dir . 'avatar-default.csv', 'r');

        // Remove header row.
        $csv->setHeaderOffset(0);
        foreach ($csv->getRecords() as $record) {
            AvatarDefault::create([
                'gender' => $record['gender'],
                'bodies' => $record['bodies'],
                'hair' => $record['hair'],
                'faces' => $record['faces'],
                'pants' => $record['pants'],
                'shirts' => $record['shirts'],
                'coats' => $record['coats'],
                'shoes' => $record['shoes'],
                'head_accessories' => $record['head_accessories'],
                'accessories' => $record['accessories'],
                'specials' => $record['specials'],
                'user_id' => 1,
            ]);
        }
    }
}
