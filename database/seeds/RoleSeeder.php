<?php

use App\Role;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $csv_dir = base_path().'/database/seeds/csv/';
        $csv = Reader::createFromPath($csv_dir . 'roles.csv', 'r');

        // Remove header row.
        $csv->setHeaderOffset(0);
        foreach ($csv->getRecords() as $record) {
            Role::create([
                'name' => $record['name'],
                'male_icon_path' => $record['male_icon_path'],
                'female_icon_path' => $record['female_icon_path'],
                'room_limit' => $record['room_limit'],
                'name' => $record['name'],
                'title' => $record['title'],
                'description' => $record['description'],
                'user_id' => 1,
            ]);
        }
    }
}
