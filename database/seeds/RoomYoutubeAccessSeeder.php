<?php

use App\RoomYoutubeAccess;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class RoomYoutubeAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csv_dir = base_path().'/database/seeds/csv/';
        $csv = Reader::createFromPath($csv_dir . 'room-youtube-accesses.csv', 'r');

        // Remove header row.
        $csv->setHeaderOffset(0);
        foreach ($csv->getRecords() as $record) {
            RoomYoutubeAccess::create([
                'name' => $record['name'],
                'description' => $record['description'],
            ]);
        }
    }
}
