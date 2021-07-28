<?php

use App\User;
use App\Role;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $csv_dir = base_path() . '/database/seeds/csv/';
    $csv = Reader::createFromPath($csv_dir . 'users.csv', 'r');

    // Remove header row and build array of keyed values.
    $csv->setHeaderOffset(0);
    foreach ($csv->getRecords() as $record) {
      $user = User::create([
        'name' => $record['name'],
        'email' => $record['email'],
        'gender' => $record['gender'],
        'password' => Hash::make('p@ssw0rd@123'),
        'chat_interface' => '',
      ]);
    }
  }
}
