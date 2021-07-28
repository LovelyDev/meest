<?php

use App\RoomType;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csv_dir = base_path().'/database/seeds/csv/';
        $csv = Reader::createFromPath($csv_dir . 'room-types.csv', 'r');

        // Remove header row.
        $csv->setHeaderOffset(0);
        foreach ($csv->getRecords() as $record) {
            RoomType::create([
                'name' => $record['name'],
                'description' => $record['description'],
            ]);
        }
    }
}
