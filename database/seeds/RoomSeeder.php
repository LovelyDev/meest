<?php

use App\Room;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csv_dir = base_path().'/database/seeds/csv/';
        $csv = Reader::createFromPath($csv_dir . 'rooms.csv', 'r');

        // Remove header row.
        $csv->setHeaderOffset(0);
        foreach ($csv->getRecords() as $record) {
            Room::create([
                'room_youtube_access_id' => $record['room_youtube_access_id'],
                'room_access_id' => $record['room_access_id'],
                'room_type_id' => $record['room_type_id'],
                'title' => $record['title'],
                'description' => $record['description'],
                'limit' => $record['limit'],
                'language' => $record['language'],
                'image_path' => $record['image_path'],
                'user_id' => 1,
            ]);
        }
    }
}
