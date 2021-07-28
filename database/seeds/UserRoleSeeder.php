<?php

use App\User;
use App\Role;
use Illuminate\Database\Seeder;
use League\Csv\Reader;

class UserRoleSeeder extends Seeder
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
      $user = User::where('email', $record['email'])->first();

      $userRole = Role::find((int) $record['role']);
      $user->roles()->attach($userRole);
    }
  }
}
